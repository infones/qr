<?php

isset($_REQUEST["source"]) or die("source parameter missing");
$dataSource=$_REQUEST["source"];
$data = json_decode(file_get_contents($dataSource));

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=export.csv");
header("Pragma: no-cache");
header("Expires: 0");

$out = fopen('php://output', 'w');

$i=1;
foreach ($data as $row)
{
   printf("%d;", $i++);
   fputcsv($out, array($row));
  
}
fclose($out);

