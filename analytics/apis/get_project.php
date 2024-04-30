<?php
include "db_connection.php";
try {
    $conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
} catch (PDOException $e) {
    echo "<script type='text/javascript'>alert('Failed to connect to database');</script>";
}

if ($conn) {
    if (isset($_GET["project_ID"])) {
        $final_json = json_decode("{}");

        $stmt = "select project.project_id, project_title, project.due_date, sum(est_length) as total_hours, avg(completion_percentage) as total_completion, count(task_id) as task_count
         from project left join task on project.project_id = task.project_id  
         where project.project_id = 3
         group by project.project_id";
         
        $query = $conn->prepare($stmt);
        $query->bindParam(":project_id", $_GET["project_ID"]);
        $result = $query->execute();
        if ($result) {
            $final_json->project = $query->fetch();
            $stmt = "select * from task where project_id = :project_id";
            if (isset($_GET["task_search"])) {
                $search_string = $_GET["task_search"];
                $stmt = $stmt . " and task_title like '%$search_string%'";
            }
            if (isset($_GET["task_filter_milestone"]) && $_GET["task_filter_milestone"] == "true") {
                $stmt = $stmt . " and is_milestone = true";
            }

            if (isset($_GET["sort_value"])) {
                if ($_GET["sort_value"] == "due_date") {
                    $stmt = $stmt . " order by due_date "; 
                }
                else if ($_GET["sort_value"] == "priority") {
                    $stmt = $stmt . " order by priority "; 
                }
                else if ($_GET["sort_value"] == "est_length") {
                    $stmt = $stmt . " order by est_length "; 
                }
            } else {
                $stmt = $stmt . " order by due_date"; 
            }
            if (isset($_GET["sort_order"])) {
                if ($_GET["sort_order"] == "ASC" || $_GET["sort_order"] == "DESC") {
                    $stmt = $stmt .  $_GET["sort_order"];
                } 
            }

            // echo $stmt;
            $query = $conn->prepare($stmt);
            $query->bindParam(":project_id", $_GET["project_ID"]);
            $result = $query->execute();
            if ($result) {
                $final_json->tasks = $query->fetchAll();
                
                $stmt = "select users.user_id, concat(first_name, ' ', surname) as full_name, count(task_id) as task_count, sum(est_length) as total_hours 
                from users left join task on users.user_id = task.user_id 
                where project_id = :project_id
                group by users.user_id";
                $query = $conn->prepare($stmt);
                $query->bindParam(":project_id", $_GET["project_ID"]);
                $result = $query->execute();
                if ($result) {
                    $final_json->user_assignment = $query->fetchAll();
                    echo json_encode(array("status" => "success", "message" => $final_json));
                } else {
                    echo json_encode(array("status" => "error", "message" => "user data query failed"));
                    exit;
                }
                exit;
            } else {
                echo json_encode(array("status" => "error", "message" => "task query failed"));
                exit;
            }
        }else {
            echo json_encode(array("status" => "error", "message" => "project query failed"));
            exit;
        }
    } else {
        echo json_encode(array("status" => "error", "message" => "project ID not set"));
        exit;
    }
} else {
    echo json_encode(array("status" => "error", "message" => "failed to connect to db"));
    exit;
}

?>
