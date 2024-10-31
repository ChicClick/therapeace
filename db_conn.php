<?php

$servername = "localhost";
$username = "root";
$password = "JM0987654";
$dbname = "therapeacedb";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

