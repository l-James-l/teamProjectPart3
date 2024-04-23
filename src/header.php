<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("location: login.php");
} 

if (!isset($currentPage)) {
    header("location: analytics_landing_page.php");
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make It All</title>
	<link rel="icon" type="image/x-icon" href="./imgs/logo.ico">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../src/stylesheets/header.css">
</head>

<body>
    <header>
        <div class="container header-container">
            <img src="./imgs/logo.png" alt="Company Logo" id="page-logo">

            <ul style="display: flex;">
                <li class="sub-system-link <?php echo $currentPage=='analytics' ? 'current-page-li': '' ?>">
                    <a class="headerLink <?php echo $currentPage=='analytics' ? 'current-page': 'default' ?>" href="../analytics_landing_page.php?lf=projects">Analytics</a>
                </li>
                <li class="sub-system-link <?php echo $currentPage=='chat' ? 'current-page': '' ?>">
                    <a class="headerLink <?php echo $currentPage=='chat' ? 'current-page': 'default' ?>" href="../textchat/index2.html.php">Chat</a>
                </li>
            </ul>



            <div style="width: -webkit-fill-available;"></div>

            <div class=" dropdown">
                <a href="#" class="d-block link-dark text-decoration-none user-dropdown dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="./imgs/icon.png" alt="mdo" width="42" height="42" class="rounded-circle">
                </a>
                <ul class="dropdown-menu text-small" aria-labelledby="dropdownUser1">
                    <li>
                        <div class="dropdown-item dropdown-item-nohover" >
                            <div style="white-space: normal;">
                                <img src="./imgs/icon.png" alt="mdo" width="32" height="32" class="rounded-circle">
                                <span style="padding-left: 10px;"><?php echo $_SESSION["first_name"] . " " . $_SESSION["surname"]?></span>
                            </div>
                            <span><?php echo $_SESSION["email"]?></span>
                        </div>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="login.php">Sign out</a></li>
                </ul>
            </div>
        </div>
    </header>
</body>

</html>