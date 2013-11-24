<?php
include_once('simple_html_dom.php');

$html= file_get_html('http://europepmc.org/abstract/MED/12503002')->plaintext;

$reg = "/([A-Za-z]+[^\[\"\'\,.:0-9\s\(\)\?\*\/|&+-;])/";
preg_match_all($reg,$html,$matches);

function sort_using_value($a, $b){
	if ($a == $b){
		return 0;
	}
	return ($a < $b) ? 1 : -1;
}

$frequency_table = [];

foreach ($matches[0] as $element) {
	# code...
	$element = strtolower($element);
	if (!array_key_exists($element, $frequency_table)){
		$frequency_table[$element] = 1;
	}else{
		$frequency_table[$element] += 1;
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
$barchart_table .= "]";
//echo $barchart_table;
//var_dump($frequency_table);
?>

<html>
  <head>
   	<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=shadow-multiple">
   	<link href="style.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable(<?= $barchart_table ?>);

        var options = {
          title: 'Word Frequency Table',
          hAxis: {title: 'Word', titleTextStyle: {color: 'red'}}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
  	<div class="search_form">
  		<form>
  		</form>
  	</div>
  	<div class="wrapper">
	  	<div id ="chart_table"><?=$table?></div>
	    <div id="chart_div" style="width: 1000px; height: 500px;"></div>
	</div>
  </body>
</html>