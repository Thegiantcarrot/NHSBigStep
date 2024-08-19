<?php
include 'navbar.php';
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Ensure the user is logged in
if (!isset($_SESSION['username'])) {
    die("User not logged in.");
}

$current_user = $_SESSION['username'];

// Handle group creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_group'])) {
    $group_name = $_POST['group_name'];

    // Check if a group with the same name already exists
    $stmt = $conn->prepare("SELECT COUNT(*) AS GroupExists FROM Groups WHERE GroupName = ?");
    $stmt->bind_param("s", $group_name);
    $stmt->execute();
    $result = $stmt->get_result();
    $group_exists = $result->fetch_assoc()['GroupExists'];
    $stmt->close();

    if ($group_exists > 0) {
        $_SESSION['error'] = "A group with that name already exists.";
        header("Location: create_group.php");
        exit();
    }

    // Insert the new group into the database
    $stmt = $conn->prepare("INSERT INTO Groups (GroupName, creator) VALUES (?, ?)");
    $stmt->bind_param("ss", $group_name, $current_user);

    if ($stmt->execute()) {
        // Get the ID of the newly created group
        $group_id = $stmt->insert_id;

        // Add the creator as a member of the group
        $stmt = $conn->prepare("INSERT INTO GroupMembers (GroupID, Username) VALUES (?, ?)");
        $stmt->bind_param("is", $group_id, $current_user);
        $stmt->execute();
        $stmt->close();

        $_SESSION['message'] = "Group created successfully.";
        header("Location: groups.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to create group: " . $stmt->error;
        header("Location: create_group.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Group</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <h1>Create a New Group</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <p class="message"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <p class="error"><?php echo htmlspecialchars($_SESSION['error']); ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form method="POST">
        <label for="group_name">Group Name:</label>
        <input type="text" name="group_name" id="group_name" required>
        <br>
        <input type="submit" name="create_group" value="Create Group">
    </form>
</body>
</html>
