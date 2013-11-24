<?php
	include_once('simple_html_dom.php');

	Class Scrapper{
		/*
		This is the Scrapper Class. It contains methods that will be used to scrap words and frequency of each word.
		it will also contain methods that will be used to find the DOI numbers. Meanwhile, regular expressions can be
		updated here as well. The frequency table can be printed using two different formats, one is table format, another
		one is in JSON format.

		This class contains the following methods:
		-	__construct($url)
		-	sortUsingValue($a, $b)
		- 	wordsFrequencyTable
		-	setDOIRegex
		-	getDOI
		- 	printWordsFrequencyTable (TABLE)
		-	printBarcharTable (JSON)
		*/

		public $url = '';
		public $error_msg = '';
		public $html = '';
		private $frequency_table = array();
		private $barchart_table = '';
		//regular expression for scrapping doi numbers
		private $doi_reg = "/(10[.][0-9]{4,}(?:[.][0-9]+)*\/(?:(?![&\'<>])\S)+)[0-9]+/";
		//regular expression for scrapping words and frequencies
		private $word_reg = "/([A-Za-z]+[^\[\"\'\,.:0-9\s\(\)\?\*\/\>\<|&+-;])/";

		function __construct($url){

			$this->url = $url;

			//open the url
			$handle = @fopen($url,'r');
			if(!$handle || file_get_html($this->url)==''){
				$this->error_msg = "Sorry, your url does not exist.";

			}else{
				$this->html= file_get_html($this->url)->plaintext;
			}
		}

		private static function sortUsingValue($a, $b){
		//This function is use to sort the frequency in descending order
			if ($a == $b){
				return 0;
			}
			return ($a < $b) ? 1 : -1;
		}

		public function wordsFrequencyTable(){
			
			preg_match_all($this->word_reg,$this->html,$matches);

			foreach ($matches[0] as $element) {
				if ($element != "nbsp"){
					//count the same word
					$element = strtolower($element);
					if (!array_key_exists($element, $this->frequency_table)){
						$this->frequency_table[$element] = 1;
					}else{
						$this->frequency_table[$element] += 1;
					}
				}
			}

			//sort the result in descending order base on the frequencies
			uasort($this->frequency_table, array("Scrapper", 'sortUsingValue'));

			return $this->frequency_table;
		}
		public function setDOIRegex($reg){
			//update doi regular expression if doi format changes

			$this->doi_reg = $reg;
		}

		public function getDOI(){
			//get doi number by applying the existing regular expression for doi numbers

			preg_match($this->doi_reg,$this->html,$doi_matches);

			if(count($doi_matches)>0){
				$doi_number = trim($doi_matches[0]);
			}else{
				$doi_number = 'N/A';
			}

			return $doi_number;
		}

		public function printWordsFrequencyTable(){
			//Display table

			$table = "<table>";
			$table .="<tr class='highlight'>";
			$table .="<td>No.</td>";
			$table .="<td>Word</td>";
			$table .="<td>Frequency</td>";
			$table .="</tr>";

			$index = 1;

			$barchart_table = "[['Word', 'Frequency'],";

			//count frequency for each word
			foreach($this->frequency_table as $words => $frequency){
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

			return $table;
		}

		public function printBarcharTable(){
			//print the table in JSON format

			$this->barchart_table = "[['Word', 'Frequency'],";

			$index = 1;

			foreach($this->frequency_table as $words => $frequency){
				if($index <= 10){
					$this->barchart_table .= "['" . $words . "', " . $frequency . "],";
					$index += 1;
				}else{
					break;
				}	
			}
			$this->barchart_table .= "]";

			return $this->barchart_table;
		}
	}

?>