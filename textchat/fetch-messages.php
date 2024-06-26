<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
$servername = "localhost";
$username = "phpUser";
$password = "p455w0rD";
$dbname = "make_it_all"; 
$conn = mysqli_connect($servername, $username, $password, $dbname);
$user_id = $_SESSION["user_id"];
if ($conn === false) {
    // Handle database connection error
    echo json_encode(array("status" => "error", "message" => "Database connection failed"));
    exit;
}

if (isset($_GET['chat_id']) && !empty($_GET['chat_id'])) {
    $chat_id = $_GET['chat_id'];
    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT cl.message_id, cl.chat_id, cl.user_id, cl.message, cl.timestamp, u.first_name, u.surname 
    FROM chat_log cl
    JOIN users u ON cl.user_id = u.user_id
    WHERE cl.chat_id = ?
    ORDER BY cl.timestamp ASC"); // Change ASC to DESC if you want the newest messages first


    if ($stmt === false) {
        // Handle prepare statement error
        echo json_encode(array("status" => "error", "message" => "Prepare statement failed"));
        exit;
    }
    
    $stmt->bind_param("i", $chat_id); // 'i' indicates integer type
    $stmt->execute();    
    
    // Fetch messages as an associative array
    $result = $stmt->get_result();
    $messages = $result->fetch_all(MYSQLI_ASSOC);
    
    // Return the messages as JSON
    echo json_encode(array("status" => "success", "messages" => $messages));
} else {
    // Chat ID not provided or empty
    echo json_encode(array("status" => "error", "message" => "Chat ID not provided"));
}
?>