<?php
define('TIMEZONE', 'Asia/Colombo');
date_default_timezone_set(TIMEZONE);

$servername = "localhost";
$username = "root";
$password = "";
$databse = "erav_sk_marketing";


// Create connection
$conn = mysqli_connect($servername, $username, $password, $databse);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$resentID="";

date_default_timezone_set('Asia/Colombo');
?>