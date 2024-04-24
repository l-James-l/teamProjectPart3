<?php
include 'fetch_projects.php';
// SQL data collection


$userID = 1; // get userID from url later

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

$stmt->close();

$sql = "SELECT project_id, completion_percentage, est_length FROM task WHERE user_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('MySQL prepare error: ' . $conn->error);
}
$stmt->bind_param('i', $userID);
$stmt->execute();
$stmt->bind_result($projectID, $completionPercentage, $estimatedHours);

$completionPercentages = array();
$estimatedHoursArray = array();
$projectIDs = array();

while ($stmt->fetch()) {
    $completionPercentages[] = $completionPercentage;
    $estimatedHoursArray[] = $estimatedHours;
    $projectIDs[] = $projectID;
}

$stmt->close();

$completionSum = array_sum($completionPercentages);
if (count($completionPercentages) > 0) {
    $overallCompletion = $completionSum / count($completionPercentages);
} else {
    $overallCompletion = 0; 
}

$hoursDoneArray = array();
foreach ($completionPercentages as $index => $completionPercentage) {
    $hoursDoneArray[$index] = $completionPercentage * $estimatedHoursArray[$index];
    $hoursDone = array_sum($hoursDoneArray);
    $hoursLeft = array_sum($estimatedHoursArray) - $hoursDone;
}

$projectCount = count(array_unique($projectIDs));
$taskCount = count($completionPercentages);

$conn->close();

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project Analytics - Project ID <?php echo $projectID; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Project Dashboard</h1>
        <div class="alert alert-info">
            Total Task Completion: <?php echo round($overallCompletion, 2); ?>%
        </div>
        <div class="alert alert-warning">
            Remaining Assigned Hours: <?php echo round($hoursLeft, 2); ?>
        </div>
        <div class="mt-3">
            <h2>Tasks Details</h2>
            <?php foreach ($taskDetails as $task) { ?>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Task: <?php echo htmlspecialchars($task['task_title']); ?></h5>
                        <p class="card-text">Completion: <?php echo $task['completion_percentage']; ?>%</p>
                        <p class="card-text">Estimated Hours: <?php echo $task['estimated_hours']; ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function() {
    var projectID = <?php echo json_encode($projectID); ?>; // Get project ID from PHP

    $.ajax({
        url: 'fetch_projects.php', // Your back-end endpoint
        type: 'GET',
        data: {projectID: projectID},
        dataType: 'json',
        success: function(data) {
            if(data) {
                // Update your frontend elements like task details
                $.each(data.taskDetails, function(i, task) {
                    $('.task-container').append(`
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Task: ${task.task_title}</h5>
                                <p class="card-text">Completion: ${task.completion_percentage}%</p>
                                <p class="card-text">Estimated Hours: ${task.estimated_hours}</p>
                            </div>
                        </div>
                    `);
                });
                // Update other elements like completion and hours left
                $('.alert-info').text(`Total Task Completion: ${data.overallCompletion}%`);
                $('.alert-warning').text(`Remaining Assigned Hours: ${data.hoursLeft}`);
            }
        },
        error: function() {
            alert('Error loading data.');
        }
    });
});
</script>

</body>
</html>
