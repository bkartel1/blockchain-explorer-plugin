<?php
/**
* Plugin Name: Bitcoin Block Explorer
* Description: Display a bitcoin block explorer with PDF export capabilities
* Version: 1.0
* Author: Richard Hogan
* Author URI: https://www.twitter.com/irnagoh
* License: GNU General Public License v2 or later
* License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/


// Block explorer
function searchingBlocks() {
	?>
	<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.4/angular.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.22/pdfmake.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
	<script>
	var app = angular.module('exportPdf', []);
	app.controller('exportCont', function($scope) {
	// Function to export PDFs
		$scope.export = function(){
      			html2canvas(document.getElementById('resultsTable'), {
            			onrendered: function (canvas) {
                		var data = canvas.toDataURL();
                		var docDefinition = {
                    			content: [{
                        			image: data,
                        			width: 400,
                    			}]
                		};
                		pdfMake.createPdf(docDefinition).download("coinblocks.pdf");
            			}
        		});
    		}
	});
	</script>	

	<style>
	    #explorerHoldings{
	        padding:50px 0px;
	        text-align:center;
	    }
	    #addressBox{
	        border-radius:2px;
	        border:1px solid lightgrey;
	        width:500px;
	        padding:10px 20px;
	        font-family:"Poppins", sans-serif;
	    }
	    #submitExplorer{
	        width:500px;
	        border-radius:2px;
	        margin:5px 0px;
	        transition: opacity .5s ease;   
	        opacity:.7;
	    }
	    #submitExplorer:hover{
	        opacity:1;
	    }
		#displayResultsHolder{
			width: 800px;
			margin-left: auto;
			margin-right: auto;
		}
		#resultsTable{
			width: 800px;
			margin-left: auto;
			margin-right: auto;
		}
		#resultsTable, td{
			border: 1px solid black;
			padding: 5px;
			color: black;
			margin-left:auto;
			margin-right:auto;
		}
		#displayResultsHolder button{
			width:100%;
			margin:10px 0px;
		}
	</style>

	<form id="explorerHoldings" method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<input id="addressBox" name="addressBox" placeholder="Transaction id, wallet address, block hash" />
	<br>
	<button id="submitExplorer" type="submit">Search</button>
	</form>
	
	<?php
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		// Get search item
		$searchItem = $_POST['addressBox'];
		
		?>
		<div id='displayResultsHolder' ng-app='exportPdf' ng-controller='exportCont'>
		<?php
		if (strlen($searchItem)>=26 && strlen($searchItem)<=36){
			// Get wallet
			$url = "https://chain.api.btc.com/v3/address/".$searchItem;
			$apiResponse = file_get_contents($url);
			$jsonResponse = json_decode($apiResponse, true);
			$jsonResponse = $jsonResponse['data'];
			echo "<table id='resultsTable'>";
			foreach($jsonResponse as $key => $value){
				echo "<tr><td>".strtoupper($key) ."</td><td>".$value."</td>";
			}
			echo "</table>";
		} elseif (strlen($searchItem)>36){
			// Get tx
			$url = "https://chain.api.btc.com/v3/tx/".$searchItem; 
			$apiResponse = file_get_contents($url);
			$jsonResponse = json_decode($apiResponse, true);
			$jsonResponse = $jsonResponse['data'];
			echo "<table id='resultsTable'>";
			foreach($jsonResponse as $key => $value){
				echo "<tr><td>".strtoupper($key) ."</td><td>".$value."</td>";
			}
			echo "</table>";	
		} else {
			// Get block
			$url = "https://chain.api.btc.com/v3/block/".$searchItem;
			$apiResponse = file_get_contents($url);
			$jsonResponse = json_decode($apiResponse, true);
			$jsonResponse = $jsonResponse['data'];
			echo "<table id='resultsTable'>";
			foreach($jsonResponse as $key => $value){
				echo "<tr><td>".strtoupper($key) ."</td><td>".$value."</td>";
			}
			echo "</table>";
		}
		?>
			<button ng-click="export()">Generate PDF</button>
		</div>
		<?php
	}
}

add_shortcode('searchingTheBlocks', 'searchingBlocks');