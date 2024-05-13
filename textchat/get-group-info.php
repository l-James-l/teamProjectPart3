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
    http_response_code(500);
    echo json_encode(array("status" => "error", "message" => "Database connection failed"));
    exit;
}

if (isset($_GET['chat_id']) && !empty($_GET['chat_id'])) {
    $chat_id = $_GET['chat_id'];
    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT u.first_name, u.surname, c.chat_name, c.is_group
                            FROM users u
                            JOIN chat_relation cr ON u.user_id = cr.user_id
                            JOIN chat c ON cr.chat_id = c.chat_id
                            WHERE c.chat_id = ?");
    if ($stmt === false) {
        // Handle prepare statement error
        http_response_code(500);
        echo json_encode(array("status" => "error", "message" => "Prepare statement failed"));
        exit;
    }
    
    $stmt->bind_param("i", $chat_id); // 'i' indicates integer type
    $stmt->execute();    
    
    // Fetch messages as an associative array
    $result = $stmt->get_result();
    $groupInfo = $result->fetch_all(MYSQLI_ASSOC);
    
    // Return the messages as JSON
    echo json_encode(array("status" => "success", "group_info" => $groupInfo));
} else {
    // Chat ID not provided or empty
    http_response_code(400);
    echo json_encode(array("status" => "error", "message" => "Chat ID not provided"));
}
?>
