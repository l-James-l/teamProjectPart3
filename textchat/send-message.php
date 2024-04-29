<?php
// include_once(__DIR__.'/../src/db_connection.php');

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     if (isset($_POST['message']) && !empty($_POST['message'])) {
//         // Get the message from the POST data and sanitize it
//         $message = mysqli_real_escape_string($conn, $_POST['message']);
        
//         // Get the user ID from the session or authenticate the user 
//         $user_id = 1; // Hardcoded 1 for now
        
//         // Get the chat ID from the POST data
//         if (isset($_POST['chat_id']) && !empty($_POST['chat_id'])) {
//             $chat_id = mysqli_real_escape_string($conn, $_POST['chat_id']);
            
//             // Perform encryption
//             $key = openssl_random_pseudo_bytes(32); // Generate a random encryption key
//             $iv = openssl_random_pseudo_bytes(16); // Generate a random IV
//             $encryptedMessage = openssl_encrypt($message, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
            
//             // Construct the SQL query with prepared statements
//             $sql = "INSERT INTO chat_log (chat_id, message, user_id, timestamp, message_iv)
//             VALUES (1, 'hardcoded_encrypted_message', 1, NOW(), 'hardcoded_iv');";
            
//             // Prepare the statement
//             $stmt = mysqli_prepare($conn, $sql);
            
//             if ($stmt) {
//                 // Bind parameters
//                 mysqli_stmt_bind_param($stmt, "issb", $chat_id, $encryptedMessage, $user_id, $iv);
                
//                 // Execute the statement
//                 $result = mysqli_stmt_execute($stmt);
                
//                 if ($result) {
//                     // Message inserted successfully
//                     echo json_encode(array("status" => "success", "message" => "Message sent successfully"));
//                 } else {
//                     // Failed to insert message
//                     echo json_encode(array("status" => "error", "message" => "Failed to send message: " . mysqli_error($conn)));
//                 }
                
//                 // Close the statement
//                 mysqli_stmt_close($stmt);
//             } else {
//                 // Error in preparing the statement
//                 echo json_encode(array("status" => "error", "message" => "Failed to prepare statement"));
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
?>


<?php
include_once(__DIR__.'/../src/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['message']) && !empty($_POST['message'])) {
        // Get the message from the POST data and sanitize it
        $message = $_POST['message'];
        
        // Get the user ID from the session or authenticate the user 
        $user_id = 1; // Hardcoded 1 for now
        
        // Get the chat ID from the POST data
        if (isset($_POST['chat_id']) && !empty($_POST['chat_id'])) {
            $chat_id = $_POST['chat_id'];
            
            // Construct the SQL query with prepared statements
            $sql = "INSERT INTO chat_log (chat_id, message, user_id, timestamp) VALUES (?, ?, ?, NOW())";
            
            // Prepare the statement
            $stmt = mysqli_prepare($conn, $sql);
            
            if ($stmt) {
                // Bind parameters
                mysqli_stmt_bind_param($stmt, "iss", $chat_id, $message, $user_id);
                
                // Execute the statement
                $result = mysqli_stmt_execute($stmt);
                
                if ($result) {
                    // Message inserted successfully
                    echo json_encode(array("status" => "success", "message" => "Message sent successfully"));
                } else {
                    // Failed to insert message
                    echo json_encode(array("status" => "error", "message" => "Failed to send message: " . mysqli_error($conn)));
                }
                
                // Close the statement
                mysqli_stmt_close($stmt);
            } else {
                // Error in preparing the statement
                echo json_encode(array("status" => "error", "message" => "Failed to prepare statement"));
            }
        } else {
            // Chat ID not provided or empty
            echo json_encode(array("status" => "error", "message" => "Chat ID not provided"));
        }
    } else {
        // Message not provided or empty
        echo json_encode(array("status" => "error", "message" => "Message not provided"));
    }
} else {
    // Request method is not POST
    echo json_encode(array("status" => "error", "message" => "Invalid request method"));
}
?>
