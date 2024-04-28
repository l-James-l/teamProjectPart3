<?php

// Include necessary files and establish database connection
include_once(__DIR__.'/../src/db_connection.php');

// Get message and chat ID from POST data
$message = mysqli_real_escape_string($conn, $_POST['message']);

$chatId = 1; // For demonstration purposes only; should come from POST data or session
$user_id = 1; // For demonstration purposes only; should come from authentication

// Perform encryption
// Note: Implement your encryption logic here
$key = openssl_random_pseudo_bytes(32); // Generate a random encryption key
$iv = openssl_random_pseudo_bytes(16); // Generate a random IV
$encryptedMessage = openssl_encrypt($message, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

// Construct the SQL query with prepared statements
$sql = "INSERT INTO chat_log (chat_id, encrypted_message, user_id, timestamp) 
        VALUES (?, ?, ?, NOW())";

// Prepare the statement
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    // Bind parameters
    mysqli_stmt_bind_param($stmt, "isi", $chatId, $encryptedMessage, $user_id);

    // Execute the statement
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        // Message inserted successfully
        echo json_encode(array("status" => "success", "message" => "Message sent successfully"));
    } else {
        // Failed to insert message
        echo json_encode(array("status" => "error", "message" => "Failed to send message"));
    }

    // Close the statement
    mysqli_stmt_close($stmt);
} else {
    // Error in preparing the statement
    echo json_encode(array("status" => "error", "message" => "Failed to prepare statement"));
}

// Close the database connection
mysqli_close($conn);
?>
