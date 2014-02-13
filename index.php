<?php    

// parameters:
//    data  - content 
//    level - error correction level ("L"ow,"M"edium,"Q"uarter,"H"igh)  
//    size  - pixels per point

    include "qrlib.php";    
    
    $errorCorrectionLevel = 1;
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
        QRcode::png($_REQUEST['data'], false ,$errorCorrectionLevel, $matrixPointSize, 2);    
        
    } else {    
        die('Data cannot be empty!');
        
    }    
    
?>   
