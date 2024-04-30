<?php
session_start();

if (isset($_GET["page"]) && isset($_GET["project_ID"])) {
    $page = $_GET["page"];
} else if (!isset($_GET["page"])) {
    $page = "overview";
} else {
    header("location: analytics_landing_page.php");
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
    <link rel="stylesheet" href="stylesheets/individual.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="js/project_analytics.js"></script>


</head>

<body>
    <?php
    if (isset($_SESSION["user_id"])) {
        $currentPage = "analytics";
        include "../../src/header.php";
    } else {
        header("location: ../../src/login.php");
    }
    ?>
    

    <div>
        <div class="d-flex flex-column flex-shrink-0 p-3 bg-light sidebar">
            <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">

            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="?page=overview" class="nav-link <?php echo $page == "projects" ? "active" : "link-dark" ?>" aria-current="page">
                        <i class="bi bi-folder-fill"></i>
                        Overview
                    </a>
                </li>
                <li>
                    <a href="?page=users" class="nav-link <?php echo $page == "users" ? "active" : "link-dark" ?>">
                        <i class="bi bi-people-fill"></i>
                        Users Assignment
                    </a>
                </li>
                <li>
                    <a href="?page=progression" class="nav-link <?php echo $page == "progression" ? "active" : "link-dark" ?>">
                        <i class="bi bi-people-fill"></i>
                        Progression
                    </a>
                </li>

            </ul>
            <hr>

        </div>
        <div class="main-content-container">
            <h1 id="project_title"></h1>

            <?php if ($page == "projects") { ?>
            <!-- <div class="row" style="margin: auto;">
                <div class="col-8" style="padding-bottom: 10px; padding-left: unset">
                    <input id='searchbar' type="search" class="form-control" placeholder="Search..."
                        oninput="get_project_json(<?php echo $_SESSION['user_id'] ?>)" aria-label="Search">
                </div>

                <div class="dropdown col-2" style="padding: 0px">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdownMenuButton"
                        data-bs-toggle="dropdown" style="width: 90%;">Sort</button>
                    <div class=" dropdown-menu" aria-labelledby="filterDropdownMenuButton">
                        <button class="dropdown-item" type="button"
                            onclick="change_sort_value('due date');get_project_json(<?php echo $_SESSION['user_id'] ?>)">Due date</button>
                        <button class="dropdown-item" type="button"
                            onclick="change_sort_value('completion_percentage');get_project_json(<?php echo $_SESSION['user_id'] ?>)">Completion Percentage</button>
                        <button class="dropdown-item" type="button"
                            onclick="change_sort_value('assigned tasks');get_project_json(<?php echo $_SESSION['user_id'] ?>)">Assigned Tasks</button>
                        <input type="hidden" id="sortValue" value="due date">
                    </div>
                </div>
                <button id="asc-button" class="btn btn-secondary col-1" style="height: fit-content;" 
                    onclick="change_sort_order('ASC');get_project_json(<?php echo $_SESSION['user_id'] ?>)">
                    <i class="bi bi-sort-up"></i>
                </button>
                <button id="desc-button" class="btn btn-outline-secondary col-1" style="height: fit-content;"
                    onclick="change_sort_order('DESC');get_project_json(<?php echo $_SESSION['user_id'] ?>)">
                    <i class="bi bi-sort-down"></i>
                </button>
                <input type="hidden" id="sortOrder" value="ASC">
                
            </div> -->

            <?php } else if ($page == "overview") { ?>
            <div>
                <div> <!-- summary details container-->
                    <h3>Summary</h3>
                    <div style="display: -webkit-inline-box;">
                        <div id="summary-pie" class="pie" style="--c:darkblue;--b:10px"></div>
                        <p id="summary-label"></p>
                    </div>
                </div>
                
                 <div> <!-- task display container -->
                    <h3 id="task_count"></h3>
                    <div class="row" style="margin: auto;">
                        <div class="col-6" style="padding-bottom: 10px; padding-left: unset">
                            <input id='searchbar' type="search" class="form-control" placeholder="Search..."
                                oninput="get_project_from_api(<?php echo $_GET['project_ID']?>)" aria-label="Search">
                        </div>

                        <div class="dropdown col-2" style="padding: 0px">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdownMenuButton"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside" style="width: 90%;">Filter</button>
                                <div class="dropdown-menu" aria-labelledby="filterDropdownMenuButton">
                                    <button class="dropdown-item d-flex justify-content-between" type="button" onclick="toggleFilter('milestone');get_project_from_api(<?php echo $_GET['project_ID']?>)">
                                        milestones <i id="milestoneToggleIcon" class="bi bi-x"></i>
                                    </button>
                                    <input type="hidden" id="milestoneToggleValue" value=0>
                                </div>
                        </div>

                        <div class="dropdown col-2" style="padding: 0px">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdownMenuButton"
                                data-bs-toggle="dropdown" style="width: 90%;">Sort</button>
                            <div class="dropdown-menu" aria-labelledby="filterDropdownMenuButton">
                                <button class="dropdown-item" type="button"
                                    onclick="change_sort_value('due_date');get_project_from_api(<?php echo $_GET['project_ID']?>)">Due Date</button>
                                <button class="dropdown-item" type="button"
                                    onclick="change_sort_value('priority');get_project_from_api(<?php echo $_GET['project_ID']?>)">Priority</button>
                                <button class="dropdown-item" type="button"
                                    onclick="change_sort_value('est_length');get_project_from_api(<?php echo $_GET['project_ID']?>)">Hours</button>
                                <input type="hidden" id="sortValue" value="due_date">
                            </div>
                        </div>
                        <button id="asc-button" class="btn btn-secondary col-1" style="height: fit-content;" 
                            onclick="change_sort_order('ASC');get_project_from_api(<?php echo $_GET['project_ID']?>)">
                            <i class="bi bi-sort-up"></i>
                        </button>
                        <button id="desc-button" class="btn btn-outline-secondary col-1" style="height: fit-content;"
                            onclick="change_sort_order('DESC');get_project_from_api(<?php echo $_GET['project_ID']?>)">
                            <i class="bi bi-sort-down"></i>
                        </button>
                        <input type="hidden" id="sortOrder" value="ASC">
                    </div>

                    <div id="task-container" class="task-container">
                        <!-- filled with JS -->
                    </div>
                </div>
            </div>

            <?php } ?>
       
            
        </div>
    </div>

</body>

</html>



<script>
    full_project_json = get_project_from_api(<?php echo $_GET["project_ID"]?>)
</script>