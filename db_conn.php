<?php

$db_host = getenv('DB_HOST');
$db_user = getenv('DB_USERNAME');
$db_password = getenv('DB_PASSWORD');
$db_name = getenv('DB_NAME');

$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>

