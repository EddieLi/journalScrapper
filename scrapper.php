<?php
/* This is the controller of the scrapper program. It uses the class
Scrapper to get words frequency table, and doi number, which then is
used by API class to obtain more information associated with the doi number.
*/
	require_once("class.scrapper.php");
	require_once("class.api.php");
	
	$url = '';
	$frequency_table = 'N/A';
	$word_frequency_table = 'N/A';
	$doi_number = 'N/A';
	$doi_information = '';
	$doi_url = '';
	$doi_score = 'N/A';
	$doi_normalized_score = 'N/A';
	$doi_title = 'N/A';
	$doi_fullCitation = 'N/A';
	$doi_year = 'N/A';

	//if url is posted on url use get
	if(isset($_GET['url'])){
		$url = $_GET['url'];
		if(substr($url, 0,7) != "http://"){
			$url = "http://" . $url;
		}

	//if url is posted from the search form, use post
	}elseif(isset($_POST['url'])) {
		$url = $_POST['url'];
	}else{
		$url = "";
	}


	if ($url != ""){
		//call and instantialized Scrapper Class.
		$Scrapper = new Scrapper($url);
	
		$url = $Scrapper->url;

		//Check if there is any error
		$error_msg = $Scrapper->error_msg;
		
		//If there is no error
		if(!$error_msg){

			//get word frequency table
			$frequency_table = $Scrapper->wordsFrequencyTable();
			$word_frequency_table = $Scrapper->printWordsFrequencyTable();
			
			//get barchar table
			$barchar_table = $Scrapper->printBarcharTable();
			
			//get doi number in order to request information from the API
			$doi_number = $Scrapper->getDOI();

			if ($doi_number != "N/A"){
				//Instantialize API with given doi number
				$API = new API($doi_number);

				$doi_score = $API->getScore();
				$doi_normalized_score = $API->getNormalizedScore();
				$doi_title = $API->getTitle();
				$doi_fullCitation = $API->getFullCitation();
				$doi_year = $API->getYear();
			}
			
		}

	}else{
		$error_msg = "Please enter a journal url to start.";
	}
	

?>	