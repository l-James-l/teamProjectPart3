<?php

// Include necessary files and establish database connection
include_once(__DIR__.'/../src/db_connection.php');

// Get message and chat ID from POST data
$message = mysqli_real_escape_string($conn, $_POST['message']);
$chatId = mysqli_real_escape_string($conn, $_POST['chat_id']);

// Perform encryption
// Note: Implement your encryption logic here

// Example: Encrypt message using AES-256-CBC
$key = openssl_random_pseudo_bytes(32); // Generate a random encryption key
$iv = openssl_random_pseudo_bytes(16); // Generate a random IV
$encryptedMessage = openssl_encrypt($message, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

// Store the encrypted message and IV in the database
$sql = "INSERT INTO chat_log (chat_id, encrypted_message, user_id, timestamp) 
        VALUES ('$chatId', '$encryptedMessage', '$user_id', NOW())";
$result = mysqli_query($conn, $sql);

if ($result) {
    // Message inserted successfully
    echo json_encode(array("status" => "success", "message" => "Message sent successfully"));
} else {
    // Failed to insert message
    echo json_encode(array("status" => "error", "message" => "Failed to send message"));
}
?>
