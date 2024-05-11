<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "phpUser";
$password = "p455w0rD";
$dbname = "make_it_all"; 
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if message_id is set and not empty
    if (isset($_POST['message_id']) && !empty($_POST['message_id']) && isset($_SESSION['user_id'])) {
        $messageId = $_POST['message_id'];
        $userId = $_SESSION['user_id'];

        // Prepare the DELETE statement
        $stmt = mysqli_prepare($conn, "DELETE FROM chat_log WHERE message_id = ? AND user_id = ?");
        if ($stmt === false) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to prepare the statement']);
            exit;
        }
        mysqli_stmt_bind_param($stmt, "ii", $messageId, $userId);
        mysqli_stmt_execute($stmt);
        $affectedRows = mysqli_stmt_affected_rows($stmt);

        if ($affectedRows >0) {
            echo json_encode(['success' => true, 'message' => 'Message deleted successfully.']);
        } else if ($affectedRows == 0) {
            http_response_code(404);
            echo json_encode(['error' => 'No message deleted - no matching message found or lack of permission.']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Query error']);
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Message ID is required']);
    }
}
mysqli_close($conn);

?>

// include_once(__DIR__ . '/../src/db_connection.php');
// $connection=mysqli_connect($servername,$username,$password,$dbname);
// $statement=mysqli_stmt_init($connection);

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     if(isset($_POST['message_id'])&& !empty($_POST['message_id'])) {
//         mysqli_stmt_prepare($statement,"DELETE FROM chat_log
//         WHERE message_id=? 
//         AND user_id=?");
//         mysqli_stmt_bind_param($statement,"ii",$_POST['message_id'],$_SESSION['user_id']);
//         mysqli_stmt_execute($statement);
//         $affectedRows=mysqli_stmt_affected_rows($statement);
//         if($affectedRows==1) {
//             //Success
//         }
//         else if($affectedRows==0) {
//             //No message deleted - no matching message
//             http_response_code(404);
//         }
//         else if($affectedRows==-1) {
//             //Query error
//             http_response_code(500);
//         }
//     }
// }
?>