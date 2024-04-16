<?php

include '../src/db_connection.php';

// Establishing a connection
$conn = mysqli_connect($host, $user, $password, $database);

// Check connection status 
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

session_start();