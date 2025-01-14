<?php
// Database connection details
$username = '----';
$password = '----';
$hostname = 'localhost';
$port = '3307'; 
$database = 'scheduling_app';

// Creating a connection using MySQLi
$db = new mysqli($hostname, $username, $password, $database);
// Check connection
if ($db->connect_errno) {
    
    echo 'Failed to connect to MySQL: ' . $db->connect_error();
    exit();
}

?>
