<?php
include_once(__DIR__ . '/../src/db_connection.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR);
session_start();

$connection = mysqli_connect($servername, $username, $password, $db_name);
$groupCheckStatement = mysqli_stmt_init($connection);
$adminCheckStatement = mysqli_stmt_init($connection);
$addUserToGroupStatement = mysqli_stmt_init($connection);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION["user_id"])) {
        if (isset($_POST["chat_id"]) && isset($_POST["user_id_to_add"])) {
            mysqli_stmt_prepare($groupCheckStatement, "SELECT is_group FROM chat WHERE chat_id=?");
            mysqli_stmt_bind_param($groupCheckStatement, "i", $_POST["chat_id"]);
            mysqli_stmt_execute($groupCheckStatement);
            $isGroupResult = mysqli_stmt_get_result($groupCheckStatement);
            $groupResultRowCount = mysqli_num_rows($isGroupResult);

            if ($groupResultRowCount == 0) {
                // Invalid chat ID
                http_response_code(500);
                echo json_encode(['error' => 'Invalid chat ID.']);
            } else {
                // Check if chat is a group
                $isGroupResultArray = mysqli_fetch_array($isGroupResult, MYSQLI_ASSOC);
                if ($isGroupResultArray["is_group"] == 0) {
                    // Error: chat isn't a group
                    http_response_code(400);
                    echo json_encode(['error' => 'This chat is not a group.']);
                } else {
                    // Chat is a group
                    // Check if user is an admin
                    mysqli_stmt_prepare($adminCheckStatement, "SELECT is_admin FROM chat_relation WHERE chat_id=? AND user_id=?");
                    mysqli_stmt_bind_param($adminCheckStatement, "ii", $_POST["chat_id"], $_SESSION["user_id"]);
                    mysqli_stmt_execute($adminCheckStatement);
                    $isAdminResult = mysqli_stmt_get_result($adminCheckStatement);
                    $adminResultRowCount = mysqli_num_rows($isAdminResult);

                    if ($adminResultRowCount == 0) {
                        // Invalid chat/user combination
                        http_response_code(500);
                        echo json_encode(['error' => 'Invalid chat/user combination.']);
                    } else {
                        $isAdminResultArray = mysqli_fetch_array($isAdminResult, MYSQLI_ASSOC);
                        if ($isAdminResultArray["is_admin"] == 0) {
                            // Error: user isn't an admin for this chat
                            http_response_code(403);
                            echo json_encode(['error' => 'You are not an admin for this chat.']);
                        } else {
                            // User is an admin for the chat
                            mysqli_stmt_prepare($addUserToGroupStatement, "INSERT INTO chat_relation(chat_id, user_id, is_admin) VALUES (?, ?, 0)");
                            mysqli_stmt_bind_param($addUserToGroupStatement, "ii", $_POST["chat_id"], $_POST["user_id_to_add"]);
                            mysqli_stmt_execute($addUserToGroupStatement);
                            $affectedRows = mysqli_stmt_affected_rows($addUserToGroupStatement);

                            if ($affectedRows == 1) {
                                echo json_encode(['success' => true, 'message' => 'User added successfully.']);
                            } else if ($affectedRows == 0) {
                                http_response_code(404);
                                echo json_encode(['error' => 'No rows affected.']);
                            } else {
                                http_response_code(500);
                                echo json_encode(['error' => 'Query error.']);
                            }
                        }
                    }
                }
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Missing chat_id or user_id_to_add.']);
        }
    } else {
        http_response_code(401);
        echo json_encode(['error' => 'User not authenticated.']);
    }
}
?>
