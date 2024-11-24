<?php
// Database connection for user directory
$userDB = new mysqli("localhost", "root", "", "studentdirectory");

// Database connection for admin directory
$adminDB = new mysqli("localhost", "root", "", "admindirectory");

// Check connection
if ($userDB->connect_error) {
    die("Connection failed: " . $userDB->connect_error);
}

if ($adminDB->connect_error) {
    die("Connection failed: " . $adminDB->connect_error);
}
?>
