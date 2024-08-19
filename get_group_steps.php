<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "127.0.0.1";
$port = 3306;
$dbname = "";
$username = "";
$password = "";

//Create connection
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the group ID is provided
if (!isset($_GET['group_id'])) {
    echo json_encode(['error' => 'No group ID provided.']);
    exit();
}

$group_id = $_GET['group_id'];

// Prepare SQL query to get the total steps of the group
$stmt = $conn->prepare("
    SELECT SUM(u.StepsInTotal) as GroupTotalSteps
    FROM Users u 
    INNER JOIN GroupMembers gm ON u.Username = gm.Username
    WHERE gm.GroupID = ?
");
$stmt->bind_param("i", $group_id);
$stmt->execute();
$result = $stmt->get_result();
$total_steps = $result->fetch_assoc()['GroupTotalSteps'] ?? 0;
$stmt->close();

echo json_encode(['total_steps' => $total_steps]);

$conn->close();
?>
