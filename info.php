<?php
include "db_connection.php";
// phpinfo();
try {
    $conn = new PDO("mysql:host=localhost;dbname=make_it_all", $username, $password);
    $users = $conn->query("select * from users;");
    foreach($users as $row) {
        echo "<li>" . $row['first_name'] . ' ' . $row['surname'] . "</li>";
      }
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

?>