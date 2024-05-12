<?php
include "../src/db_connection.php";
try {
    $conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
} catch (PDOException $e) {
    echo "<script type='text/javascript'>alert('Failed to connect to database');</script>";
}

if (isset($_POST["task"]) && isset($_POST["employee"]) && isset($_POST["hours"]) && isset($_POST["date"])) {
    $emp_id = $_POST['employee'];
    $task_id = $_POST['task'];
    $hours = $_POST['hours'];
    $date = $_POST['date'];

    $sql = "insert into task_progress_log values (null, $task_id, $emp_id, $hours, Date $date)";
    $conn->query($sql);
    // header("location: ./analytics_landing_page.php?lf=projects");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

        <link rel="canonical" href="https://getbootstrap.com/docs/5.0/examples/headers/">

    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous"> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <style>
        .searchbox {
            margin: 0.5em 0;
        }

        .searchbox:focus {
            outline: 3px solid #ddd;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f6f6f6;
            min-width: 230px;
            border: 1px solid #ddd;
            z-index: 1;
            max-height: 200px;
            overflow: hidden;
            overflow-y: scroll;
        }

        .dropdown-content li {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content li:hover {
            background-color: #afa8a8
        }

        .searchbox:focus ~ .dropdown-content {
            display: block;
        }

        .show {
            display: block;
        }   


        .section {
            padding: 0 10%;
            width: 100%;
        }

        .tasksection {
            background-color: #aba9a9;
        }

        .bg-dark-task {
            background-color: #393939 !important;
        }

        .horizontal-scroll {
            overflow-x: auto;
            padding-bottom: 1%;
        }

        .taskcard {
            min-height: 10rem;
        }
    </style>
</head>
<body>
    <form action="#" method="post" autocomplete="off" style="width:100%; margin-top:15%" class="text-center">
        <h1>Log Hours</h1>
        <?php
        echo "<label class='mr-sm-2' for='projectsearch'>Select Task</label>
        <br>
        <div class='dropdown'>
        <input type='text' placeholder='Search..' id='projectsearch' class='searchbox form-control' style='width: 250px' onkeyup='filterFunction(\"project\")' required>
        <input type='hidden' id='hiddenprojectsearch' name='task' required>";   
        
        $sql = "SELECT task_title, task_id FROM task";
        
        $result = $conn->query($sql);

        
        if (!$result) {
            echo "Connection Error.";
            exit;
        }
        $projectsArray = $result->fetchAll();
        
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

        <label for="empsearch">Staff Member</label>
        <br>
        <div class="dropdown">
            <input type="text" placeholder="Search.." id="empsearch" class="searchbox form-control" style="width: 250px" onkeyup="filterFunction('emp')" required>
            <input type="hidden" id="hiddenempsearch" name="employee" required>
            <?php
            $sql = 'SELECT first_name, surname, user_id FROM users';
            
            $result = $conn->query($sql);

            if (!$result) {
                echo "Connection Error.";
                exit;
            }
            $userArray = $result->fetchAll();
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

        <br>

        <div style="display: inline-block">
            <label for="hours">Hours To Log</label>
            <input type="number" id="hours" name="hours" class="form-control" placeholder="Hours" style="width: 250px;" min="1" required value=1>
        </div>

        <br>

        <div style="display:inline-block">
            <label for="date">Select Date</label>
            <input class="form-control" id="date" name="date" type="date" style="width: 250px;" required value="">
            <script>    
                let date = new Date(); 
                let text = date.toISOString();
                text = text.substring(0, text.length - 14)
                document.getElementById("date").setAttribute("value", text);
            </script>
        </div>

        <br>

        <button type="submit" class="btn btn-primary" style="margin:10px; width:250px">Submit</button>

    </form>
</body>
</html>

<script>
    // searchable drop down functions

    // filters the displayed results in the dropdown based on what the user has typed in the input
    function filterFunction(dropdown) {
        document.getElementById(dropdown + 'search').classList.add("is-invalid");
        document.getElementById(dropdown + 'search').classList.remove("is-valid");
        document.getElementById("submitButton").classList.add("disabled");
        document.getElementById('hidden' + dropdown + 'search').value = null;

        var input, filter, ul, li, i;
        input = document.getElementById(dropdown + "search");
        filter = input.value.toUpperCase();
        div = document.getElementById(dropdown + "Dropdown");
        li = div.getElementsByTagName("li");
        for (i = 0; i < li.length; i++) {
            txtValue = li[i].textContent || li[i].innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    }

    // when a list element in the drop down is selected, set the text input and hidden input to the corresponding project title and ID
    function setSearch(dropdown, id) {
        document.getElementById('hidden' + dropdown + 'search').value = document.getElementById('id_' + id).value;
        document.getElementById(dropdown + 'search').value = document.getElementById(id).innerHTML;
        document.getElementById(dropdown + 'search').classList.add("is-valid");
        document.getElementById(dropdown + 'search').classList.remove("is-invalid");
        document.getElementById(dropdown + 'Dropdown').classList.remove("show");
        if (document.getElementById("empsearch").classList.contains("is-valid")) {
            if (<?php echo isset($editingTask) ? 'true' : 'false' ?>) {
                document.getElementById("submitButton").classList.remove("disabled");
            }else if (document.getElementById("projectsearch").classList.contains("is-valid")) {
                document.getElementById("submitButton").classList.remove("disabled");
            }

        }
    }

    // if the task is being edited rather than created, use the setSearch function to pre set the emp drop down 
    <?php if (isset($editingTask)) {echo "setSearch('emp', 'emp_li_$setEmpTo')";}?>
</script>