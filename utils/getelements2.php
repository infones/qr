<?php

header('Content-Type: application/json;charset=utf-8');
header("Pragma: no-cache");
header("Expires: 0");

$url = 'https://edocu.eu/api2/graph/Hajek/elements/search-with-relationship-in-location?h=Test';

$postData['search-conditions'] = array("city" =>"Senec", "state"=>"Slovensko", "building"=>"SO 02");

$ch = curl_init();
$headers= array('Accept: application/json','Content-Type: application/json;charset=utf-8'); 
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
$result = curl_exec($ch);
curl_close($ch);

echo $result;

?>
