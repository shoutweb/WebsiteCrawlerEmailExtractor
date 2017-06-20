<?php
    
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
$URLArray = array();
   
$inputtedURL = $_GET['url'];
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

foreach($scrapedEmails as $value) {
	echo $value . " " . count($URLArray);
}

?>