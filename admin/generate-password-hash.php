<?php
// The password you want to hash
$password = '12345678';

// Hash the password using the PASSWORD_DEFAULT algorithm
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Output the hashed password
echo $hashed_password;
?>
