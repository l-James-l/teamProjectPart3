<?php

// Connect to your MySQL database
$servername = "localhost";
$username = "phpUser";
$password = "p455w0rD";
$dbname = "make_it_all"; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error: Could not connect. " . $e->getMessage());
}

// Define a function to get group members
function getGroupMembers($chatId, $pdo) {
    $query = "SELECT u.first_name, u.surname
              FROM users u
              JOIN chat_relation cr ON u.user_id = cr.user_id
              WHERE cr.chat_id = :chatId";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':chatId', $chatId, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

// Define a function to check if it's a group chat
function isGroupChat($chatId, $pdo) {
    $query = "SELECT is_group FROM chat WHERE chat_id = :chatId";

    $statement = $pdo->prepare($query);
    $statement->bindParam(':chatId', $chatId, PDO::PARAM_INT);
    $statement->execute();

    $result = $statement->fetch(PDO::FETCH_ASSOC);
    return $result['is_group'] == 1;
}

// Usage example
$chatId = 4;

if (isGroupChat($chatId, $pdo)) {
    $groupMembers = getGroupMembers($chatId, $pdo);
    echo "Group Members:\n";
    foreach ($groupMembers as $member) {
        echo $member['first_name'] . ' ' . $member['surname'] . "\n";
    }
} else {
    echo "This is not a group chat.\n";
}

?>
