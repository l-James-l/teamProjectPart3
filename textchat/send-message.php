<?php

// Include necessary files and establish database connection
include_once(__DIR__.'/../src/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['message']) && !empty($_POST['message'])) {
        // Get the message from the POST data and sanitise it
        $message = mysqli_real_escape_string($conn, $_POST['message']);
        
        // Get the user ID from the session or authenticate the user 
        $user_id = 1; // Hardcoded 1 for now
        
        // Get the chat ID from the POST data
        if (isset($_POST['chat_id']) && !empty($_POST['chat_id'])) {
            $chat_id = mysqli_real_escape_string($conn, $_POST['chat_id']);
            
            // Perform encryption
            // Note: Implement your encryption logic here
            
            // Example: Encrypt message using AES-256-CBC
            $key = openssl_random_pseudo_bytes(32); // Generate a random encryption key
            $iv = openssl_random_pseudo_bytes(16); // Generate a random IV
            $encryptedMessage = openssl_encrypt($message, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
            
            // Store the encrypted message, encryption key, and IV in the database
            $sql = "INSERT INTO chat_log (chat_id, message, user_id, timestamp, message_iv) 
                    VALUES ('$chat_id', '$encryptedMessage', '$user_id', NOW(), '$iv')";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                // Message inserted successfully
                echo json_encode(array("status" => "success", "message" => "Message sent successfully"));
            } else {
                // Failed to insert message
                echo json_encode(array("status" => "error", "message" => "Failed to send message: " . mysqli_error($conn)));
            }
        } else {
            // Chat ID not provided or empty
            echo json_encode(array("status" => "error", "message" => "Chat ID not provided"));
        }
    } else {
        // Message not provided or empty
        echo json_encode(array("status" => "error", "message" => "Message not provided"));
    }
} else {
    // Request method is not POST
    echo json_encode(array("status" => "error", "message" => "Invalid request method"));
}
?>
