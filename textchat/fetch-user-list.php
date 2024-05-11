<?php
include_once(__DIR__ . '/../src/db_connection.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "phpUser";
$password = "p455w0rD";
$dbname = "make_it_all"; 
$connection=mysqli_connect($servername,$username,$password,$dbname);
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if(isset($_SESSION["user_id"])) {
        $statement=mysqli_stmt_init($connection);
        mysqli_stmt_prepare($statement,"SELECT user_id, first_name, surname 
        FROM user;");
        mysqli_stmt_execute($statement);
        $result=mysqli_stmt_get_result($statement);
        echo($result);
        $resultingUsers=[];
        if(mysqli_num_rows($result)>0) {
            while($row=mysqli_fetch_array($result)) {
                $resultingUsers[]=$row;
            }
            echo "running";
            echo json_encode($resultingUsers);
        }
        else {
            //no users
        }
    }
    else {
        //user not logged in
        echo "no session";
    }
}
else {
    //wrong request method
}



?>