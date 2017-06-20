<?php
    
	//Regular expression function that scans individual pages for emails
    function get_emails_from_webpage($url)
    {
      $text=file_get_contents($url);
      $res = preg_match_all("/[a-z0-9]+[_a-z0-9\.-]*[a-z0-9]+@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})/i",$text,$matches);
      if ($res) {
          return array_unique($matches[0]);
      }
      else{
          return null;
      }
    }

//URL Array
$URLArray = array();
   
//Inputted URL right now it just pulls it from a GET variable but you can do alter this any way you want
$inputtedURL = $_GET['url'];


//Crawling the inputted domain to get the URLS
$urlContent = file_get_contents("http://".urldecode($inputtedURL));
$dom = new DOMDocument();
@$dom->loadHTML($urlContent);
$xpath = new DOMXPath($dom);
$hrefs = $xpath->evaluate("/html/body//a");

$scrapedEmails = array();

for($i = 0; $i < $hrefs->length; $i++){
    $href = $hrefs->item($i);
    $url = $href->getAttribute('href');
    $url = filter_var($url, FILTER_SANITIZE_URL);
	//array_push($scrapedEmails, $hrefs->length);
    // validate url
    if(!filter_var($url, FILTER_VALIDATE_URL) === false){
		if (strpos($url, $inputtedURL) !== false) {
				array_push($URLArray, $url);
			}
        
    }
}

//Extracting the emails from URLS that were crawled
foreach ($URLArray as $key => $url) {
    $emails = get_emails_from_webpage($url);

    if($emails != null){
      foreach($emails as $email) {
          if(!in_array($email, $scrapedEmails)){
        	array_push($scrapedEmails,$email);
        }
      }
    } 
}


//Ouputting the scraped emails in addition to the the number of URLS crawled
foreach($scrapedEmails as $value) {
	echo $value . " " . count($URLArray);
}

?>