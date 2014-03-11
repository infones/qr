<?php    

// parameters:
//    data  - content 
//    level - error correction level ("L"ow,"M"edium,"Q"uarter,"H"igh)  
//    size  - pixels per point
//    border - in points

    include "qrlib.php";    
    
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
    
        //it's very important!
        if (trim($_REQUEST['data']) == '')
            die('Data cannot be empty!');
            
        // user data
        QRcode::png($_REQUEST['data'], false ,$errorCorrectionLevel, $matrixPointSize, $borderSize);    
        
    } else {    
        die('Data cannot be empty!');
        
    }    
    
?>   
