<?php

include_once(__DIR__.'/../src/db_connection.php');
$connection=mysqli_connect($servername,$username,$password,$dbname);
$statement=mysqli_stmt_init($connection);

// include_once(__DIR__ . '/../src/db_connection.php');


// $conn = new mysqli($servername, $username, $password, $db_name);

// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['message']) && !empty($_POST['message'])) {
        // Get the message from the POST data and sanitise it
        $message = mysqli_real_escape_string($conn, $_POST['message']);
        
        // Get the user ID from the session or authenticate the user 
        $user_id = 1; // Hardcoded 1 for now
        
        // Get the chat ID from the POST data
        if (isset($_POST['chat_id']) && !empty($_POST['chat_id'])) {
            $chat_id = mysqli_real_escape_string($conn, $_POST['chat_id']);
            
            // Insert message into chat_log table
            $sql = "INSERT INTO chat_log (chat_id, encrypted_message, user_id, timestamp) 
                    VALUES ('$chat_id', '$message', '$user_id', NOW())";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                // Message inserted successfully
                echo json_encode(array("status" => "success", "message" => "Message sent successfully"));
            } else {
                // Failed to insert message
                echo json_encode(array("status" => "error", "message" => "Failed to send message"));
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
// $conn->close();
?>
