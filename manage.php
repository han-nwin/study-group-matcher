<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "study_group_matcher";

// Establish database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo "<p style='color: red;'>Connection failed: " . htmlspecialchars($conn->connect_error) . "</p>";
    exit; // Stop script execution
}

// Ensure errors are caught instead of breaking the page
mysqli_report(MYSQLI_REPORT_OFF);

function executeQuery($conn, $query)
{
    try {
        $result = $conn->query($query);

        if ($result === TRUE) {
            echo "<p style='color: green;'>Operation successful.</p>";
        } elseif ($result) {
            echo "<h3>Results:</h3><table border='1'><tr>";
            while ($field = $result->fetch_field()) {
                echo "<th>" . htmlspecialchars($field->name) . "</th>";
            }
            echo "</tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            throw new Exception("SQL Error: " . $conn->error);
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<a href='manual.php'> Check Manual </a>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST["query"])) {
        $query = trim($_POST["query"]);
        executeQuery($conn, $query);
    } else {
        echo "<p style='color: red;'>Please enter a valid SQL query.</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Data - Study Group Matcher</title>
</head>
<body>
    <h2>Data Management</h2>
    <form method="post">
        <textarea name="query" rows="10" cols="100" placeholder="Enter SQL query..."></textarea><br>
        <button type="submit">Execute</button>
    </form>
    <br>
    <form action="admin.php" method="get">
        <button type="submit">Back to Admin Panel</button>
    </form>
</body>
</html>
