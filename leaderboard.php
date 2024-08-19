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
$group_id = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;

// Fetch group members and their steps
$stmt = $conn->prepare("
    SELECT u.Username, u.StepsForDay, u.StepsInTotal
    FROM Users u
    INNER JOIN GroupMembers gm ON u.Username = gm.Username
    WHERE gm.GroupID = ?
    ORDER BY u.StepsInTotal DESC
");
$stmt->bind_param("i", $group_id);
$stmt->execute();
$result = $stmt->get_result();
$leaderboard = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (empty($leaderboard)) {
    echo "<p>No data available for this leaderboard.</p>";
} else {
    echo "<h1>Leaderboard</h1>";
    echo "<table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Steps Today</th>
                    <th>Total Steps</th>
                </tr>
            </thead>
            <tbody>";
    foreach ($leaderboard as $user) {
        echo "<tr>
                <td>" . htmlspecialchars($user['Username']) . "</td>
                <td>" . htmlspecialchars($user['StepsForDay']) . "</td>
                <td>" . htmlspecialchars($user['StepsInTotal']) . "</td>
              </tr>";
    }
    echo "</tbody>
          </table>";
}

$conn->close();
?>
