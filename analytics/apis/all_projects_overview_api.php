<?php
include "../../src/db_connection.php";
try {
    $conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
} catch (PDOException $e) {
    echo "<script type='text/javascript'>alert('Failed to connect to database');</script>";
}

if ($conn) {
    if (isset($_GET["user_id"])) {
        $role = $conn->query("select role from users where user_id=".$_GET["user_id"])->fetch()["role"];
        $searchString = isset($_GET["search"]) ? "%".$_GET["search"]."%" : "%";

        if (!in_array($role, array("Mgr", "TL", "Emp"))){
            echo json_encode(array("status" => "error", "message" => "Invalid Paramaters")); 
            exit();
        }
        else if ($role == "Mgr") {
            $stmt = "select project_title, first_name, surname, project.due_date, count(task_id) as task_count, avg(completion_percentage) as overall_completion
            from project inner join users on users.user_id = project.team_leader_id 
            left join task on project.project_id = task.project_id 
            where (project_title like '$searchString'
            or first_name like '$searchString'
            or surname like '$searchString')
            group by project.project_id";
            
        } else if ($role == "TL" || $role == "Emp") {
            $stmt = "select project_title, first_name, surname, project.due_date, count(task_id) as task_count, avg(completion_percentage) as overall_completion
            from project inner join users on users.user_id = project.team_leader_id 
            left join task on project.project_id = task.project_id 
            where (project.team_leader_id = :user_id
            or project.project_id in (select project_id from task where user_id = :user_id))
            and (project_title like '$searchString'
            or first_name like '$searchString'
            or surname like '$searchString')
            group by project.project_id";  
        } 
        if (!isset($_GET["sortValue"])) {
            $stmt = $stmt." order by project.due_date";
        } else if ($_GET["sortValue"] == "due date") {
            $stmt = $stmt." order by project.due_date";
        } else if ($_GET["sortValue"] == "completion_percentage") {
            $stmt = $stmt." order by overall_completion";
        } else if ($_GET["sortValue"] == "assigned tasks") {
            $stmt = $stmt." order by task_count";
        } else {
            $stmt = $stmt." order by project.due_date";
        }

        if (!isset($_GET["sortOrder"])){
            $stmt = $stmt." ASC";
        } else if (in_array($_GET["sortOrder"], array("ASC", "DESC"))) {
            $stmt = $stmt." " . $_GET["sortOrder"];
        }
        
        // print($stmt);
        $query = $conn->prepare($stmt);
        $query->bindParam(":user_id", $_GET["user_id"]);
        $query->bindParam(":search_string", $searchString);
        // print($query->queryString);
        $query->execute();
        echo json_encode(array("status" => "success", "message" => $query->fetchAll(PDO::FETCH_ASSOC)));
    } else {
        echo json_encode(array("status" => "error", "message" => "Invalid Paramaters"));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "failed to establish connection"));
}



?>

