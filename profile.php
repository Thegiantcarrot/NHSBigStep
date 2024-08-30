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
    $new_steps_today = (int)$_POST['steps_today'];
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

    // Redirect to the same page to reflect the updated values
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
    <link rel="stylesheet" href="https://unpkg.com/@pqina/flip/dist/flip.min.css">
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

        .tick {
            font-size: 1rem;
            white-space: nowrap;
            font-family: Arial, sans-serif;
        }

        .tick-flip, .tick-text-inline {
            font-size: 2.5em;
        }

        .tick-label {
            margin-top: 1em;
            font-size: 1em;
        }

        .tick-char {
            width: 1.5em;
        }

        .tick-text-inline {
            display: inline-block;
            text-align: center;
            min-width: 1em;
        }

        .tick-text-inline + .tick-text-inline {
            margin-left: -0.325em;
        }

        .tick-group {
            margin: 0 .5em;
            text-align: center;
        }

        body {
            background-color: #fff !important;
        }

        .tick-text-inline {
            color: #595d63 !important;
        }

        .tick-label {
            color: #595d63 !important;
        }

        .tick-flip-panel {
            color: #fff !important;
        }

        .tick-flip {
            font-family: Arial, sans-serif !important;
        }

        .tick-flip-panel-text-wrapper {
            line-height: 1.45 !important;
        }

        .tick-flip-panel {
            background-color: rgb(25, 78, 176) !important;
        }

        .tick-flip {
            border-radius: 0.17em !important;
        }

        .counter1 {
            padding: 5px;
            max-width: 30%;
            max-height: 12%;
            margin: 0 auto;
            text-align: center;
        }
        /* Add this to your CSS file */
.share-container {
    margin-top: 20px;
    text-align: center;
}

#share-btn {
    background-color: #04AA6D;
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px;
}

#share-btn:hover {
    background-color: #039e5a;
}

    </style>
</head>
<body>
    <!-- Include Tick.js from CDN -->
    <script src="https://unpkg.com/@pqina/flip/dist/flip.min.js"></script>

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
        <p>Steps Today:</p>
        <div class="counter1">
            <div id="tick" class="tick" data-value="<?php echo $steps_today; ?>" data-did-init="handleTickInit">
                <span data-layout="horizontal fit">
                    <span data-repeat="true" data-transform="arrive(200, .001) -> round -> split -> delay(rtl, 100, 150,)">
                        <span data-view="flip"></span>
                    </span>
                </span>
            </div>
        </div>

        <h2>Update Steps</h2>
        <form id="update-steps-form" method="POST">
            <input type="hidden" name="update_profile" value="1">
            Steps Today: <input type="number" id="steps_today" name="steps_today" required><br>
            <input type="submit" value="Update Steps">
        </form>
    </div>
    <!-- Add this within your HTML content where you want the button to appear -->
<div class="share-container">
    <button id="share-btn">Share My Steps</button>
</div>


    <script>
document.addEventListener('DOMContentLoaded', function() {
    var tickElement = document.querySelector('#tick');
    var form = document.querySelector('#update-steps-form');
    var stepsInput = document.querySelector('#steps_today');

    // Check if the tickElement exists before proceeding
    if (tickElement) {
        // Initialize the Tick Flip object using the element's data-value attribute
        var tick = Tick.DOM.create(tickElement, {
            didInit: function(tickInstance) {
                // Set initial value based on the data-value attribute
                var initialValue = parseInt(tickElement.getAttribute('data-value'));
                if (!isNaN(initialValue)) {
                    tickInstance.value = initialValue;
                } else {
                    console.error("Initial value is not a valid number.");
                }
            }
        });

        // Form submit event listener
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Get the current value displayed in the tick element
            var oldValue = parseInt(tickElement.getAttribute('data-value'));
            var newSteps = parseInt(stepsInput.value);

            if (!isNaN(oldValue) && !isNaN(newSteps)) {
                var newValue = oldValue + newSteps;

                // Update the data-value attribute to the new value
                tickElement.setAttribute('data-value', newValue);

                // Update the Tick instance value to trigger the flip animation
                if (tick) {
                    tick.value = newValue; // Trigger the animation
                } else {
                    console.error("Tick object is not defined.");
                }

                // Determine animation duration based on the length of the new value
                var numberLength = newValue.toString().length;
                var animationDuration = 700 + (numberLength * 500); // Base duration + duration per digit

                // Wait for the animation to complete before submitting the form
                setTimeout(function() {
                    form.submit();
                }, animationDuration); // Submit after animation completes
            } else {
                console.error("Invalid step values. Please ensure the values are numbers.");
            }
        });
    } else {
        console.error("Tick element not found.");
    }
});
</script>
<script>document.addEventListener('DOMContentLoaded', function() {
    var shareButton = document.querySelector('#share-btn');
    var stepsElement = document.querySelector('#tick');
    
    // Check if the share button and steps element exist before proceeding
    if (shareButton && stepsElement) {
        // Retrieve the steps value
        var steps = parseInt(stepsElement.getAttribute('data-value')) || 0;

        shareButton.addEventListener('click', function() {
            var message = `I have just reached ${steps} steps with the NHS Big Step Challenge!`;

            // Twitter sharing URL
            var twitterUrl = `https://twitter.com/intent/tweet?text=${encodeURIComponent(message)}`;

            // Facebook sharing URL
            var facebookUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(location.href)}&quote=${encodeURIComponent(message)}`;

            // Open the sharing URL in a new window
            window.open(twitterUrl, '_blank', 'width=600,height=400'); // Twitter share
            // or use Facebook sharing
            // window.open(facebookUrl, '_blank', 'width=600,height=400'); // Facebook share
        });
    } else {
        console.error("Share button or steps element not found.");
    }
});
</script>
</body>
</html>
