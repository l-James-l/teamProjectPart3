<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__ . '/../src/db_connection.php');

if (isset($_GET['chat_id']) && !empty($_GET['chat_id'])) {
    $chat_id = $_GET['chat_id'];  // Sanitize the input properly

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM chat_log WHERE chat_id = :chat_id ORDER BY timestamp DESC");
    $stmt->bindParam(':chat_id', $chat_id);
    $stmt->execute();    
    
    // Fetch messages as an associative array
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Return the messages as JSON
    echo json_encode(array("status" => "success", "messages" => $messages));
} else {
    // Chat ID not provided or empty
    echo json_encode(array("status" => "error", "message" => "Chat ID not provided"));
}
?>
