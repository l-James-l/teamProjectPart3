<?php
include_once(__DIR__ . '/../src/db_connection.php');
$connection=mysqli_connect($servername,$username,$password,$dbname);
$statement=mysqli_stmt_init($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['message_id'])&& !empty($_POST['message_id'])) {
        mysqli_stmt_prepare($statement,"DELETE FROM chat_log
        WHERE message_id=? 
        AND user_id=?");
        mysqli_stmt_bind_param($statement,"ii",$_POST['message_id'],$_SESSION['user_id']);
        mysqli_stmt_execute($statement);
        $affectedRows=mysqli_stmt_affected_rows($statement);
        if($affectedRows==1) {
            //Success
        }
        else if($affectedRows==0) {
            //No message deleted - no matching message
            http_response_code(404);
        }
        else if($affectedRows==-1) {
            //Query error
            http_response_code(500);
        }
    }
}
?>