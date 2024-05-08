<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once(__DIR__.'/../src/db_connection.php');

try {
    $conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['message']) && !empty($_POST['message']) && isset($_POST['chat_id']) && !empty($_POST['chat_id']) && isset($_POST['user_id']) && !empty($_POST['user_id'])) {
        $message = $_POST['message'];  // The message from the user
        $chat_id = $_POST['chat_id'];  // The chat ID to which the message is being sent
        $user_id = $_POST['user_id'];  // The user ID sending the message

        $sql = "INSERT INTO chat_log (chat_id, message, user_id, timestamp) VALUES (:chat_id, :message, :user_id, NOW())";
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':chat_id', $chat_id);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':user_id', $user_id);

        if ($stmt->execute()) {
            echo json_encode(array("status" => "success", "message" => "Message sent successfully"));
        } else {
            echo json_encode(array("status" => "error", "message" => "Failed to send message"));
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "Message, Chat ID, or User ID not provided"));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid request method"));
}

?>
