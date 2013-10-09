<?

class Program{

	 /*-------------------------
	  * BASIC USAGE:
	  * 
	  * init
	  * $program = new Program();
	  * $program->main(val);
	  * 
	  * val -can be altered to change
	  * output rowcount. Default value
	  * is 10
	  * 
	  * Working live demo at 
	  * http://vanhaviitakoski.com/dev/
	  * 
	  * author: Mika Vanhaviitakoski
	  * versio: 1 / 2013-10-08
	  * 
	  * ----------------------*/
	  
	var $id = 0;
	var $name = "";
	var $originalName = "";
	var $seriesTitle = "";
	var $description = "";
	var $startTime;
	var $endTime;

	var $seasonNo = 0;
	var $productionYear = 0;
	var $productionCountry = "";
	var $director = "";
	var $movie = false;
	var $sport = false;
	var $series = false;
	var $children = false;
	var $ageRating = "";
	 
	
	/*-----------------
	 * main program
	 * ---------------*/
	 




	 function main($limit=10) {
		 
			/*---------------------
			 * Get/Set content to
			 * dataSource-variable.
			 * Content type is array
			 * -----------------*/
			
			$dataSource = self::getFcontent('http://vanhaviitakoski.com/dev/viasat_film_action_fi_2013-14_tab.txt');


			/*---------------------
			 * If server sends 
			 * fail/wrong response header
			 * -------------------*/
			# print_r($dataSource);
			 
			 if($dataSource['http_code']!=200) { echo 'Error - Invalid url'; die; } 
			
			
			/*------------------------
			 * if dataSource[0] is NOT empty,
			 * start compiling
			 *------------------------*/
			 
			 if(!empty($dataSource['content'])) {
				 
					
					 /*----------------------
					  * Read content by lines
					  * to $row-var
					  *--------------------*/
					  $row = explode("\n", $dataSource['content']);
						
					  /*---------------------
					   * Set temp vars
					   * -------------------*/
					  $rowCount=0;
					  $HTML=array();
					  
					  /*---------------------
					   * Read lines
					   * -------------------*/
					  foreach($row as $key => $item) {
						  /*------------------------
						   * First row is namespace,
						   * skip it!
						   * ----------------------*/
						  if($rowCount>=1) {
							
						  $j = explode("\t", $item);
						   
						  /*-------------------
						   * set items to array
						   * 
						   * 
						   *    [0] => Date
								[1] => Start time
								[2] => Leadtext
								[3] => name
								[4] => org name
								[5] => episode nr
								[6] => Season number
								[7] => Widescreen (16:9)
								[8] => Duration (minutes)
								[9] => Parental rating
								[10] => B-line
								[11] => Category
								[12] => Country of Origin
								[13] => Production Year
								[14] => Genre
								[15] => Logline
								[16] => Synopsis this episode
								[17] => Guests this episode
								[18] => Synopsis
								[19] => Cast
								[20] => Director
								[21] => Guest
								[22] => Host
								[23] => Commentator
								[24] => Voice
								[25] => Rerun
								[26] => Extra information
								[27] => Unique ID
								[28] => Category Num
								[29] => Last episode this season
								[30] => HD
						 
						*/
						 
						 
						 
						
								/*----------------------
								* Map array items to
								* variables
								* --------------------*/
							
							 
								/*------------------------------
								* Set date value to
								* calculation - Not necessary, but
								* cleaner.
								* ----------------------------*/
								if(!empty($j[0])) { $setDate=trim($j[0]); }

							    
							  	$this->id = settype(trim($j[27]), "int");
								$this->name = trim($j[3]);
								$this->originalName = trim($j[4]);
								$this->seriesTitle = "";
								$this->description = trim($j[2]);
								
								/*-----------------------------
								 * construct time
								 * --------------------------*/
								
								$this->startTime = date("H:i", strtotime($setDate." ".trim($j[1])));
								/*------------------------------
								 * construct end time
								 * by creating temp var $time and
								 * add Duration (minutes) to it
								 * -----------------------------*/
								$time = strtotime($this->startTime);
								$this->endTime =date("H:i", strtotime('+'.trim($j[8]*1).' minutes', $time));

								$this->seasonNo = settype(trim($j[6]), "int");
								$this->productionYear = settype(trim($j[13]), "int");
								
								$this->productionCountry = trim($j[12]);
								$this->director = trim($j[20]);
								
								/*------------------------------
								 * Category values confirmed from source.
								 * Assumption: array item 11 is 
								 * Category and content is ELOKUVAT
								 * then item 28 equals 1 presuming
								 * 2 equals Sport, 3 equals Series
								 * and 4 equals Children
								 * -----------------------------*/
								(trim($j[28])=="1") ?  $this->movie = true : $this->movie = false;
								(trim($j[28])=="2") ?  $this->sport = true  :  $this->sport = false;
								(trim($j[28])=="3") ?  $this->series = true :  $this->series = false;
								(trim($j[28])=="4") ?  $this->children = true :  $this->children = true;
								$this->ageRating = trim($j[9]);
								
								$HTML[$rowCount] = "".$this->startTime." ".$this->name." ".$this->description."";
							 
							
							
						  }
						  
						  /*-----------------------
						   * Loop counter for line
						   * reading
						   * ----------------------*/
						   $rowCount++;
					  }
					
					
					/*----------------------
					* if rowcounter has added
					* some loops - the we have
					* content in array
					* ---------------------*/
					
					if($rowCount!=0) {
						/*------------------------
						 * Construct simple 
						 * html-output
						 *------------------------*/
						$OUT="<p>\n";
						for ($i = 1; $i <= $limit; $i++) {
								$OUT .=  $HTML[$i]."<br>\n";
						}
						$OUT .= '</p>';
						
						/*------------------------
						 * THIS IS IT 
						 *------------------------*/
						 
						echo  $OUT;
						
						
					} else {
						
							/*----------------------
							 * set fail note if
							 * no content is set att the 
							 * loop
							 * ---------------------*/
							
							echo 'Error - No parsing done';
						
					}
					
			 } else {
				
				/*----------------------
				 * set fail note if
				 * no content is parsed
				 * check getFcontent();
				 * ---------------------*/
				
				echo 'Error - No content to read'; 
				
					
			 }
			
				
				
		 
	}
	 
	 


			
			
			
function getFcontent( $url  ) {
			/*---------------------------------------
			 * get external content via
			 * curl -handler. more info 
			 * http://php.net/manual/en/book.curl.php
			 * --------------------------------------*/		
			 
		$user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

        $options = array(

            CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
            CURLOPT_POST           =>false,        //set to GET
            CURLOPT_USERAGENT      => $user_agent, //set user agent
            CURLOPT_RETURNTRANSFER => true,     	// return web page
            CURLOPT_HEADER         => false,    	// don't return headers
            CURLOPT_FOLLOWLOCATION => false,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 10,      // timeout on connect
            CURLOPT_TIMEOUT        => 10,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        
        return $header;
		
		}
	
}

?>
