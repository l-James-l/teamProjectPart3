<?php
include_once(__DIR__ . '/../src/db_connection.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR);
echo "Line 7";
$connection=mysqli_connect($servername,$username,$password,$db_name);
$groupCheckStatement=mysqli_stmt_init($connection);
$adminCheckStatement=mysqli_stmt_init($connection);
$addUserToGroupStatement=mysqli_stmt_init($connection);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_SESSION["user_id"])) {
        if(isset($_POST["chat_id"])) {
            if(isset($_POST["user_id_to_add"])) {
                mysqli_stmt_prepare($groupCheckStatement,"SELECT is_group 
                FROM chat
                WHERE chat_id=?");
                mysqli_stmt_bind_param($groupCheckStatement,"i",$_POST["chat_id"]);
                mysqli_stmt_execute($groupCheckStatement);
                echo "Group check statement ran";
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
                        //check if user is an admin
                        mysqli_stmt_prepare($adminCheckStatement,"SELECT is_admin
                        FROM chat_relation
                        WHERE chat_id=?
                        AND user_id=?");
                        mysqli_stmt_bind_param($adminCheckStatement,"ii",$_POST["chat_id"],$_SESSION["user_id"]);
                        mysqli_stmt_execute($adminCheckStatement);
                        echo "Admin check statement ran";
                        $isAdminResult=mysqli_stmt_get_result($adminCheckStatement);
                        $adminResultRowCount=mysqli_num_rows($isAdminResult);
                        if($adminResultRowCount==0) {
                            //Invalid chat/user combination
                            http_response_code(500);
                        }
                        else if($adminResultRowCount==1) {
                            $isAdminResultArray=mysqli_fetch_array($isAdminResult,MYSQLI_ASSOC);
                            if($isAdminResultArray["is_admin"]==0) {
                                //Error: user isn't an admin for this chat
                            }
                        else {
                            //user is an admin for the chat:
                            
                            mysqli_stmt_prepare($addUserToGroupStatement,"INSERT INTO chat_relation(chat_id,user_id,is_admin)
                            VALUES(?,?,0)");
                            mysqli_stmt_bind_param($addUserToGroupStatement,"ii",$_POST["chat_id"],$_SESSION["user_id"]);
                            mysqli_stmt_execute($addUserToGroupStatement);
                            $affectedRows=mysqli_stmt_affected_rows($addUserToGroupStatement);
                            //change the below messages:
                            if ($affectedRows == 1) {
                                echo json_encode(['success' => true, 'message' => 'Message deleted successfully.']);
                            } 
                            else if ($affectedRows == 0) {
                                http_response_code(404);
                                echo json_encode(['error' => 'No message deleted - no matching message found or lack of permission.']);
                            } else {
                                http_response_code(500);
                                echo json_encode(['error' => 'Query error']);
                            }
                        }
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