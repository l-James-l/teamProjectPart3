<?php
include_once(__DIR__ . '/../src/db_connection.php');
$connection=mysqli_connect($servername,$username,$password,$dbname);
$chatCreateStatement=mysqli_stmt_init($connection);
$chatIDRetrievalStatement=mysqli_stmt_init($connection);
$chatRelationCreateStatement=mysqli_stmt_init($connection);
$chatRelationRecipientAddStatement=mysqli_stmt_init($connection);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_SESSION["user_id"])) {
        if(isset($_POST["is_group"])) {
            if(isset($_POST["recipient_user_ID"])) {
                if($_POST["is_group"]=="true") {
                    $isGroup=true;
                    $isAdmin=true;
                    $chatName="test";//hardcoded for now
                }
                else if($_POST["is_group"=="false"]) {
                    $isGroup=false;
                    $chatName="test";//set to name of recipient
                }
                mysqli_stmt_prepare($chatCreateStatement,"INSERT INTO chat(chat_name,is_group)
                VALUES (?,?)");
                mysqli_stmt_bind_param($chatCreateStatement,"si",$chatName,$isGroup);
                mysqli_stmt_execute($chatCreateStatement);
                $affectedRows=mysqli_stmt_affected_rows($chatCreateStatement);
                if($affectedRows==1) {
                    //Success
                }
                else if($affectedRows==-1) {
                    //Query error
                    http_response_code(500);
                }
                $newChatID=mysqli_insert_id($connection);
                mysqli_stmt_prepare($chatRelationCreateStatement,"INSERT INTO chat_relation(chat_id,user_id,is_admin)
                VALUES(?,?,?");
                mysqli_stmt_bind_param($chatRelationCreateStatement,"iii",$newChatID,$_SESSION["user_id"],$isAdmin);
                mysqli_stmt_execute($chatRelationCreateStatement);
                $affectedRows=mysqli_stmt_affected_rows($chatRelationCreateStatement);
                if($affectedRows==1) {
                    //Success
                }
                else if($affectedRows==-1) {
                    //Query error
                    http_response_code(500);
                }
                mysqli_stmt_prepare($chatRelationRecipientAddStatement,"INSERT INTO chat_relation(chat_id,user_id,is_admin)
                VALUES(?,?)");
                mysqli_stmt_bind_param($chatRelationRecipientAddStatement,"iii",$newChatID,$recipientUserID,$isAdmin);
                mysqli_stmt_execute($chatRelationRecipientAddStatement);
                $affectedRows=mysqli_stmt_affected_rows($chatRelationRecipientAddStatement);
                if($affectedRows==1) {
                    //Success
                }
                else if($affectedRows==-1) {
                    //Query error
                    http_response_code(500);
                }
                


            }
        }
            
        
        

    }
}
?>