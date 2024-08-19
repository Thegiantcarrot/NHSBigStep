<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Home - The Big Step</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            color: #333;
        }

        /* Navbar styles */
        .topnav {
            background-color: #333;
            overflow: hidden;
        }

        .topnav a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
            font-size: 18px;
            transition: background-color 0.3s;
        }

        .topnav a:hover {
            background-color: #005bb5;
            color: white;
        }

        .topnav a.active {
            background-color: #04AA6D;
            color: white;
        }

        /* Hero section */
        .container1 {
            background-color:blue;
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: yellow;
        }

        .container1 h1 {
            font-size: 50px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .container1 p {
            font-size: 20px;
            margin-bottom: 30px;
            max-width: 600px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }

        .container1 a {
            background-color: #0073e6;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            font-size: 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .container1 a:hover {
            background-color: #005bb5;
              transition: width 0.5s;
        }

        /* Main Content */
        main {
            padding: 20px;
        }

        main .welcome {
            text-align: center;
            margin-top: 20px;
        }

        main .welcome p {
            font-size: 18px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="topnav">
        <div class="nav-links">
            <a href="index.php" class="active">Home</a>
            <?php if (isset($_SESSION['username'])): ?>
                <a href="profile.php">Profile</a>
                <a href="groups.php">Groups</a>
                <a href="requests.php">Requests</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
<div class="containera"><img src="https://images.prismic.io/nhscharitiestogether-thebigstep/5ebef592-8dfe-44be-ae6a-053be4883001_Big-Step-Homepage-Header.gif?&rect=0,0,1480,450&w=1480&h=450"></div>
    <!-- Hero Section -->
    <div class="container1">
        <div>
            <h1>STEP FOR THE NHS</h1>
            <p>Step during October. Boost your health. Raise funds to ensure our NHS can thrive.</p>
            <?php if (isset($_SESSION['username'])): ?>
                <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
                <p><a href="profile.php">Go to your profile</a></p>
            <?php else: ?>
                <p><a class="btn" href="register.php">Get Started</a></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content -->
    <main>
        <div class="welcome">
            <p>Welcome to the NHS Walking challenge. Stay fit, stay healthy, and support the NHS!</p>
        </div>
    </main>
</body>
</html>
