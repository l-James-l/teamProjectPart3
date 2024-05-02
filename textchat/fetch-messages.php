<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
include_once(__DIR__ . '/../src/db_connection.php');

// if (isset($_GET['chat_id']) && !empty($_GET['chat_id'])) {
//     // Get the chat ID from the query string and sanitise it
//     $chat_id = mysqli_real_escape_string($conn, $_GET['chat_id']);
//     $chat_id = 1; // Hardcoded for now
    
//     // Fetch messages from the chat_log table for the specified chat ID
//     $sql = "SELECT * FROM chat_log WHERE chat_id = '$chat_id' ORDER BY timestamp DESC";
//     $result = mysqli_query($conn, $sql);

//     if ($result) {
//         // Fetch messages as an associative array
//         $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
//         // Return the messages as JSON
//         echo json_encode(array("status" => "success", "messages" => $messages));
//     } else {
//         // Failed to fetch messages
//         echo json_encode(array("status" => "error", "message" => "Failed to fetch messages"));
//     }
// } else {
//     // Chat ID not provided or empty
//     echo json_encode(array("status" => "error", "message" => "Chat ID not provided"));
// }
// 

if (isset($_GET['chat_id']) && !empty($_GET['chat_id'])) {
    $chat_id = $_GET['chat_id'];  // Sanitization with PDO, like how I did send-messages

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM chat_log WHERE chat_id = :chat_id ORDER BY timestamp DESC");
    $stmt->execute(['chat_id' => $chat_id]);
    
    // Fetch messages as an associative array
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the messages as JSON
    echo json_encode(array("status" => "success", "messages" => $messages));
} else {
    // Chat ID not provided or empty
    echo json_encode(array("status" => "error", "message" => "Chat ID not provided"));
}

?>