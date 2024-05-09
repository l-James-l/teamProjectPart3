<?php
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
    if(isset($_POST['message_id'])&& !empty($_POST['message_id'])) {
        if(isset($_POST['edited_message'])&&!empty($_POST['edited_message'])) {
            //Update encrypted message variable once encryption is decided
            $messageId = $_POST['message_id'];
            $editedMessage = $_POST['edited_message'];
            $userId = 1;

            
            mysqli_stmt_prepare($statement,"UPDATE chat_log
            SET encrypted_message=?
            WHERE message_id=?
            AND user_id=?");
            mysqli_stmt_bind_param($statement,"sii",$_POST['edited_message'],$_POST['message_id'],$_SESSION['user_id']);
            mysqli_stmt_execute($statement);
            $affectedRows=mysqli_stmt_affected_rows($statement);
            if($affectedRows==1) {
                //Success
            }
            else if($affectedRows==0) {
                //No message edited - no matching message
                http_response_code(404);
            }
            else if($affectedRows==-1) {
                //Query error
                http_response_code(500);
            }
        }
        
        
    }
}
?>