<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("location: login.php");
    exit();
}

// Get projectID from URL
$projectID = isset($_GET['projectID']) ? intval($_GET['projectID']) : 0; // ensure the ID is an integer

// DB connection 
$servername = "localhost";
$username = "phpUser";
$password = "p455w0rD";
$dbname = "make_it_all"; 
$connection = mysqli_connect($servername, $username, $password, $dbname);
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";

// Fetch project tasks
$sql = "SELECT task_id, completion_percentage, estimated_hours FROM tasks WHERE project_id = ?";
$stmt = mysqli_prepare($connection, $sql);
if ($stmt === false) {
    die('MySQL prepare error: ' . mysqli_error($connection));
}
mysqli_stmt_bind_param($stmt, 'i', $projectID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$completionPercentages = [];
$estimatedHoursArray = [];
$taskDetails = [];

while ($row = mysqli_fetch_assoc($result)) {
    $completionPercentages[] = $row['completion_percentage'];
    $estimatedHoursArray[] = $row['estimated_hours'];
    $taskDetails[] = $row; // Store entire row for later use
}

mysqli_stmt_close($stmt);

// Calculations for task completion and hours left
$completionSum = array_sum($completionPercentages);
$totalTasks = count($completionPercentages);
$overallCompletion = $totalTasks > 0 ? $completionSum / $totalTasks : 0;

$hoursLeft = 0;
foreach ($estimatedHoursArray as $index => $hours) {
    $completedHours = ($completionPercentages[$index] / 100) * $hours;
    $hoursLeft += ($hours - $completedHours); // Total uncompleted hours
}

// Close DB connection
mysqli_close($connection);

