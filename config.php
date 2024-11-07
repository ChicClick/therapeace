<?php
// Start the session only if it hasn't been started yet
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the database connection file to reuse the $conn variable
include 'db_conn.php'; 

?>
