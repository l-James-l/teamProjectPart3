<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
 
include_once(__DIR__.'/../src/db_connection.php');

try {
    $conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
// } catch (PDOException $e) {
//     print "Error!: " . $e->getMessage() . "<br/>";
//     die();
// }
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit;
    }

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     if (isset($_POST['message']) && !empty($_POST['message'])) {
//         // Get the message from the POST data and sanitise it
//         $message = mysqli_real_escape_string($conn, $_POST['message']);
        
//         // Get the user ID from the session or authenticate the user 
//         $user_id = 1; // Hardcoded 1 for now
        
//         // Get the chat ID from the POST data
//         if (isset($_POST['chat_id']) && !empty($_POST['chat_id'])) {
//             $chat_id = mysqli_real_escape_string($conn, $_POST['chat_id']);
            
//             // Insert message into chat_log table
//             $sql = "INSERT INTO chat_log (chat_id, encrypted_message, user_id, timestamp) 
//                     VALUES ('$chat_id', '$message', '$user_id', NOW())";
//             $result = mysqli_query($conn, $sql);

//             if ($result) {
//                 // Message inserted successfully
//                 echo json_encode(array("status" => "success", "message" => "Message sent successfully"));
//             } else {
//                 // Failed to insert message
//                 echo json_encode(array("status" => "error", "message" => "Failed to send message"));
//             }
//         } else {
//             // Chat ID not provided or empty
//             echo json_encode(array("status" => "error", "message" => "Chat ID not provided"));
//         }
//     } else {
//         // Message not provided or empty
//         echo json_encode(array("status" => "error", "message" => "Message not provided"));
//     }
// } else {
//     // Request method is not POST
//     echo json_encode(array("status" => "error", "message" => "Invalid request method"));
// }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['message']) && !empty($_POST['message']) && isset($_POST['chat_id']) && !empty($_POST['chat_id'])) {
        $message = $_POST['message'];  // The message from the user
        $chat_id = $_POST['chat_id'];  // The chat ID to which the message is being sent
        $user_id = 1;  // Hardcoded user ID, for now, will replace with dynamic value later

        
        $sql = "INSERT INTO chat_log (chat_id, message, user_id, timestamp) VALUES (:chat_id, :message, :user_id, NOW())";
        $stmt = $conn->prepare($sql);

        
        $stmt->bindParam(':chat_id', $chat_id);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':user_id', $user_id);

      
        if ($stmt->execute()) {
            echo json_encode(array("status" => "success", "message" => "Message sent successfully"));
        } else {
            echo json_encode(array("status" => "error", "message" => "Failed to send message"));
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "Message or Chat ID not provided"));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid request method"));
}

?>
