<?
   $data = json_decode(file_get_contents("truck.json"));
   
   $nova = array();
   foreach ($data->elements as $element)
   {
      $nelement= new StdClass;
      $nelement->name=$element->name;
      $nelement->href=$element->url;
      $nova[]=$nelement;
   }
   echo json_encode($nova);
?>
