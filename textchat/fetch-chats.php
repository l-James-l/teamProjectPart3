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

// Prepare the SQL statement
$stmt = $conn->prepare("SELECT c.chat_id, c.chat_name, m.message, m.timestamp, u.first_name, u.surname
                        FROM chat c
                        LEFT JOIN chat_log m ON c.chat_id = m.chat_id
                        LEFT JOIN users u ON m.user_id = u.user_id
                        WHERE c.chat_id = ?
                        GROUP BY c.chat_id
                        ORDER BY m.timestamp DESC
                        LIMIT 3"); // Adjust the LIMIT as needed

if ($stmt === false) {
    // Handle prepare statement error
    echo json_encode(array("status" => "error", "message" => "Prepare statement failed: " . $conn->error));
    exit;
}

$stmt->bind_param("i", $chat_id); // 'i' indicates integer type
$stmt->execute();    

$result = $stmt->get_result();

if ($result === false) {
    // Handle query error
    echo json_encode(array("status" => "error", "message" => "Query execution failed: " . $conn->error));
    exit;
}

$chats = array();

while ($row = $result->fetch_assoc()) {
    // Format the timestamp
    $timestamp = date("Y-m-d H:i:s", strtotime($row['timestamp']));

    // Add chat details to the array
    $chats[] = array(
        "chat_id" => $row['chat_id'],
        "chat_name" => $row['chat_name'],
        "last_message" => $row['message'],
        "last_message_timestamp" => $timestamp,
        "other_user_name" => $row['first_name'] . ' ' . $row['surname']
    );
}

// Return the chats as JSON
echo json_encode(array("status" => "success", "chats" => $chats));
?>
