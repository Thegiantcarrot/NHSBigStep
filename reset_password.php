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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    // Validate email
    if (empty($email)) {
        die("Email is required.");
    }

    // Check if email exists
    $stmt = $conn->prepare("SELECT Username FROM Users WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        die("No user found with this email.");
    }

    // Generate a reset token
    $resetToken = bin2hex(random_bytes(32));
    $expiryDate = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Insert reset request into the database
    $stmt = $conn->prepare("
        INSERT INTO PasswordReset (Email, ResetToken, ExpiryDate)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("sss", $email, $resetToken, $expiryDate);

    if (!$stmt->execute()) {
        die("Error processing request: " . $stmt->error);
    }
    $stmt->close();

    // Send email with the reset link (assuming mail setup is correct)
    $resetLink = "http://walking.over-and-out.org/reset_password_form.php?token=$resetToken";
    $subject = "Password Reset Request";
    $message = "To reset your password, please visit the following link: $resetLink";
    $headers = "From: no-reply@walking.over-and-out.org";

    if (mail($email, $subject, $message, $headers)) {
        echo "Password reset link has been sent to your email.";
    } else {
        echo "Failed to send email.";
    }
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
    </style>
</head>
<body>
    <div class="topnav">
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
            <a href="reset_password.php" class="active">Reset Password</a>
        </div>
    </div>

    <div class="reset-container">
        <h1>Reset Password</h1>
        <form method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <input type="submit" value="Send Reset Link">
        </form>
    </div>
</body>
</html>
