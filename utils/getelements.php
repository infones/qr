<?php

header('Content-Type: application/json;charset=utf-8'); 
header("Pragma: no-cache");
header("Expires: 0");

$url = 'http://localhost:8010/elements/metadata';

$postData['fields'] = array("city", "element_name");
$postData['hashes'] = array("00018f0fbfac3458a04dacb330d17e8a","002b1e6195f41bf4efb3d7584c88fc56");

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

