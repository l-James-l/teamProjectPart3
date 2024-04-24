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
            <!-- ... existing header content ... -->
            <div class="header-title">
                Analytics Dashboard - Project Name
                <div class="project-select mt-2" style="width: 200px;">
                    <!-- PHP code for connecting to database and populating the dropdown -->
                    <!-- Assume $conn is the mysqli connection object -->
                    <select class="form-select form-select-sm" id="project-dropdown" aria-label=".form-select-sm example">
                        <option selected>Select a project</option>
                        <?php
                            $projectQuery = "SELECT project_id, project_title FROM project";
                            $projectResult = $conn->query($projectQuery);
                            while ($project = $projectResult->fetch_assoc()) {
                                echo '<option value="' . htmlspecialchars($project['project_id']) . '">' . htmlspecialchars($project['project_title']) . '</option>';
                            }
                        ?>
                    </select>
                </div>
            </div>
        </div>
            <!-- ... -->
    </header>

    <!-- Main content -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar for Total Task Completion and Remaining Assigned Hours -->
            <div class="col-md-5 sidebar">
                <div class="sidebar-row flex-fill d-flex flex-column align-items-center justify-content-center">
                    <div class="number-label">Total Task Completion</div>
                    <div class="circle-percentage d-flex flex-column align-items-center justify-content-center">
                        <div class="percentage-number" data-bs-toggle="tooltip" data-bs-placement="top" title="Completion">
                            <!-- This will be filled by AJAX -->
                        </div>
                    </div>
                </div>
                <div class="sidebar-row flex-fill d-flex flex-column align-items-center justify-content-center">
                    <div class="number-label">Current Remaining Assigned Hours</div>
                    <div class="hours-left d-flex flex-column align-items-center justify-content-center">
                        <div class="hours-number">
                            <!-- This will be filled by AJAX -->
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main area for displaying tasks -->
            <div class="col-md-7">
                <header class="main-content-header">
                    <h1>Project Tasks</h1>
                </header>
                <div class="task-container">
                    <!-- Tasks will be dynamically loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery and AJAX script for dynamically loading tasks -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
                $(document).ready(function() {
                    $('#project-dropdown').on('change', function() {
                        var projectId = $(this).val();
                        if(projectId) {
                            $.ajax({
                                url: 'fetch_project.php', // Make sure this path is correct
                                type: 'GET',
                                data: { project_id: projectId },
                                dataType: 'json',
                                success: function(response) {
                                    if (response.error) {
                                        $('.task-container').html('<p>' + response.error + '</p>');
                                    } else {
                                        var html = '';
                                        response.forEach(function(task) {
                                            html += '<div class="task-box bg-light border rounded p-3 mb-2">';
                                            html += '<div class="task-info">';
                                            html += '<h5 class="task-name">Task: ' + task.task_title + '</h5>';
                                            html += '<p class="task-due-date">Due Date: ' + task.due_date + '</p>';
                                            html += '<p class="task-priority">Priority: ' + task.priority + '</p>';
                                            html += '<p class="task-length">Estimated Length: ' + task.est_length + ' hours</p>';
                                            html += '<div class="progress" style="height: 20px;">';
                                            html += '<div class="progress-bar" role="progressbar" style="width: ' + task.completion_percentage + '%;" aria-valuenow="' + task.completion_percentage + '" aria-valuemin="0" aria-valuemax="100">' + task.completion_percentage + '% Complete</div>';
                                            html += '</div>';
                                            html += '</div>';
                                            html += '</div>';
                                        });
                                        $('.task-container').html(html);
                                    }
                                }
                            });
                        }
                    });
                });
    </script>


    <!-- Tooltip activation script -->
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
    
</body>
</html>
