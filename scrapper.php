<?php
session_start();

$url = '';
$doi_number = '';
if(isset($_POST['url'])){
	$url = $_POST['url'];
}elseif(isset($_SESSION['url'])) {
	$url = $_SESSION['url'];
}else{
	$url = "Start a new search";
}

include_once('simple_html_dom.php');
$handle = @fopen($url,'r');
if ($url == "Start a new search"){
	$table = 'Start a new search by entering a journal url on the top.';
}elseif(!$handle) {
	$table = 'Sorry, your url does not exist.';
}else{
	$html= file_get_html($url)->plaintext;
	//echo $html;
	$reg = "/([A-Za-z]+[^\[\"\'\,.:0-9\s\(\)\?\*\/\>\<|&+-;])/";
	$doi_reg = "/(DOI|doi):( )*[a-z0-9-.\/]*/";
	
	preg_match_all($reg,$html,$matches);
	preg_match_all($doi_reg,$html,$doi_matches);

	$doi_number = trim(substr($doi_matches[0][0], 4));
	// echo ($doi_number);
	function sort_using_value($a, $b){
		if ($a == $b){
			return 0;
		}
		return ($a < $b) ? 1 : -1;
	}

	$frequency_table = [];

	foreach ($matches[0] as $element) {
		if ($element != "nbsp"){
			# code...
			$element = strtolower($element);
			if (!array_key_exists($element, $frequency_table)){
				$frequency_table[$element] = 1;
			}else{
				$frequency_table[$element] += 1;
			}
		}
	}

	uasort($frequency_table, "sort_using_value");

	$table = "<table>";
	$table .="<tr class='highlight'>";
	$table .="<td>No.</td>";
	$table .="<td>Word</td>";
	$table .="<td>Frequency</td>";
	$table .="</tr>";

	$index = 1;

	$barchart_table = "[['Word', 'Frequency'],";

	foreach($frequency_table as $words => $frequency){
		if($index <= 10){
			if($index %2 == 0){
				$table .="<tr class='highlight'>";
			}else{
				$table .="<tr>";
			}
			$table .="<td>" . $index . "</td>";
			$table .= "<td>" . $words . "</td>";
			$table .="<td>" . $frequency ."</td>";
			$table .="</tr>";
			$index += 1;
			$barchart_table .= "['" . $words . "', " . $frequency . "],";
		}else{
			break;
		}	
	}
	$table .="</table>";
	$barchart_table .= "]"
;}

?>

<html>
  <head>
   	<link href="style.css" rel="stylesheet" type="text/css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script> 
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable(<?= $barchart_table ?>);

        var options = {
          title: 'Word Frequency Table',
          hAxis: {title: 'Word'}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
  	<div class="wrapper">
	  	<div class="search_form">
	  		<form class="search" method="post" action="scrapper.php">
	  			<input class="search_bar" name="url" value = '<?=$url?>' > </input>
	  			<button class="submit_button" type="submit">Try my luck today!</button>
	  		</form>
	  	</div>
	  </div>
  	<div class="wrapper">
  		<div class="important_info"> DOI: <?=$doi_number?></div>
	  	<div id ="chart_table"><?=$table?></div>
	    <div id="chart_div" style="width: 1000px; height: 500px;"></div>
	</div>
  </body>
  <script>

	$('.search_bar').change(function(){
		String.prototype.startsWith = function(str) {
	    return (this.length >= str.length)
	        && (this.substr(0, str.length) == str);
		}

		String.prototype.startsWith_nc = function(str) {
		    return this.toLowerCase().startsWith(str.toLowerCase());
		}

		var text = $('.search_bar').val();
		if (!text.startsWith_nc("http://")) {
			new_text = "http://" + text;
			$(".search_bar").val(new_text);
		}
	})
  </script>
</html>