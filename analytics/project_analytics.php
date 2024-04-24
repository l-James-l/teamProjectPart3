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
                <div class="project-select mt-2" style="width: 200px;"> 
                    <?php
                    // SQL to fetch projects
                    $projectQuery = "SELECT project_id, project_title FROM project";
                    $projectResult = $conn->query($projectQuery);

                    // Check if there are any projects returned
                    if ($projectResult->num_rows > 0) {
                        // Start the select element
                        echo '<select class="form-select form-select-sm" aria-label=".form-select-sm example">';
                        echo '<option selected>Select a project</option>';
                        
                        // Loop through all the projects and create an option element for each
                        while($project = $projectResult->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($project['project_id']) . '">' . htmlspecialchars($project['project_title']) . '</option>';
                        }
                        
                        // Close the select element
                        echo '</select>';
                    } else {
                        echo 'No projects found.';
                    }
                    ?>
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
                            75%
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
                            30 Hours
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <!-- Main content goes here -->
                <header class="main-content-header">
                    <h1>2 Remaining Tasks</h1>
                </header>
                <div class="task-container">
                    <div class="task-box bg-light border rounded p-3 mb-2">
                        <div class="task-info">
                            <h5 class="task-name">Task: Implement User Authentication</h5>
                            <h5 class="project-name">Project: Website Redesign</h5>
                            <p class="task-due-date">Due Date: 2024-04-30</p>
                            <p class="task-priority">Priority: High</p>
                            <p class="task-length">Estimated Length: 15 hours</p>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">50% Complete</div>
                            </div>
                        </div>
                    </div>
                    <div class="task-box bg-light border rounded p-3 mb-2">
                        <div class="task-info">
                            <h5 class="task-name">Task: Implement User Authentication</h5>
                            <h5 class="project-name">Project: Website Redesign</h5>
                            <p class="task-due-date">Due Date: 2024-04-30</p>
                            <p class="task-priority">Priority: High</p>
                            <p class="task-length">Estimated Length: 15 hours</p>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">50% Complete</div>
                            </div>
                        </div>
                    </div>
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
