<?php
include_once(__DIR__ . '/../src/db_connection.php');
$conn = new mysqli($servername, $username, $password, $db_name);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connection successful!";
}
$conn->close();
?>
