<?php
session_start();
include 'config.php';  // or wherever your DB connection is set up

if (!isset($_GET['group_id'])) {
    echo "Group ID not provided.";
    exit();
}

$group_id = $_GET['group_id'];

// Fetch leaderboard data
$stmt = $conn->prepare("
    SELECT u.Username, u.StepsInTotal
    FROM Users u
    INNER JOIN GroupMembers gm ON u.Username = gm.Username
    WHERE gm.GroupID = ?
    ORDER BY u.StepsInTotal DESC
");
$stmt->bind_param("i", $group_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<h2>Leaderboard</h2>";
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($row['Username']) . ": " . htmlspecialchars($row['StepsInTotal']) . " steps</li>";
    }
    echo "</ul>";
} else {
    echo "No data available.";
}

$stmt->close();
$conn->close();
?>
