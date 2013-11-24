<?php
	Class API{
		/*
		This is the API class. It is used to request data from crossref.org
		and to verify the given DOI number.

		It contains the following methods:
		-	__construct($doi_num)
		-	setDOINum
		-	getDOINum
		-	getScore
		-	getNormalizedScore
		-	getTitle
		-	getFullCitation
		-	getYear
		*/

		private $api_url = 'http://search.crossref.org/dois?q=';
		private $doi_num;
		private $api_data_object = '';

		function __construct($doi_num){

			//DOI api
			$this->doi_num = $doi_num;
			$api_request = $this->api_url . $this->doi_num;
			$doi_html = file_get_contents($api_request);

			//API returns a JSON file
			$doi_information = json_decode($doi_html);
			
			//Get the first element inside the JSON, which contains all the information we need
			$this->api_data_object = $doi_information[0];
		}

		public function setDOINum($doi_num){
			//update DOI number when necessary
			$this->doi_num = $doi_num;
		}

		public function getDOINum(){
			return $this->doi_num;
		}

		public function getScore(){
			return $this->api_data_object->score;
		}

		public function getNormalizedScore(){
			return $this->api_data_object->normalizedScore;
		}

		public function getTitle(){
			return $this->api_data_object->title;
		}

		public function getFullCitation(){
			return $this->api_data_object->fullCitation;
		}

		public function getYear(){
			return $this->api_data_object->year;
		}
	}

?>