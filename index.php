<?php    
// parameters:
//    data  - content 
//    level - error correction level ("L"ow,"M"edium,"Q"uarter,"H"igh)  
//    size  - pixels per point
//    border - in points
//    fileName - default name of the file
//    blackWhite - color set (0=color, 1=gray, 2=B&W)
//    format - output format - PNG, EPS, TEXT, RAW (PNG=default)
    
    include "qrlib.php";    

    $format= "PNG";
    if (isset($_REQUEST['format']))
        $format= strtoupper($_REQUEST['format']);

    $fileName="qrcode.png";
    if (isset($_REQUEST["fileName"]))
      //$fileName=$_REQUEST["fileName"];
      $fileName=str_replace(".","_",$_REQUEST["fileName"]);

    $blackWhite=0;
    if (isset($_REQUEST["blackWhite"]))
      $blackWhite=$_REQUEST["blackWhite"];

    $borderSize= 2;
    if (isset($_REQUEST['border']))
        $borderSize= $_REQUEST['border'];    

    $errorCorrectionLevel = 3;
    if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L','M','Q','H')))
        $errorCorrectionLevel = $_REQUEST['level'];    

    $matrixPointSize = 5;
    if (isset($_REQUEST['size']))
        $matrixPointSize = min(max((int)$_REQUEST['size'], 1), 10);


    if (isset($_REQUEST['data'])) { 
        // filename in Content-Dispositon header must be encode in ASCII (ISO-8859-1) for filename and for filename* in url encoded format
        header (sprintf('Content-Disposition: inline; filename="%s";filename*=UTF-8\'\'%s', iconv("UTF-8", "ISO-8859-1//TRANSLIT", $fileName), rawurlencode($fileName)));

        //it's very important!
        if (trim($_REQUEST['data']) == '')
            die('Data cannot be empty!');
            
        // select output format
        if ($format=="EPS")
        {
           header("Content-type: application/eps");
           QRcode::eps($_REQUEST['data'], false ,$errorCorrectionLevel, $matrixPointSize, $borderSize, $blackWhite); 
        }  
        elseif ($format=="TEXT") 
        {
           header("Content-type: text/plain");
           $tab = QRcode::text($_REQUEST['data'], false ,$errorCorrectionLevel, $matrixPointSize, $borderSize);
           foreach ($tab as $row)
              echo "$row";
        }   
        elseif ($format=="RAW")    
           QRcode::raw($_REQUEST['data'], false ,$errorCorrectionLevel, $matrixPointSize, $borderSize); 
        else // default is PNG   
        { 
           header("Content-type: image/png");
           QRcode::png($_REQUEST['data'], false ,$errorCorrectionLevel, $matrixPointSize, $borderSize, false, $blackWhite);    
         }
    } 
    else 
    {    
        die('Data cannot be empty!');
    }    
    
?> 

