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
    if(isset($_POST['message_id'])&& !empty($_POST['message_id'])&& isset($_POST['edited_message']) && !empty($_POST['edited_message'])) {
            $messageId = $_POST['message_id'];
            $editedMessage = $_POST['edited_message'];
            $userId = $_SESSION['user_id'] ?? 1;

            $stmt = mysqli_prepare($conn, "UPDATE chat_log SET message = ? WHERE message_id = ? AND user_id = ?");

            if ($stmt === false) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to prepare the statement: ' . mysqli_error($conn)]);
                exit;
            }
    
            mysqli_stmt_bind_param($stmt, "sii", $editedMessage, $messageId, $userId);
            
            if (mysqli_stmt_execute($stmt) === false) {
                // Error executing the statement
                http_response_code(500);
                echo json_encode(['error' => 'Failed to execute the statement: ' . mysqli_stmt_error($stmt)]);
                exit;
            }
    
            $affectedRows = mysqli_stmt_affected_rows($stmt);
    
            if ($affectedRows > 0) {
                echo json_encode(['success' => true, 'message' => 'Message updated successfully.']);
            } else {
                // No message found to update
                http_response_code(404);
                echo json_encode(['error' => 'No message edited - no matching message found or lack of permission.']);
            }
    
            mysqli_stmt_close($stmt);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Message ID and Edited Message are required']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid request method']);
        
    }
?>