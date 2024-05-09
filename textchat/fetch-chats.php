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

$user_id = $_SESSION["user_id"];

// Determine whether to fetch groups or non-groups
$is_group = isset($_GET['is_group']) ? intval($_GET['is_group']) : null;

// Prepare the SQL statement
$sql = "SELECT 
cl.message_id, 
cl.chat_id, 
cl.user_id, 
cl.message, 
cl.timestamp, 
u.first_name, 
u.surname,
CASE 
    WHEN (SELECT COUNT(DISTINCT user_id) FROM chat_relation WHERE chat_id = cl.chat_id) > 1 THEN 1 
    ELSE 0 
END AS is_group
FROM 
chat_log cl
JOIN 
users u ON cl.user_id = u.user_id
WHERE 
cl.chat_id = ?
ORDER BY 
cl.timestamp ASC;
";


// Conditionally add filter for groups or non-groups
if ($is_group !== null) {
    $sql .= " AND c.is_group = ?";
}

$sql .= " GROUP BY c.chat_id, c.is_group
          ORDER BY recent_timestamp DESC";

$stmt = $conn->prepare($sql); 

if ($stmt === false) {
    // Handle prepare statement error
    echo json_encode(array("status" => "error", "message" => "Prepare statement failed: " . $conn->error));
    exit;
}

// Bind parameters
if ($is_group !== null) {
    $stmt->bind_param("iii", $user_id, $user_id, $is_group);
} else {
    $stmt->bind_param("ii", $user_id, $user_id);
}

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
    $timestamp = date("Y-m-d H:i:s", strtotime($row['recent_timestamp']));

    // Add chat details to the array
    $chats[] = array(
        "chat_id" => $row['chat_id'],
        "chat_name" => $row['chat_name'],
        "recent_timestamp" => $timestamp
    );
}

// Return the chats as JSON
echo json_encode(array("status" => "success", "chats" => $chats));

// Print the SQL statement to the PHP error log for debugging
error_log("SQL Statement: " . $sql);
?>
