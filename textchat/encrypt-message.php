<?php

// Include necessary files and establish database connection
include_once(__DIR__.'/../src/db_connection.php');

// Get message and chat ID from POST data
$message = mysqli_real_escape_string($conn, $_POST['message']);

$chatId = 1;
$user_id = 1;

// Perform encryption
// Note: Implement your encryption logic here

// Example: Encrypt message using AES-256-CBC
$key = openssl_random_pseudo_bytes(32); // Generate a random encryption key
$iv = openssl_random_pseudo_bytes(16); // Generate a random IV
$encryptedMessage = openssl_encrypt($message, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

// Store the encrypted message and IV in the database


// Construct the SQL query with hardcoded IDs
$sql = "INSERT INTO chat_log (chat_id, message, user_id, timestamp) 
        VALUES ('$chatId', '$message', '$user_id', NOW())";

$result = mysqli_query($conn, $sql);

if ($result) {
    // Message inserted successfully
    echo json_encode(array("status" => "success", "message" => "Message sent successfully"));
} else {
    // Failed to insert message
    echo json_encode(array("status" => "error", "message" => "Failed to send message"));
}
?>
