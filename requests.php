<?php
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

// Handle request acceptance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_request'])) {
    $request_id = $_POST['request_id'];

    // Fetch the request details
    $stmt = $conn->prepare("
        SELECT GroupID, RequestedUsername
        FROM Requests
        WHERE RequestID = ?
    ");
    $stmt->bind_param("i", $request_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();
    $stmt->close();

    if ($request) {
        $group_id = $request['GroupID'];
        $requested_username = $request['RequestedUsername'];

        // Add user to the group
        $stmt = $conn->prepare("
            INSERT INTO GroupMembers (GroupID, Username)
            VALUES (?, ?)
        ");
        $stmt->bind_param("is", $group_id, $requested_username);
        if (!$stmt->execute()) {
            die("Error adding user to the group: " . $stmt->error);
        }
        $stmt->close();

        // Remove the request
        $stmt = $conn->prepare("
            DELETE FROM Requests
            WHERE RequestID = ?
        ");
        $stmt->bind_param("i", $request_id);
        if (!$stmt->execute()) {
            die("Error deleting request: " . $stmt->error);
        }
        $stmt->close();

        $_SESSION['message'] = "Request accepted successfully.";
    } else {
        die("Request not found.");
    }

    header("Location: requests.php");
    exit();
}

// Handle request rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_request'])) {
    $request_id = $_POST['request_id'];

    // Remove the request
    $stmt = $conn->prepare("
        DELETE FROM Requests
        WHERE RequestID = ?
    ");
    $stmt->bind_param("i", $request_id);
    if (!$stmt->execute()) {
        die("Error deleting request: " . $stmt->error);
    }
    $stmt->close();

    $_SESSION['message'] = "Request rejected successfully.";
    header("Location: requests.php");
    exit();
}

// Fetch pending requests
$stmt = $conn->prepare("
    SELECT r.RequestID, g.GroupName, r.Requester, r.RequestedUsername
    FROM Requests r
    JOIN Groups g ON r.GroupID = g.GroupID
    WHERE r.RequestedUsername = ?
");
$stmt->bind_param("s", $current_user);
$stmt->execute();
$result = $stmt->get_result();
$requests = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        .topnav {
            overflow: hidden;
            background-color: #333;
        }

        .topnav a {
            float: left;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            font-size: 17px;
        }

        .topnav a:hover {
            background-color: #ddd;
            color: black;
        }

        .topnav a.active {
            background-color: #04AA6D;
            color: white;
        }

        .request-container {
            border: 1px solid #ddd;
            margin-bottom: 10px;
            padding: 10px;
        }

        .message {
            color: green;
            font-weight: bold;
        }

        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="topnav">
        <div class="nav-links">
            <a href="index.php">Home</a>
            <?php if (isset($_SESSION['username'])): ?>
                <a href="profile.php">Profile</a>
                <a href="groups.php">Groups</a>
                <a href="requests.php" class="active">Requests</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <h1>Manage Requests</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <p class="message"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php if (empty($requests)): ?>
            <p>No requests available.</p>
        <?php else: ?>
            <?php foreach ($requests as $request): ?>
                <div class="request-container">
                    <div><strong>Group:</strong> <?php echo htmlspecialchars($request['GroupName']); ?></div>
                    <div><strong>Requester:</strong> <?php echo htmlspecialchars($request['Requester']); ?></div>
                    <div><strong>Requested User:</strong> <?php echo htmlspecialchars($request['RequestedUsername']); ?></div>

                    <!-- Accept request -->
                    <form method="POST" style="margin-top: 10px;">
                        <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['RequestID']); ?>">
                        <input type="submit" name="accept_request" value="Accept Request">
                    </form>

                    <!-- Reject request -->
                    <form method="POST" style="margin-top: 10px;">
                        <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($request['RequestID']); ?>">
                        <input type="submit" name="reject_request" value="Reject Request">
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
