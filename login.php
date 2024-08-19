<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background-color: #f0f4f7;
        }

        /* Navbar styles */
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

        /* Login container styles */
        .login-container {
            background-color: #ffffff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
            width: 300px;
            margin: 50px auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        input[type="submit"] {
            width: 100%;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-top: 10px;
        }

        .forgot-password {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="topnav">
        <div class="nav-links">
            <a href="index.php">Home</a>
            <?php if (isset($_SESSION['username'])): ?>
                <a href="profile.php">Profile</a>
                <a href="groups.php">Groups</a>
                <a href="requests.php">Requests</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php" class="active">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Login Form -->
    <div class="login-container">
        <h2>Login</h2>
        <?php
        session_start(); // Start the session

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

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $login_input = $_POST['login_input'];
            $pass = $_POST['password'];

            // Prepare and execute the query to get the hashed password
            $stmt = $conn->prepare("
                SELECT Username, PasswordHash
                FROM Users
                WHERE Username = ? OR Email = ?
            ");
            if ($stmt === false) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("ss", $login_input, $login_input);
            $stmt->execute();
            $stmt->bind_result($username, $hashed_password);
            $stmt->fetch();

            // Verify the password and start the session if valid
            if (password_verify($pass, $hashed_password)) {
                $_SESSION['username'] = $username;
                header("Location: profile.php");
                exit(); // Ensure no further code is executed after the redirect
            } else {
                echo "<div class='error-message'>Invalid username, email, or password</div>";
            }

            $stmt->close();
        }

        $conn->close();
        ?>

        <form method="POST" action="login.php">
            <input type="text" name="login_input" placeholder="Username or Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
        <a class="forgot-password" href="reset_password.php">Forgot Password?</a>
    </div>
</body>
</html>
