<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("location: ../src/login.php");
    exit(); // Ensure no further code is executed after redirect
}

if (isset($_GET["page"])) {
    $page = $_GET["page"];
} else {
    $page = "overview";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Landing Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="stylesheets/analytics_landing_page.css">
</head>
<body>
    <?php
    $currentPage = "analytics"; 
    include "../src/header.php"; 
    ?>

    <div class="d-flex">
        <div class="d-flex flex-column flex-shrink-0 p-3 bg-light sidebar">
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="?projectToGet=<?php echo $_GET['projectToGet'] ?? '' ?>&page=overview" class="nav-link <?php echo $page == "overview" ? "active" : "link-dark" ?>" aria-current="page">
                        <i class="bi bi-folder-fill"></i>
                        Overview
                    </a>
                </li>
                <li>
                    <a href="?projectToGet=<?php echo $_GET['projectToGet'] ?? '' ?>&page=tasks" class="nav-link <?php echo $page == "tasks" ? "active" : "link-dark" ?>">
                        <i class="bi bi-list-check"></i>
                        Tasks
                    </a>
                </li>
            </ul>
            <hr>
        </div>
        <div class="main-content-container" style="padding: 20px; width: 100%;">
            <!-- Content area for different pages -->
            <?php if ($page == "overview") { ?>
                <h2>Overview</h2>
                <!-- Content for Overview -->
            <?php } elseif ($page == "tasks") { ?>
                <h2>Tasks</h2>
                <!-- Content for Tasks -->
            <?php } ?>
        </div>
    </div>
</body>
</html>

</html>
