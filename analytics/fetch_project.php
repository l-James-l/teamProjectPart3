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
// Respond to AJAX requests
if (isset($_GET['project_id']) && !empty($_GET['project_id'])) {
    $projectId = $_GET['project_id'];

    // Initialize the response array
    $response = [];

    // Fetch tasks for the selected project
    $taskQuery = "SELECT task_title, due_date, est_length, priority, completion_percentage FROM Task WHERE project_id = ?";
    $stmt = $conn->prepare($taskQuery);
    $stmt->bind_param('i', $projectId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($task = $result->fetch_assoc()) {
            $response[] = [
                'task_title' => htmlspecialchars($task['task_title']),
                'due_date' => htmlspecialchars($task['due_date']),
                'est_length' => htmlspecialchars($task['est_length']),
                'priority' => htmlspecialchars($task['priority']),
                'completion_percentage' => htmlspecialchars($task['completion_percentage'])
            ];
        }
    } else {
        $response['error'] = 'No tasks found for this project.';
    }
    $stmt->close();
    // Encode response as JSON and output it
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    echo 'Invalid request';
}
$conn->close();

