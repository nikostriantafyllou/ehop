<?php
    $db_user = 'root';
    $db_password = '';
    $db_host = 'localhost';
    $db_name = 'eshop';
    
    $dbc = @mysqli_connect($db_host, $db_user, $db_password, $db_name) OR 
            die('Δεν είναι δυνατή η σύνδεση με τη βάση δεδομένων: ' . mysqli_connect_error());
    
?>
    
    
