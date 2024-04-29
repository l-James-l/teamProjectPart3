<?php
$servername = "localhost";
$username = "phpUser";
$password = "p455w0rD";
$dbname = "make_it_all"; 
$conn = mysqli_connect($servername, $username, $password, $dbname);
$message = mysqli_real_escape_string($conn, $_POST['message']);
$chatId = 1; // For demonstration purposes only; should come from POST data or session
$user_id = 1; // For demonstration purposes only; should come from authentication

// Perform encryption
$key = openssl_random_pseudo_bytes(32); // Generate a random encryption key
$iv = openssl_random_pseudo_bytes(16); // Generate a random IV
$encryptedMessage = openssl_encrypt($message, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

// Construct the SQL query with prepared statements
$sql = "INSERT INTO chat_log (chat_id, message, user_id, timestamp, message_iv)
            VALUES (?, ?, ?, NOW(), ?)";

// Prepare the statement
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    // Bind parameters
    mysqli_stmt_bind_param($stmt, "isss", $chatId, $encryptedMessage, $user_id, $iv);

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
