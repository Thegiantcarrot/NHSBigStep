<?php
$host = "127.0.0.1";
$port = 3306;
$dbname = "u529174437_Walking";
$username = "u529174437_Alexfife";
$password = "DCboy2019";

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
