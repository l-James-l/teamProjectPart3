<?php
include_once(__DIR__ . '/../src/db_connection.php');

if (isset($_GET['chat_id']) && !empty($_GET['chat_id'])) {
    // Get the chat ID from the query string and sanitise it
    $chat_id = mysqli_real_escape_string($conn, $_GET['chat_id']);
    $chat_id = 1; // Hardcoded for now
    
    // Fetch messages from the chat_log table for the specified chat ID
    $sql = "SELECT * FROM chat_log WHERE chat_id = '$chat_id' ORDER BY timestamp DESC";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        // Fetch messages as an associative array
        $messages = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        // Return the messages as JSON
        echo json_encode(array("status" => "success", "messages" => $messages));
    } else {
        // Failed to fetch messages
        echo json_encode(array("status" => "error", "message" => "Failed to fetch messages"));
    }
} else {
    // Chat ID not provided or empty
    echo json_encode(array("status" => "error", "message" => "Chat ID not provided"));
}
?>
