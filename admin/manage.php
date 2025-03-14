<?php
$servername = "mysql";
$username = "root";
$password = "";
$dbname = "study_group_matcher";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo "<p style='color: red;'>Connection failed: " . htmlspecialchars($conn->connect_error) . "</p>";
    exit;
}

mysqli_report(MYSQLI_REPORT_OFF);

function executeQuery($conn, $query)
{
    try {
        $result = $conn->query($query);

        if ($result === TRUE) {
            echo "<p class='text-success'>Operation successful.</p>";
        } elseif ($result) {
            echo "<h3 class='mt-4 text-warning'>Query Results:</h3>";
            echo "<div class='table-responsive'>";
            echo "<table class='table table-dark table-bordered table-hover table-striped'>";
            
            // Table headers
            echo "<thead class='thead-light'><tr>";
            while ($field = $result->fetch_field()) {
                echo "<th>" . htmlspecialchars($field->name) . "</th>";
            }
            echo "</tr></thead><tbody>";
            
            // Table rows
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            echo "</tbody></table></div>";
        } else {
            throw new Exception("SQL Error: " . $conn->error);
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger' role='alert'>";
        echo "<strong>Error:</strong> " . htmlspecialchars($e->getMessage());
        echo "</div>";
        echo "<a href='manual.php' class='btn btn-warning mt-2'>Check Manual</a>";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container mt-4">
        <h2>Data Management</h2>
        <form method="post">
            <textarea name="query" rows="10" cols="100" class="form-control mb-3" placeholder="Enter SQL query..."></textarea>
            <button type="submit" class="btn btn-primary">Execute</button>
        </form>
        <br>
        <form action="admin.php" method="get">
            <button type="submit" class="btn btn-secondary">Back to Admin Panel</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
