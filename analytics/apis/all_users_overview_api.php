<?php
include "../../src/db_connection.php";
try {
    $conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
} catch (PDOException $e) {
    echo "<script type='text/javascript'>alert('Failed to connect to database');</script>";
}

if ($conn) {
    if (isset($_GET["search"])) {
        $searchString = "%".$_GET["search"]."%";
    } else {
        $searchString = "%";
    }
    $stmt = "select users.user_id, first_name, surname, role, email, count(task_id) as task_count, count(project.project_id) as project_count     
    from users left join (select * from task where completion_percentage < 100) as task2 on task2.user_id = users.user_id     
    left join project on task2.project_id = project.project_id
    where (users.first_name like '$searchString'
    or users.surname like '$searchString'
    or users.email like '$searchString') ";

    if (isset($_GET["filerMgr"]) || isset($_GET["filerTL"]) || isset($_GET["filerEmp"])) {
        $stmt = $stmt . "and role in (";
        if (isset($_GET["filerMgr"]) && $_GET["filerMgr"] == "true") {
            $stmt = $stmt . "'Mgr',";
        } 
        if (isset($_GET["filerTL"]) && $_GET["filerTL"] == "true") {
            $stmt = $stmt . "'TL',";
        }
        if (isset($_GET["filerEmp"]) && $_GET["filerEmp"] == "true") {
            $stmt = $stmt . "'Emp',";
        }
        $stmt = rtrim($stmt, ",") . ")";
    }

    $stmt = $stmt."group by users.user_id";

    if (!isset($_GET["sortValue"])) {
        $stmt = $stmt." order by users.surname";
    } else if ($_GET["sortValue"] == "first_name") {
        $stmt = $stmt." order by users.first_name";
    } else if ($_GET["sortValue"] == "surnmae") {
        $stmt = $stmt." order by users.surname";
    } else if ($_GET["sortValue"] == "task_count") {
        $stmt = $stmt." order by task_count";
    } else {
        $stmt = $stmt." order by users.surname";
    }

    if (!isset($_GET["sortOrder"])){
        $stmt = $stmt." ASC";
    } else if (in_array($_GET["sortOrder"], array("ASC", "DESC"))) {
        $stmt = $stmt." " . $_GET["sortOrder"];
    }

    // print($stmt);
    $result = $conn->query($stmt);
    if ($result) {
        echo json_encode(array("status" => "success", "message" => $result->fetchAll()));
    } else {
        echo json_encode(array("status" => "error", "message" => "Unexpected error occured"));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Failed to conntect to server"));
}
?>
