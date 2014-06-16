<?php
/**
 * Project: test
 * File: convert_elements.php 
 * Author: Matej Polák <polakmatko@gmail.com>
 * Date: 13.6.2014
 * Time: 12:59
 */ 

if (isset($_GET['e'])) {
    echo json_encode(json_decode($_GET['e']));
} else {
    echo '[]';
}
