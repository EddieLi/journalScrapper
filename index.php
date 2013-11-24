<?php
	session_start();
	require_once('scrapper.php');
?>


<html lang="en-US">
  <head>
  	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
   	<link href="style.css" rel="stylesheet" type="text/css">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script> 
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable(<?=$barchar_table; ?>);

        var options = {
          title: 'Word Frequency Table',
          hAxis: {title: 'Word'}
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div_wrapper'));
        chart.draw(data, options);
      }
    </script>
  </head>
  <body>
  	<div class="wrapper">
	  	<div class="search_form">
	  		<form class="search" method="POST" action=".">
	  			<input class="search_bar" name="url" value = '<?=$url?>' > </input>
	  			<button class="submit_button" type="submit">Try my luck today!</button>
	  		</form>
	  		<button class="examples">Examples</button>
	  		<div id="examples_wrapper">
		  		 <p><a href=".?url=www.jmir.org/2013/11/e218/">Journal of Medical Internet Research</a></p>
		  		<p><a href=".?url=europepmc.org/abstract/MED/12503002">Europe PubMed Central</a></p>
		  		<p><a href=".?url=www.cmaj.ca/content/185/17/1475.full">CMAJ</a></p>
		  	
		  	</div>
	  	</div>
	  </div>

  	<div class="wrapper">
  		<button class="important_info">DOI information</button>
	  		<div class="text-wrapper" id="important_info_wrapper"> 
	  			<h1><?=$error_msg?></h1>
	  			<h1>DOI: <?=$doi_number?></h1>
	  			<h2>Title: <?=$doi_title ?></h2>
	  			<p>Full citation: <?=$doi_fullCitation?></p>
	  			<table>
		  			<tr class='highlight'>
		  				<td>Score</td>
		  				<td>Normalized Score</td>
		  				<td>Year</td>
		  			</tr>
		  			<tr>
		  				<td><?=$doi_score?></td>
		  				<td><?=$doi_normalized_score?></td>
		  				<td><?=$doi_year?></td>
		  			</tr>
		  		</table>
	  		</div>
	  		
  		
  	</div>

  	<div class="wrapper">
  		<button class="chart_table">Word Frequency Table</button>
	  	<div id ="chart_table_wrapper"><?=$word_frequency_table?></div>
    </div>

    <div class="wrapper">
  		<button class="chart_div">Word Frequency Barchart</button>
	    <div id="chart_div_wrapper" style="width: 1000px; height: 500px;"></div>
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

	$('button:not(".submit_button")').click(function(){
		$('#' + $(this).attr('class') + "_wrapper").slideToggle('slow');
	})
  </script>
</html>