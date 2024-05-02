<?php
$servername = "localhost";
$username = "phpUser";
$password = "p455w0rD";
$dbname = "make_it_all";
$conn = mysqli_connect($servername, $username, $password, $dbname);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['chat_id']) && !empty($_GET['chat_id'])) {
        // Get the chat ID from the GET data
        $chat_id = $_GET['chat_id'];

        // Prepare the SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM chat_log WHERE chat_id = ?");
        if ($stmt === false) {
            // Handle prepare statement error
            echo json_encode(array("status" => "error", "message" => "Prepare statement failed: " . $conn->error));
            exit;
        }

        $stmt->bind_param("i", $chat_id); // 'i' indicates integer type
        $stmt->execute();

        // Get the result
        $result = $stmt->get_result();

        // Fetch messages as an associative array
        $messages = $result->fetch_all(MYSQLI_ASSOC);

        // Decrypt messages
        foreach ($messages as &$message) {
            // Decode the encrypted message and IV from base64
            $decodedMessage = base64_decode($message['message']);
            $decodedIV = base64_decode($message['message_iv']);

            // Perform decryption
            $decryptedMessage = openssl_decrypt($decodedMessage, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $decodedIV);

            if ($decryptedMessage === false) {
                // Handle decryption error
                echo json_encode(array("status" => "error", "message" => "Decryption failed"));
                exit;
            }

            // Assign decrypted message to the message array
            $message['message'] = $decryptedMessage;
        }

        // Return the messages as JSON
        echo json_encode(array("status" => "success", "messages" => $messages));
    } else {
        // Chat ID not provided or empty
        echo json_encode(array("status" => "error", "message" => "Chat ID not provided"));
    }
} else {
    // Request method is not GET
    echo json_encode(array("status" => "error", "message" => "Invalid request method"));
}

// Close the database connection
mysqli_close($conn);
?>
