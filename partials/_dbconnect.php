<?php
$servername = "sql312.infinityfree.com"; 
$username   = "if0_41511465";   
$password   = "Sa12#ma34@rth47136";    
$database   = "if0_41511465_epiz_12345678_carrental";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>