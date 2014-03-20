<?php

$postData = array(
   'fields' => array("city","element_name"),
   'hashes' => array("00018f0fbfac3458a04dacb330d17e8a","002b1e6195f41bf4efb3d7584c88fc56")
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
$response = file_get_contents("http://localhost:8010/elements/metadata", FALSE, $context);

var_dump($response);

?>
