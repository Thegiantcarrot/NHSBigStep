<?php
$host = "127.0.0.1";
$port = 3306;
$dbname = "";
$username = "";
$password = "";
//Set up a cron job to reset the steps every day
// Create connection
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute the update statement
$sql = "UPDATE Users SET StepsForDay = 0";
if ($conn->query($sql) === TRUE) {
    echo "StepsForDay reset successfully.";
} else {
    echo "Error resetting StepsForDay: " . $conn->error;
}

$conn->close();
?>
