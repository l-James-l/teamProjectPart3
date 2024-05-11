<?php
include_once(__DIR__ . '/../src/db_connection.php');
$connection=mysqli_connect($servername,$username,$password,$dbname);
$groupCheckStatement=mysqli_stmt_init($connection);
$addUserToGroupStatement=mysqli_stmt_init($connection);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_SESSION["user_id"])) {
        if(isset($_POST["chat_id"])) {
            if(isset($_POST["user_id_to_add"])) {
                mysqli_stmt_prepare($groupCheckStatement,"SELECT is_group 
                FROM chat
                WHERE group_id=?");
                mysqli_stmt_bind_param($groupCheckStatement,"i",$_POST["chat_id"]);
                mysqli_stmt_execute($groupCheckStatement);
                $isGroupResult=mysqli_stmt_get_result($groupCheckStatement);
                $groupResultRowCount=mysqli_num_rows($isGroupResult);
                if($groupResultRowCount==0) {
                    //Invalid chat ID
                    http_response_code(500);
                }
                else if($groupResultRowCount==1) {
                    //Check if chat is a group
                    $isGroupResultArray=mysqli_fetch_array($isGroupResult,MYSQLI_ASSOC);
                    if($isGroupResultArray["is_group"]==0) {
                        //Error: chat isn't a group
                    }
                    else {
                        //chat is a group:
                        mysqli_stmt_prepare($addUserToGroupStatement,"INSERT INTO chat_relation(chat_id,user_id,is_admin)
                        VALUES(?,?,0)");
                        mysqli_stmt_bind_param($addUserToGroupStatement,"ii",$_POST["chat_id"],$_SESSION["user_id"]);
                        mysqli_stmt_execute($addUserToGroupStatement);
                        $affectedRows=mysqli_stmt_affected_rows($addUserToGroupStatement);
                        if ($affectedRows == 1) {
                            echo json_encode(['success' => true, 'message' => 'Message deleted successfully.']);
                        } else if ($affectedRows == 0) {
                            http_response_code(404);
                            echo json_encode(['error' => 'No message deleted - no matching message found or lack of permission.']);
                        } else {
                            http_response_code(500);
                            echo json_encode(['error' => 'Query error']);
                        }
                    }
                }
                else {
                    //Database key constraint issue
                    http_response_code(500);
                }
            }
        }
    }
}
?>