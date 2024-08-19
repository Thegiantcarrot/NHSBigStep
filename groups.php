<?php
include 'navbar.php';
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

// Fetch groups the user is a member of
$stmt = $conn->prepare("
    SELECT g.GroupID, g.GroupName, g.creator,
        (SELECT SUM(u.StepsInTotal)
         FROM Users u 
         INNER JOIN GroupMembers gm ON u.Username = gm.Username
         WHERE gm.GroupID = g.GroupID
        ) AS GroupTotalSteps
    FROM Groups g
    INNER JOIN GroupMembers gm ON g.GroupID = gm.GroupID
    WHERE gm.Username = ?
");
$stmt->bind_param("s", $current_user);
$stmt->execute();
$result = $stmt->get_result();
$groups = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle group deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_group'])) {
    $group_id_to_delete = $_POST['group_id'];

    // Check if the current user is the creator of the group
    $stmt = $conn->prepare("SELECT creator FROM Groups WHERE GroupID = ? AND creator = ?");
    $stmt->bind_param("is", $group_id_to_delete, $current_user);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();

        // Delete the group
        $stmt = $conn->prepare("DELETE FROM Groups WHERE GroupID = ?");
        $stmt->bind_param("i", $group_id_to_delete);
        $stmt->execute();
        $stmt->close();

        $_SESSION['message'] = "Group deleted successfully.";
        header("Location: groups.php");
        exit();
    } else {
        $_SESSION['error'] = "You do not have permission to delete this group.";
    }

    $stmt->close();
}

// Handle request sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_request'])) {
    $group_id = $_POST['group_id'];
    $receiver_username = $_POST['receiver_username'];

    // Check if the GroupID exists in the Groups table
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS GroupExists 
        FROM Groups 
        WHERE GroupID = ?
    ");
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $group_exists = $result->fetch_assoc()['GroupExists'];
    $stmt->close();

    if ($group_exists > 0) {
        // Check if the user is already a member of the group
        $stmt = $conn->prepare("
            SELECT COUNT(*) AS IsMember
            FROM GroupMembers
            WHERE GroupID = ? AND Username = ?
        ");
        $stmt->bind_param("is", $group_id, $receiver_username);
        $stmt->execute();
        $result = $stmt->get_result();
        $is_member = $result->fetch_assoc()['IsMember'];
        $stmt->close();

        if ($is_member > 0) {
            $_SESSION['error'] = "User is already a member of the group.";
            header("Location: groups.php");
            exit();
        }

        // Check if the user already has a pending request
        $stmt = $conn->prepare("
            SELECT COUNT(*) AS RequestExists
            FROM Requests
            WHERE GroupID = ? AND Requester = ? AND RequestedUsername = ?
        ");
        $stmt->bind_param("iss", $group_id, $current_user, $receiver_username);
        $stmt->execute();
        $result = $stmt->get_result();
        $request_exists = $result->fetch_assoc()['RequestExists'];
        $stmt->close();

        if ($request_exists > 0) {
            $_SESSION['error'] = "A request to this user is already pending.";
            header("Location: groups.php");
            exit();
        }

        // Send the join request
        $stmt = $conn->prepare("
            INSERT INTO Requests (GroupID, Requester, RequestedUsername)
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iss", $group_id, $current_user, $receiver_username);
        if (!$stmt->execute()) {
            $_SESSION['error'] = "Failed to send request: " . $stmt->error;
            header("Location: groups.php");
            exit();
        }
        $stmt->close();

        $_SESSION['message'] = "Request sent successfully.";
        header("Location: groups.php");
        exit();
    } else {
        $_SESSION['error'] = "Group does not exist.";
        header("Location: groups.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://unpkg.com/@pqina/flip/dist/flip.min.css">
    <script src="https://unpkg.com/@pqina/flip/dist/flip.min.js"></script>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Groups</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>


        /* Modal styles */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
        }

        .modal-content {
            background-color: #fefefe; 
            margin: 15% auto; 
            padding: 20px; 
            border: 1px solid #888; 
            width: 80%; 
        }

        .close {
            color: #aaa; 
            float: right; 
            font-size: 28px; 
            font-weight: bold; 
        }

        .close:hover,
        .close:focus {
            color: black; 
            text-decoration: none; 
            cursor: pointer; 
        }
    </style>
    <style>
.tick {
  font-size:1rem; white-space:nowrap; font-family:arial,sans-serif;
}

.tick-flip,.tick-text-inline {
  font-size:2.5em;
}

.tick-label {
  margin-top:1em;font-size:1em;
}

.tick-char {
  width:1.5em;
}

.tick-text-inline {
  display:inline-block;text-align:center;min-width:1em;
}

.tick-text-inline+.tick-text-inline {
  margin-left:-.325em;
}

.tick-group {
  margin:0 .5em;text-align:center;
}


.tick-text-inline {
   color: rgb(90, 93, 99) !important; 
}

.tick-label {
   color: rgb(90, 93, 99) !important; 
}

.tick-flip-panel {
   color: rgb(255, 255, 255) !important; 
}

.tick-flip {
   font-family: !important; 
}

.tick-flip-panel-text-wrapper {
   line-height: 1.45 !important; 
}

.tick-flip-panel {
   background-color: rgb(25, 78, 176) !important; 
}

.tick-flip {
   border-radius:0.12em !important; 
}
          .GFG {
                background-color: white;
                border: 2px solid black;
                color: green;
                padding: 5px 10px;
                cursor: pointer;
            }
</style>
</head>
<body>
    <h1>Your Groups</h1>
    <button onclick="window.location.href='create_group.php';" class="GFG">Create a group</button>
    <?php if (isset($_SESSION['message'])): ?>
        <p class="message"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <p class="error"><?php echo htmlspecialchars($_SESSION['error']); ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (empty($groups)): ?>
        <p>You are not a member of any groups.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Group Name</th>
                    <th>Total Steps</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($groups as $group): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($group['GroupName']); ?></td>
                        <td>
                            <div class="tick" id="flip-<?php echo $group['GroupID']; ?>" data-did-init="handleTickInit<?php echo $group['GroupID']; ?>">
                                <span data-layout="horizontal fit">
                                    <span data-repeat="true" data-transform="arrive(200, .001) -> round -> split -> delay(rtl, 100, 150,)">
                                        <span data-view="flip"></span>
                                    </span>
                                </span>
                            </div>
                        </td>
                        <td>
                            <button onclick="openModal(<?php echo $group['GroupID']; ?>)">View Leaderboard</button>
                            <?php if ($group['creator'] === $current_user): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="group_id" value="<?php echo $group['GroupID']; ?>">
                                    <input type="submit" name="delete_group" value="Delete Group">
                                </form>
                            <?php endif; ?>
                            <button onclick="showRequestForm(<?php echo $group['GroupID']; ?>)">Send Join Request</button>
                        </td>
                    </tr>

                    <script>
                        function handleTickInit<?php echo $group['GroupID']; ?>(tick) {
    // Start the counter from GroupTotalSteps - 200
    tick.value = <?php echo $group['GroupTotalSteps']; ?> * 0.95;
    
    // Animate to the actual GroupTotalSteps value after a delay
    setTimeout(function() {
        tick.value = <?php echo $group['GroupTotalSteps']; ?>;
    }, 100); // Adjust the delay if needed
}


                        function showRequestForm(groupID) {
                            document.getElementById('requestGroupID').value = groupID;
                            document.getElementById('requestFormModal').style.display = 'block';
                        }

                        function closeRequestForm() {
                            document.getElementById('requestFormModal').style.display = 'none';
                        }
                    </script>

                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- The Request Form Modal -->
    <div id="requestFormModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeRequestForm()">&times;</span>
            <h2>Send Join Request</h2>
            <form method="POST">
                <input type="hidden" name="group_id" id="requestGroupID">
                User to Invite: <input type="text" name="receiver_username" required><br>
                <input type="submit" name="send_request" value="Send Request">
            </form>
        </div>
    </div>

    <!-- The Modal for Leaderboard -->
    <div id="leaderboardModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeLeaderboardModal()">&times;</span>
            <div id="leaderboardContent">Loading...</div>
        </div>
    </div>

    <script>
    // Get the leaderboard modal
    var leaderboardModal = document.getElementById("leaderboardModal");

    // Function to open leaderboard modal
    function openModal(groupID) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'leaderboard.php?group_id=' + groupID, true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                document.getElementById("leaderboardContent").innerHTML = xhr.responseText;
                leaderboardModal.style.display = "block";
            } else {
                alert('Failed to load leaderboard.');
            }
        };
        xhr.onerror = function () {
            alert('Error making the request.');
        };
        xhr.send();
    }

    // Get the close button for leaderboard modal
    var closeLeaderboardModal = function() {
        leaderboardModal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modals, close them
    window.onclick = function(event) {
        if (event.target == leaderboardModal || event.target == document.getElementById('requestFormModal')) {
            leaderboardModal.style.display = "none";
            document.getElementById('requestFormModal').style.display = "none";
        }
    }
</script>
</body>
</html>
