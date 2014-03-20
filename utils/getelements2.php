<?php

$postData = array(
   'search-conditions' => array("state"=>"Slovensko", "city"=>"Senec", "building"=>"SO 02")
);

// Create the context for the request
$context = stream_context_create(array(
   'http' => array(
      // http://www.php.net/manual/en/context.http.php
      'method' => 'POST',
//      'header' => "Authorization: {$authToken}\r\n".
      "Content-Type: application/json\r\n",
      'content' => json_encode($postData)
   )
));

// Send the request
$response = file_get_contents("https://edocu.eu/api2/graph/Hajek/elements/search-with-relationship-in-location?h=Test", FALSE, $context);

var_dump($response);

?>
