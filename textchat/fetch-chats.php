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

// Check if chat_id is provided, otherwise set default to 1
$chat_id = isset($_GET['chat_id']) ? $_GET['chat_id'] : 1;

// Prepare the SQL statement to prevent SQL injection
$stmt = $conn->prepare("SELECT cl.message_id, cl.message, cl.timestamp, u.user_id, u.username
                        FROM chat_log cl
                        INNER JOIN (
                            SELECT MAX(message_id) AS max_message_id, user_id
                            FROM chat_log
                            WHERE chat_id = ?
                            GROUP BY user_id
                        ) recent ON cl.message_id = recent.max_message_id
                        INNER JOIN user u ON cl.user_id = u.user_id
                        WHERE cl.chat_id = ?");
if ($stmt === false) {
    // Handle prepare statement error
    echo json_encode(array("status" => "error", "message" => "Prepare statement failed"));
    exit;
}

$stmt->bind_param("ii", $chat_id, $chat_id); // 'i' indicates integer type
$stmt->execute();    

// Fetch messages as an associative array
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);

// Return the messages as JSON
echo json_encode(array("status" => "success", "messages" => $messages));
?>
