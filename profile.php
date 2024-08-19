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

$current_user = $_SESSION['username'];

// Fetch user profile details, including last updated time
$stmt = $conn->prepare("SELECT Username, StepsInTotal, StepsForDay, LastUpdated FROM Users WHERE Username = ?");
$stmt->bind_param("s", $current_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$current_time = new DateTime("now", new DateTimeZone("GMT"));
$last_updated_time = new DateTime($user['LastUpdated'], new DateTimeZone("GMT"));
$steps_today = $user['StepsForDay'];

// Reset StepsForDay if it's a new day (after 00:00 GMT)
if ($current_time->format('Y-m-d') !== $last_updated_time->format('Y-m-d')) {
    $stmt = $conn->prepare("UPDATE Users SET StepsForDay = 0 WHERE Username = ?");
    $stmt->bind_param("s", $current_user);
    $stmt->execute();
    $stmt->close();
    
    $steps_today = 0; // reset the variable for this session
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_steps_today = (int)($_POST['steps_today'] ?? 0);
    $new_steps_for_day = $steps_today + $new_steps_today;
    $new_steps_total = $user['StepsInTotal'] + $new_steps_today;

    // Update today's steps by adding the new steps
    $stmt = $conn->prepare("
        UPDATE Users
        SET StepsForDay = ?, StepsInTotal = ?, LastUpdated = ?
        WHERE Username = ?
    ");
    $current_time_str = $current_time->format('Y-m-d H:i:s');
    $stmt->bind_param("iiss", $new_steps_for_day, $new_steps_total, $current_time_str, $current_user);
    if (!$stmt->execute()) {
        die("Error updating steps: " . $stmt->error);
    }
    $stmt->close();

    $_SESSION['message'] = "Profile updated successfully.";
    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
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

        .profile-container {
            padding: 20px;
        }

        .group-container {
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
                <a href="profile.php" class="active">Profile</a>
                <a href="groups.php">Groups</a>
                <a href="requests.php">Requests</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="profile-container">
        <h1>User Profile</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <p class="message"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <h2>Profile Details</h2>
        <p>Username: <?php echo htmlspecialchars($user['Username']); ?></p>
        <p>Total Steps: <?php echo htmlspecialchars($user['StepsInTotal']); ?></p>

        <h2>Update Steps</h2>
        <form method="POST">
            <input type="hidden" name="update_profile">
            Steps Today: <input type="number" name="steps_today" value="<?php echo htmlspecialchars($steps_today); ?>" required><br>
            <input type="submit" value="Update Steps">
        </form>
    </div>
</body>
</html>
