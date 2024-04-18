<?php
include_once(__DIR__ . '/../src/db_connection.php'); // Include the file with database credentials

// Create a connection to the database
$conn = mysqli_connect($servername, $username, $password, $db_name);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Session configuration
session_start();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
