<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "127.0.0.1";
$port = 3306;
$dbname = "";
$username = "";
$password = "";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    die("User not logged in.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'];
    $group_id = $_POST['group_id'];
    $requested_username = $_POST['requested_username'];

    if (isset($_POST['accept_request'])) {
        // Add user to the group
        $stmt = $conn->prepare("INSERT INTO GroupMembers (GroupID, Username) VALUES (?, ?)");
        $stmt->bind_param("is", $group_id, $requested_username);
        
        if ($stmt->execute()) {
            // Delete the request
            $stmt = $conn->prepare("DELETE FROM Requests WHERE RequestID = ?");
            $stmt->bind_param("i", $request_id);
            $stmt->execute();
            $stmt->close();

            $_SESSION['message'] = "User added to group.";
        } else {
            $_SESSION['message'] = "Failed to add user to group.";
        }
    } elseif (isset($_POST['reject_request'])) {
        // Delete the request
        $stmt = $conn->prepare("DELETE FROM Requests WHERE RequestID = ?");
        $stmt->bind_param("i", $request_id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Request rejected.";
        } else {
            $_SESSION['message'] = "Failed to reject the request.";
        }
    }

    header("Location: requests.php");
    exit();
}
?>
