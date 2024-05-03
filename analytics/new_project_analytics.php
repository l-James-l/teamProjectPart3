
<?php
session_start();

if (isset($_GET["page"]) && isset($_GET["projectToGet"])) {
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="js/project_analytics.js"></script>
    
    <script src="https://www.gstatic.com/charts/loader.js"></script>

</head>

<body>
    <?php
    if (isset($_SESSION["user_id"])) {
        $currentPage = "analytics";
        include "../src/header.php";
    } else {
        header("location: ../src/login.php");
    }
    ?>
    

    <div>
        <div class="d-flex flex-column flex-shrink-0 p-3 bg-light sidebar">
            <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">

            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="?projectToGet=<?php echo $_GET['projectToGet']?>&page=overview" class="nav-link <?php echo $page == "overview" ? "active" : "link-dark" ?>" aria-current="page">
                        <i class="bi bi-folder-fill"></i>
                        Overview
                    </a>
                </li>
                <li>
                    <a href="?projectToGet=<?php echo $_GET['projectToGet']?>&page=users" class="nav-link <?php echo $page == "users" ? "active" : "link-dark" ?>">
                        <i class="bi bi-people-fill"></i>
                        Users Assignment
                    </a>
                </li>
                <li>
                    <a href="?projectToGet=<?php echo $_GET['projectToGet']?>&page=progress" class="nav-link <?php echo $page == "progress" ? "active" : "link-dark" ?>">
                        <i class="bi bi-people-fill"></i>
                        Progression
                    </a>
                </li>

            </ul>
            <hr>

        </div>
        <div class="main-content-container">
            <h1 id="project_title"></h1>
            <?php if ($page == "overview") { ?>
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
                        <!-- search bar -->
                        <div class="col-6" style="padding-bottom: 10px; padding-left: unset">
                            <input id='searchbar' type="search" class="form-control" placeholder="Search..."
                                oninput="get_project_from_api(<?php echo $_GET['projectToGet']?>, 'overview')" aria-label="Search">
                        </div>

                        <!-- filter dropdown -->
                        <div class="dropdown col-2" style="padding: 0px">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdownMenuButton"
                                data-bs-toggle="dropdown" data-bs-auto-close="outside" style="width: 90%;">Filter</button>
                                <div class="dropdown-menu" aria-labelledby="filterDropdownMenuButton">
                                    <button class="dropdown-item d-flex justify-content-between" type="button" onclick="toggleFilter('milestone');get_project_from_api(<?php echo $_GET['projectToGet']?>, 'overview')">
                                        Milestones Only <i id="milestoneToggleIcon" class="bi bi-x"></i>
                                    </button>
                                    <input type="hidden" id="milestoneToggleValue" value=0>
                                    <button class="dropdown-item d-flex justify-content-between" type="button" onclick="toggleFilter('complete');get_project_from_api(<?php echo $_GET['projectToGet']?>, 'overview')">
                                        Show Completed <i id="completeToggleIcon" class="bi bi-x"></i>
                                    </button>
                                    <input type="hidden" id="completeToggleValue" value=0>
                                    <button class="dropdown-item d-flex justify-content-between" type="button" onclick="toggleFilter('incomplete');get_project_from_api(<?php echo $_GET['projectToGet']?>, 'overview')">
                                        Show Incomplete <i id="incompleteToggleIcon" class="bi bi-check"></i>
                                    </button>
                                    <input type="hidden" id="incompleteToggleValue" value=1>
                                </div>
                        </div>

                        <!-- sort order dropdown -->
                        <div class="dropdown col-2" style="padding: 0px">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdownMenuButton"
                                data-bs-toggle="dropdown" style="width: 90%;">Sort</button>
                            <div class="dropdown-menu" aria-labelledby="filterDropdownMenuButton">
                                <button class="dropdown-item" type="button"
                                    onclick="change_sort_value('due_date');get_project_from_api(<?php echo $_GET['projectToGet']?>, 'overview')">Due Date</button>
                                <button class="dropdown-item" type="button"
                                    onclick="change_sort_value('priority');get_project_from_api(<?php echo $_GET['projectToGet']?>, 'overview')">Priority</button>
                                <button class="dropdown-item" type="button"
                                    onclick="change_sort_value('est_length');get_project_from_api(<?php echo $_GET['projectToGet']?>, 'overview')">Hours</button>
                                <button class="dropdown-item" type="button"
                                    onclick="change_sort_value('completion_percentage');get_project_from_api(<?php echo $_GET['projectToGet']?>, 'overview')">Completion</button>
                                <input type="hidden" id="sortValue" value="due_date">
                            </div>
                        </div>
                        <!-- ASC DESC buttons -->
                        <button id="asc-button" class="btn btn-secondary col-1" style="height: fit-content;" 
                            onclick="change_sort_order('ASC');get_project_from_api(<?php echo $_GET['projectToGet']?>, 'overview')">
                            <i class="bi bi-sort-up"></i>
                        </button>
                        <button id="desc-button" class="btn btn-outline-secondary col-1" style="height: fit-content;"
                            onclick="change_sort_order('DESC');get_project_from_api(<?php echo $_GET['projectToGet']?>, 'overview')">
                            <i class="bi bi-sort-down"></i>
                        </button>
                        <input type="hidden" id="sortOrder" value="ASC">
                    </div>

                    <div id="task-container" class="task-container">
                        <!-- filled with JS -->
                    </div>
                </div>
            </div>

            <?php } else if ($page == "users") {?>
                <!-- <div id="dual_x_div" style="width: -webkit-fill-available; height:400px"></div> -->
                <div style="padding-bottom: 10px; padding-left: unset">
                    <input id='searchbar' type="search" class="form-control" placeholder="Search for tasks or users. To search for multiple seperate terms with a space."
                        oninput="get_project_from_api(<?php echo $_GET['projectToGet']?>, 'users')" aria-label="Search">
                </div>
                <div class="accordion" id="users_graphs_container"></div>
            <?php } else if ($page == "progress") {?>
                <div id="progress_line_chart" style="width: -webkit-fill-available; height:400px"></div>
            <?php }?>
       
            
        </div>
    </div>

</body>

</html>



<script>
    get_project_from_api(<?php echo $_GET["projectToGet"]?>, "<?php echo $page?>")
</script>
