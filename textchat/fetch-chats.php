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

if ($conn === false) {
    // Handle database connection error
    echo json_encode(array("status" => "error", "message" => "Database connection failed"));
    exit;
}

// Check if user_id is provided, otherwise set default to 1
$user_id = $_SESSION["user_id"] ?? 1

// Prepare the SQL statement
$sql = "SELECT c.chat_id, c.is_group,
        CASE 
            WHEN c.is_group = 1 THEN c.chat_name 
            ELSE (
                SELECT GROUP_CONCAT(CONCAT(first_name, ' ', surname)) 
                FROM users u 
                INNER JOIN chat_relation cr2 ON u.user_id = cr2.user_id 
                WHERE cr2.chat_id = c.chat_id AND u.user_id != ?
            ) 
        END AS chat_name,
        MAX(cl.timestamp) AS recent_timestamp
        FROM chat c
        INNER JOIN chat_relation cr ON c.chat_id = cr.chat_id
        LEFT JOIN chat_log cl ON c.chat_id = cl.chat_id
        WHERE cr.user_id = ?
        GROUP BY c.chat_id, c.chat_name, c.is_group
        ORDER BY recent_timestamp DESC;";

$stmt = $conn->prepare($sql); 

if ($stmt === false) {
    // Handle prepare statement error
    echo json_encode(array("status" => "error", "message" => "Prepare statement failed: " . $conn->error));
    exit;
}

$stmt->bind_param("ii", $user_id, $user_id); // 'i' indicates integer type
$stmt->execute();    
$result = $stmt->get_result();

if ($result === false) {
    // Handle query error
    echo json_encode(array("status" => "error", "message" => "Query execution failed: " . $conn->error));
    exit;
}

$oneToOneChats = [];
$groupChats = [];

while ($row = $result->fetch_assoc()) {
    // Format the timestamp
    $timestamp = date("Y-m-d H:i:s", strtotime($row['recent_timestamp']));

    // Add chat details to the array
    // $chats[] = array(
    //     "chat_id" => $row['chat_id'],
    //     "chat_name" => $row['chat_name'],
    //     "recent_timestamp" => $timestamp
    // );
    if ($row['is_group'] == 1) {
        $groupChats[] = $row;
    } else {
        $oneToOneChats[] = $row;
    }
}

// Return the chats as JSON
// echo json_encode(array("status" => "success", "chats" => $chats));
echo json_encode(["status" => "success", "oneToOneChats" => $oneToOneChats, "groupChats" => $groupChats]);
// Print the SQL statement to the PHP error log for debugging
error_log("SQL Statement: " . $sql);
?>
