<?php

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

$projectId = 2;


$totalHoursSql = "SELECT SUM(est_length) AS total_estimated_hours FROM task WHERE project_id = $projectId";
$totalHoursResult = $conn->query($totalHoursSql);
$totalHoursRow = $totalHoursResult->fetch_assoc();

$completedHoursSql = "SELECT SUM(est_length * (completion_percentage / 100)) AS total_completed_hours FROM task WHERE project_id = $projectId";
$completedHoursResult = $conn->query($completedHoursSql);
$completedHoursRow = $completedHoursResult->fetch_assoc();

// Calculate remaining hours
$remainingHours = $totalHoursRow['total_estimated_hours'] - $completedHoursRow['total_completed_hours'];

$totalCompletionSql = "SELECT SUM(est_length * completion_percentage) / SUM(est_length) AS overall_completion_percentage FROM task WHERE project_id = $projectId";
$totalCompletionResult = $conn->query($totalCompletionSql);
$totalCompletionRow = $totalCompletionResult->fetch_assoc();

$overallCompletionPercentage = $totalCompletionRow['overall_completion_percentage'];
?>
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Project Name</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="stylesheets/individual.css">
</head>
<body>
    <header>
        <div class="container header-container">
            <img src="content/logo.png" alt="Company Logo" id="page-logo">
            
            <div class="header-title">
                Analytics Dashboard - Project Name
                <div class="project-select mt-2" style="width: 200px;"> <!-- Inline style for width -->
                <select class="form-select form-select-sm" aria-label=".form-select-sm example">
                    <option selected>Select a project</option>
                        <?php
                        $sql = "SELECT project_id, project_title FROM project";
                        $result = $conn->query($sql);
                        
                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row["project_id"] . "'>" . $row["project_title"] . "</option>";
                            }
                        } else {
                            echo "<option disabled>No projects available</option>";
                        }
                        ?>
                </select>
                </div>
                <div class="header-subtitle"></div> 
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
    </header>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5 sidebar">
                <div class="sidebar-row flex-fill d-flex flex-column align-items-center justify-content-center">
                    <div class="number-label">
                        Total Task Completion
                    </div>
                    <div class="circle-percentage d-flex flex-column align-items-center justify-content-center">
                        <div class="percentage-number" data-bs-toggle="tooltip" data-bs-placement="top" title="Hours Done: 150h, Hours Left: 50h">
                            <?php echo round($overallCompletionPercentage); ?>%
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
                            <?php echo round($remainingHours); ?> Hours
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main content starts here -->
            <div class="col-md-7">
                <!-- Main content header -->
                <header class="main-content-header">
                    <?php
                    // Fetch the number of remaining tasks
                    $countTasksSql = "SELECT COUNT(*) AS task_count FROM task WHERE completion_percentage < 100";
                    $countResult = $conn->query($countTasksSql);
                    $taskCountRow = $countResult->fetch_assoc();
                    ?>
                    <h1><?php echo $taskCountRow['task_count']; ?> Remaining Tasks</h1>
                </header>

                <!-- Task container -->
                <div class="task-container">
                <?php
                    // Set the project_id you want to display tasks for
                    $projectId = 2; // Replace with your actual project_id

                    // Fetch task details for a specific project
                    $tasksSql = "SELECT t.task_id, t.task_title, p.project_title, t.due_date, t.priority, t.est_length, t.completion_percentage FROM task t INNER JOIN project p ON t.project_id = p.project_id WHERE t.project_id = $projectId ORDER BY t.due_date ASC";
                    $tasksResult = $conn->query($tasksSql);

                    if ($tasksResult->num_rows > 0) {
                        // Output each task
                        while($taskRow = $tasksResult->fetch_assoc()) {
                            ?>
                            <div class="task-box bg-light border rounded p-3 mb-2">
                                <div class="task-info">
                                    <h5 class="task-name">Task: <?php echo htmlspecialchars($taskRow["task_title"]); ?></h5>
                                    <h5 class="project-name">Project: <?php echo htmlspecialchars($taskRow["project_title"]); ?></h5>
                                    <p class="task-due-date">Due Date: <?php echo htmlspecialchars($taskRow["due_date"]); ?></p>
                                    <p class="task-priority">Priority: <?php echo htmlspecialchars($taskRow["priority"]); ?></p>
                                    <p class="task-length">Estimated Length: <?php echo htmlspecialchars($taskRow["est_length"]); ?> hours</p>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" role="progressbar" style="width: <?php echo htmlspecialchars($taskRow["completion_percentage"]); ?>%;" aria-valuenow="<?php echo htmlspecialchars($taskRow["completion_percentage"]); ?>" aria-valuemin="0" aria-valuemax="100"><?php echo htmlspecialchars($taskRow["completion_percentage"]); ?>% Complete</div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p>No tasks to display</p>";
                    }
                    ?>
                </div>
            </div>
            <!-- Main content ends here -->

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
<?php
$conn->close();
?>
