<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "phpUser";
$password = "p455w0rD";
$dbname = "make_it_all"; 
$conn = mysqli_connect($servername, $username, $password, $dbname);

if ($conn === false) {
    // Handle database connection error
    echo json_encode(array("status" => "error", "message" => "Database connection failed"));
    exit;
}

if (isset($_GET['chat_id']) && !empty($_GET['chat_id'])) {
    $chat_id = $_GET['chat_id'];
    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM chat_log WHERE chat_id = ?");
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
