<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Database credentials
$servername = "localhost";
$username = "phpUser";
$password = "p455w0rD";
$dbname = "make_it_all"; 

// Establish a database connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
if ($conn === false) {
    // Handle database connection error
    echo json_encode(array("status" => "error", "message" => "Database connection failed"));
    exit;
}

// Check if a chat_id is provided
if (isset($_GET['chat_id']) && !empty($_GET['user_id'])) {
    $chat_id = $_GET['chat_id'];
    $user_id = $_GET['user_id'];
    
    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM chat_log WHERE chat_id = ? AND user_id = ?");
    if ($stmt === false) {
        // Handle prepare statement error
        echo json_encode(array("status" => "error", "message" => "Prepare statement failed"));
        exit;
    }
    
    // Bind parameters and execute the statement
    $stmt->bind_param("ii", $chat_id, $_SESSION["user_id"]); // 'i' indicates integer type
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
