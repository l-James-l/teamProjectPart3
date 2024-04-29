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
            $final_json["project"] = $query->fetch();
            $stmt = " select * from task where project_id = :project_id";
            $query = $conn->prepare($stmt);
            $query->bindParam(":project_id", $_GET["project_ID"]);
            $result = $query->execute();
            if ($result) {
                $final_json["tasks"] = $query->fetchAll();
                echo json_encode(array("status" => "success", "message" => $final_json));
                // exit;
            }
        }
    } 
}

// echo json_encode(array("status" => "error", "message" => null));

?>
