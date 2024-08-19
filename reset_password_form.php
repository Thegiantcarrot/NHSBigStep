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

// Validate reset token and show form
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Verify reset token
    $stmt = $conn->prepare("SELECT Email FROM PasswordReset WHERE ResetToken = ? AND ExpiryDate > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        die("Invalid or expired reset token.");
    }
    $email = $result->fetch_assoc()['Email'];
    $stmt->close();

    // Handle password reset
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $new_password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validate passwords
        if (empty($new_password) || $new_password !== $confirm_password) {
            $error = "Passwords do not match or are empty.";
        } else {
            // Hash the new password
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

            // Update user's password
            $stmt = $conn->prepare("UPDATE Users SET PasswordHash = ? WHERE Email = ?");
            $stmt->bind_param("ss", $password_hash, $email);
            
            if (!$stmt->execute()) {
                die("Error updating password: " . $stmt->error);
            }
            $stmt->close();

            // Delete reset token
            $stmt = $conn->prepare("DELETE FROM PasswordReset WHERE ResetToken = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $stmt->close();

            $success_message = "Password updated successfully.";
            header("Location: login.php");
        }
    }
} else {
    die("No reset token provided.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
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

        .reset-container {
            padding: 20px;
            max-width: 400px;
            margin: auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        .success {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="topnav">
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
            <a href="reset_password.php">Reset Password</a>
        </div>
    </div>

    <div class="reset-container">
        <h1>Reset Password</h1>
        
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif (isset($success_message)): ?>
            <p class="success"><?php echo htmlspecialchars($success_message); ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="password">New Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <input type="submit" value="Reset Password">
        </form>
    </div>
</body>
</html>
