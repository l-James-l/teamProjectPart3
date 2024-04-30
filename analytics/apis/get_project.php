<?php
include "../../src/db_connection.php";
try {
    $conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
} catch (PDOException $e) {
    echo "<script type='text/javascript'>alert('Failed to connect to database');</script>";
}

if ($conn) {
    if (isset($_GET["project_ID"])) {
        $final_json = json_decode("{}");

        $stmt = "select * from project where project_id = :project_id";
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
                echo json_encode(array("status" => "success", "message" => $final_json));
                exit;
            }
        }
    } 
}

echo json_encode(array("status" => "error", "message" => null));

?>
