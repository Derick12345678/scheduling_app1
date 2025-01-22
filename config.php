<?php
// Database connection details
$username = 'xxxxx';
$password = 'xxxxx';
$hostname = 'localhost';
$port = '3307'; // Specify the port
$database = 'scheduling_app';

// Create a connection using MySQLi
$db = new mysqli($hostname, $username, $password, $database);
// Check connection
if ($db->connect_errno) {
    // Connection failed
    echo 'Failed to connect to MySQL: ' . $db->connect_error();
    exit();
}
/* 
    $query = "
        UPDATE calendar
        JOIN appointments ON appointments.client_id = calendar.id
        SET calendar.status = 'completed'
        WHERE TIMESTAMP(appointments.date, appointments.time) < NOW();";


    if ($db->query($query) === TRUE) {
        echo "Statuses updated successfully.";
    } else {
        echo "Error: " . $db->error;
    }
*/
?>
