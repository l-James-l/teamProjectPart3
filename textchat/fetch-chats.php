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

// Check if user_id is provided, otherwise set default to 1
$user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 1;

// Prepare the SQL statement
$stmt = $conn->prepare("SELECT 
c.chat_id,
c.chat_name,
c.is_group,
cr.user_id,
MAX(cl.message) AS recent_message, -- Aggregate the message column
MAX(cl.timestamp) AS timestamp -- Aggregate the timestamp column
FROM 
chat c
INNER JOIN 
chat_relation cr ON c.chat_id = cr.chat_id
INNER JOIN 
chat_log cl ON c.chat_id = cl.chat_id
WHERE 
cr.user_id = ?
GROUP BY 
c.chat_id,
c.chat_name,
c.is_group,
cr.user_id;
"); 

if ($stmt === false) {
    // Handle prepare statement error
    echo json_encode(array("status" => "error", "message" => "Prepare statement failed: " . $conn->error));
    exit;
}

$stmt->bind_param("i", $user_id); // 'i' indicates integer type
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
        "is_group" => $row['is_group'],
        "recent_message" => $row['recent_message'],
        "recent_message_timestamp" => $timestamp
    );
}

// Return the chats as JSON
echo json_encode(array("status" => "success", "chats" => $chats));
?>
