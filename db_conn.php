<?php

$db_host = getenv('DB_HOST');
$db_user = getenv('DB_USER');
$db_password = getenv('DB_PASSWORD');
$db_name = getenv('DB_NAME');

$conn = new mysqli("localhost", "root", "", "therapeacedb", 8111);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>

