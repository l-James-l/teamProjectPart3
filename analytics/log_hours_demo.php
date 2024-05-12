<?php
include "../src/db_connection.php";
try {
    $conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
} catch (PDOException $e) {
    echo "<script type='text/javascript'>alert('Failed to connect to database');</script>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="post">
        <?php
        echo "<label class='mr-sm-2' for='projectsearch'>Select Project</label>
        <br>
        <div class='dropdown'>
        <input type='text' placeholder='Search..' id='projectsearch' class='searchbox form-control' style='width: 250px' onkeyup='filterFunction(\"project\")' required>
        <input type='hidden' id='hiddenprojectsearch' name='project' required>";   
        
        // managers can assign tasks to any project, team leader have to be leading the project.
        $sql = "SELECT project_title, project_id FROM  project";
        
        $query = $conn->prepare($sql);
        $result = $conn->execute();

        
        if (!$result) {
            echo "Connection Error.";
            exit;
        }
        $projectsArray = $query->fetchAll();
        
        echo "<div id='projectDropdown' class='dropdown-content' style='width: 250px'>";
        
        // for each project a list element is echoed to display the project name and a hidden input which holds the project ID, linked with their ID's
        $i = 0;
        foreach ($projectsArray as $project) {
            echo "<li id='project_li_$i' onmousedown='setSearch(\"project\", \"project_li_$i\")'>$project[0]</li>";
            echo "<input type='hidden' id='id_project_li_$i' value='$project[1]'>";
            $i++;
        }
        echo "</div></div><br>";
        ?>

        <label for="empsearch">Assign to Staff Member</label>
        <br>
        <div class="dropdown">
            <input type="text" placeholder="Search.." id="empsearch" class="searchbox form-control" style="width: 250px" onkeyup="filterFunction('emp')" required>
            <input type="hidden" id="hiddenempsearch" name="employee" required>
            <?php
            $sql = 'SELECT first_name, surname, user_id FROM users';
            

            $query = $conn->prepare($sql);
            $result = $conn->execute();


            if (!$result) {
                echo "Connection Error.";
                exit;
            }
            $userArray = $query->fetchAll();
            ?>
            <div id="empDropdown" class="dropdown-content" style="width: 250px">
                <?php
                $i = 0;
                foreach ($userArray as $user) {
                    echo "<li id='emp_li_$i' onmousedown='setSearch(\"emp\", \"emp_li_$i\")'>".$user['first_name'] ." ". $user['surname']."</li>";
                    echo "<input type='hidden' id='id_emp_li_$i' value='".$user[user_id]."'>";
                    $i++;
                }

                ?>
            </div>
        </div>
    </form>
</body>
</html>