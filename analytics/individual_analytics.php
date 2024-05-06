<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("location: login.php");
} 

if (isset($_GET['userToGet'])) {
    $userID = $_GET['userToGet'];
} else {
    header("location: ./analytics_landing_page.php");
}
?>


<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="stylesheets/individual.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/individual_handler.js"></script>
</head>

<script>
    fetchUserData(<?php echo $userID ?>, updateUserData);
</script>

<body>

<header>
    <div class="container header-container">
        <img src="content/logo.png" alt="Company Logo" id="page-logo">
        
        <div id ="fullName"  class="header-title"></div>
        <div id = "role" class="header-subtitle"></div> 

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
        <div class="col-md-4 col-lg-3">
            <div class="sidebar bg-light p-4">
                <h4>Analytics Dashboard</h4>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="#overview" class="nav-link active" id="link-overview">Overview</a>
                    </li>
                    <li class="nav-item">
                        <a href="#tasks" class="nav-link" id="link-tasks">Tasks</a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-8 col-lg-9">
            <div id="overview">
                <header class="mb-3">
                    <h1>General Overview</h1>
                </header>
                <div class="row">
                    <div class="col-md-5 sidebar">
                        <div class="sidebar-row flex-fill d-flex flex-column align-items-center justify-content-center">
                            <div class="number-label">
                                Total Task Completion
                            </div>
                            <div id="circlePercentage" class="circle-percentage d-flex flex-column align-items-center justify-content-center">
                                    <div id="percentageNumber" class="percentage-number" data-bs-toggle="tooltip" data-bs-placement="top" title=""></div>
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
                                <div id="hoursNumber" class="hours-number"></div>
                            </div>
                        </div>
                    </div>
            </div>
            <div id="tasks" class="hidden">
                <header class="main-content-header">
                    <h1>Current Tasks -</h1>
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

                    $sql = "SELECT t.task_name, p.project_name, t.due_date, t.priority, t.estimated_length, t.completion_percentage 
                            FROM tasks t 
                            INNER JOIN projects p ON t.projectID = p.projectID
                            WHERE t.userID = ?";
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
                                <h5 class="task-name">Task: <?php echo htmlspecialchars($taskName); ?></h5>
                                <h5 class="project-name">Project: <?php echo htmlspecialchars($projectName); ?></h5>
                                <p class="task-due-date">Due Date: <?php echo htmlspecialchars($dueDate); ?></p>
                                <p class="task-priority">Priority: <?php echo htmlspecialchars($priority); ?></p>
                                <p class="task-length">Estimated Length: <?php echo htmlspecialchars($estimatedLength); ?> hours</p>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar" role="progressbar" style="width: <?php echo htmlspecialchars($completionPercentage); ?>%;" aria-valuenow="<?php echo htmlspecialchars($completionPercentage); ?>" aria-valuemin="0" aria-valuemax="100">
                                        <?php echo htmlspecialchars($completionPercentage); ?>% Complete
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
</div>

<script>

var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

    document.addEventListener("DOMContentLoaded", function() {
        const links = document.querySelectorAll('.nav-link');
        const sections = document.querySelectorAll('div[id]');

        links.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                links.forEach(lnk => lnk.classList.remove('active'));
                this.classList.add('active');
                sections.forEach(section => section.classList.add('hidden'));
                const activeSection = document.querySelector(this.getAttribute('href'));
                activeSection.classList.remove('hidden');
            });
        });
    });
</script>

</body>
</html>
