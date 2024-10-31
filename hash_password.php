<?php
// The password you want to hash
$password = 'jojo'; // Replace with the password you want to test

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Output the hashed password
echo $hashedPassword;
?>
