<?php
include_once(__DIR__ . '/../src/db_connection.php');
$connection=mysqli_connect($servername,$username,$password,$dbname);
$statement=mysqli_stmt_init($connection);

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    if(isset($_GET['message_id'])&& !empty($_GET['message_id'])) {
        mysqli_stmt_prepare($statement,"DELETE FROM chat_log
        WHERE message_id=? 
        AND user_id=?");
        mysqli_stmt_bind_param($statement,"ii",$_GET['message_id'],$_SESSION['user_id']);
        mysqli_stmt_execute($statement);
        $affectedRows=mysqli_stmt_affected_rows($statement);
        $result;
        if($affectedRows==1) {
            //Success
        }
        else if($affectedRows==0) {
            //No message deleted - no matching message
        }
        else if(affectedRows==-1) {
            //Query error
        }
    }
}
?>