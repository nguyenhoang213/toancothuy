<?php
header("Content-type: text/html; charset=utf-8");
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "toanhoclethuy";

$conn = new mysqli($servername, $username, $password, $dbname);
mysqli_set_charset($conn, 'UTF8');
?>