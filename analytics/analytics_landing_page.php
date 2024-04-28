<?php
session_start();

if (isset($_GET["lf"])) {
    $lf = $_GET["lf"];
} else {
    header("location: ?lf=projects");
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="js/async_handlers.js"></script>


</head>

<body>
    <?php
    if (isset($_SESSION["user_id"])) {
        $currentPage = "analytics";
        include "../src/header.php";
    } else {
        header("location: login.php");
    }
    ?>

    <div>
        <div class="d-flex flex-column flex-shrink-0 p-3 bg-light sidebar">
            <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">

            </a>
            <hr>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="?lf=projects" class="nav-link <?php echo $lf == "projects" ? "active" : "link-dark" ?>" aria-current="page">
                        <i class="bi bi-folder-fill"></i>
                        Projects
                    </a>
                </li>
                <li>
                    <a href="?lf=users" class="nav-link <?php echo $lf == "users" ? "active" : "link-dark" ?>">
                        <i class="bi bi-people-fill"></i>
                        Users
                    </a>
                </li>

            </ul>
            <hr>

        </div>
        <div class="main-content-container">
            <h1><?php echo $lf ?></h1>

            <?php if ($lf == "projects") { ?>
            <div class="row" style="margin: auto;">
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
                
            </div>

            <?php } else if ($lf == "users") { ?>
            <div class="row" style="margin: auto;">
                <div class="col-6" style="padding-bottom: 10px; padding-left: unset">
                    <input id='searchbar' type="search" class="form-control" placeholder="Search..."
                        oninput="get_user_json()" aria-label="Search">
                </div>

                <div class="dropdown col-2" style="padding: 0px">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdownMenuButton"
                        data-bs-toggle="dropdown" data-bs-auto-close="outside" style="width: 90%;">Filter</button>
                        <div class="dropdown-menu" aria-labelledby="filterDropdownMenuButton">
                            <button class="dropdown-item d-flex justify-content-between" type="button" onclick="toggleFilter('Mgr');get_user_json()">
                                Managers <i id="MgrToggleIcon" class="bi bi-check"></i>
                            </button>
                            <input type="hidden" id="MgrToggleValue" value=1>
                            <button class="dropdown-item d-flex justify-content-between" type="button" onclick="toggleFilter('TL');get_user_json()">
                                Team Leaders <i id="TLToggleIcon" class="bi bi-check"></i>
                            </button>
                            <input type="hidden" id="TLToggleValue" value=1>
                            <button class="dropdown-item d-flex justify-content-between" type="button" onclick="toggleFilter('Emp');get_user_json()">
                                Employees <i id="EmpToggleIcon" class="bi bi-check"></i>
                            </button>
                            <input type="hidden" id="EmpToggleValue" value=1>
                        </div>
                </div>

                <div class="dropdown col-2" style="padding: 0px">
                    <button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdownMenuButton"
                        data-bs-toggle="dropdown" style="width: 90%;">Sort</button>
                    <div class="dropdown-menu" aria-labelledby="filterDropdownMenuButton">
                        <button class="dropdown-item" type="button"
                            onclick="change_sort_value('task_count');get_user_json()">On Going Tasks</button>
                        <button class="dropdown-item" type="button"
                            onclick="change_sort_value('first_name');get_user_json()">First Name</button>
                        <button class="dropdown-item" type="button"
                            onclick="change_sort_value('surname');get_user_json()">Surname</button>
                        <input type="hidden" id="sortValue" value="surname">
                    </div>
                </div>
                <button id="asc-button" class="btn btn-secondary col-1" style="height: fit-content;" 
                    onclick="change_sort_order('ASC');get_user_json()">
                    <i class="bi bi-sort-up"></i>
                </button>
                <button id="desc-button" class="btn btn-outline-secondary col-1" style="height: fit-content;"
                    onclick="change_sort_order('DESC');get_user_json()">
                    <i class="bi bi-sort-down"></i>
                </button>
                <input type="hidden" id="sortOrder" value="ASC">
            </div>
            <?php } ?>

            <div id="list_container">
                <?php
                if ($lf == "projects") {
                    echo "<script>get_project_json(".$_SESSION["user_id"].")</script>";
                } else if ($lf == "users") {
                    echo  "<script>get_user_json()</script>";
                }
                ?>
            </div>
        </div>
    </div>

</body>

</html>

