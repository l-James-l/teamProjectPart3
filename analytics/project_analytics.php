<?php


// SQL data collection


$userID = 1; // get userID from url later

// DB connection 
$servername = "localhost";
$username = "phpUser";
$password = "p455w0rD";
$dbname = "make_it_all";  
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";

$stmt = $conn->prepare("SELECT first_name, surname, role FROM users WHERE user_id = ?");
if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);
}
$stmt->bind_param('i', $userID);
$stmt->execute();

$stmt->bind_result($firstName, $surname, $role);



if ($stmt->fetch()) {
    $fullName = $firstName . " " . $surname;
    if ($role === 'Mgr') {
        $role = 'Manager';
    } elseif ($role === 'TL') {
        $role = 'Team Leader';
    } elseif ($role === 'Emp') {
        $role = 'Employee';
} else {
    echo "No user found with the specified userID.";
}
}

$stmt->close();

$sql = "SELECT project_id, completion_percentage, est_length FROM task WHERE user_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);
}
$stmt->bind_param('i', $userID);
$stmt->execute();
$stmt->bind_result($projectID, $completionPercentage, $estimatedHours);

$completionPercentages = array();
$estimatedHoursArray = array();
$projectIDs = array();

while ($stmt->fetch()) {
    $completionPercentages[] = $completionPercentage;
    $estimatedHoursArray[] = $estimatedHours;
    $projectIDs[] = $projectID;
}

$stmt->close();

$completionSum = array_sum($completionPercentages);
if (count($completionPercentages) > 0) {
    $overallCompletion = $completionSum / count($completionPercentages);
} else {
    $overallCompletion = 0; 
}

$hoursDoneArray = array();
foreach ($completionPercentages as $index => $completionPercentage) {
    $hoursDoneArray[$index] = $completionPercentage * $estimatedHoursArray[$index];
    $hoursDone = array_sum($hoursDoneArray);
    $hoursLeft = array_sum($estimatedHoursArray) - $hoursDone;
}

$projectCount = count(array_unique($projectIDs));
$taskCount = count($completionPercentages);

$conn->close();

?>







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - <?php echo $fullName ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="stylesheets/individual.css">
</head>
<body>
    <!-- <header>
        <div class="container header-container">
            <img src="content/logo.png" alt="Company Logo" id="page-logo">
            
            <div class="header-title">
                Analytics Dashboard - <?php //echo $fullName ?>
                <div class="header-subtitle"><?php //echo $role ?></div> 
            </div>

            <div class="dropdown">
                <a href="#" class="d-block link-dark text-decoration-none user-dropdown dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="content/icon.png" alt="mdo" width="42" height="42" class="rounded-circle">
                </a>
                <ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1">
                    <li>
                        <div class="dropdown-item dropdown-item-nohover">
                            <div style="white-space: normal;">
                                <img src="content/icon.png" alt="mdo" width="32" height="32" class="rounded-circle">
                                <span style="padding-left: 10px;">John Doe</span>
                            </div>
                            <span>johndoe@example.com</span>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="login.php">Sign out</a></li>
                </ul>
            </div>
        </div>
    </header> -->

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5 sidebar">
                <div class="sidebar-row flex-fill d-flex flex-column align-items-center justify-content-center">
                    <div class="number-label">
                        Total Task Completion
                    </div>
                    <div class="circle-percentage d-flex flex-column align-items-center justify-content-center" style="background-color: <?php 
                            if ($overallCompletion < 40) {
                                echo 'red';
                            } elseif ($overallCompletion >= 40 && $overallCompletion <= 70) {
                                echo 'yellow';
                            } else {
                                echo 'green';
                            }
                        ?>;">
                            <div class="percentage-number" data-bs-toggle="tooltip" data-bs-placement="top" title="Hours Done: <?php echo $hoursDone?>, Hours Left: <?php echo $hoursLeft; ?>">
                                <?php echo $overallCompletion; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                
                <div class="sidebar-row flex-fill">
                    <p></p>
                </div>
                <div class="sidebar-row flex-fill d-flex flex-column align-items-center justify-content-center">
                    <div class="number-label">
                        Current Remaining Assigned Hours
                    </div>
                    
                    <div class="hours-left d-flex flex-column align-items-center justify-content-center">
                        <div class="hours-number" >
                            <?php echo $hoursLeft ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <header class="main-content-header">
                    <h1>Current Tasks - <?php echo $taskCount ?> across <?php echo $projectCount ?> projects</h1>
                </header>
                <div class="task-container">
                <?php
                    $servername = "localhost";
                    $username = "phpUser";
                    $password = "p455w0rD";
                    $dbname = "make_it_all";  
                    $conn = new mysqli($servername, $username, $password, $dbname);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $sql = "SELECT t.task_name, p.project_name, t.due_date, t.priority, t.est_length, t.completion_percentage 
                            FROM tasks t 
                            INNER JOIN projects p ON t.project_id = p.project_id
                            WHERE t.user_id = ?";
                    $stmt = $conn->prepare($sql);
                    if ($stmt === false) {
                        die('MySQL prepare error: ' . $conn->error);
                    }
                    $stmt->bind_param('i', $userID);
                    $stmt->execute();
                    $stmt->bind_result($taskName, $projectName, $dueDate, $priority, $estimatedLength, $completionPercentage);

                    while ($stmt->fetch()) {
                    ?>
                        <div class="task-box bg-light border rounded p-3 mb-2">
                            <div class="task-info">
                                <h5 class="task-name">Task: <?php echo $taskName; ?></h5>
                                <h5 class="project-name">Project: <?php echo $projectName; ?></h5>
                                <p class="task-due-date">Due Date: <?php echo $dueDate; ?></p>
                                <p class="task-priority">Priority: <?php echo $priority; ?></p>
                                <p class="task-length">Estimated Length: <?php echo $estimatedLength; ?> hours</p>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo $completionPercentage; ?>%;" aria-valuenow="<?php echo $completionPercentage; ?>" aria-valuemin="0" aria-valuemax="100">
                                        <?php echo $completionPercentage; ?>% Complete
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }

                    $stmt->close();
                    $conn->close();
                    ?>

                </div>
            </div>
        </div>
    </div>

    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    </script>
    
</body>
</html>
