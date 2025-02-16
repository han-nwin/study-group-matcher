<?php
$servername = "localhost";
$username = "root";
$password = ""; // Change if necessary
$dbname = "study_group_matcher";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
$conn->query($sql);
$conn->select_db($dbname);

// Array of tables with their creation queries
$tables = [
    "departments" => "CREATE TABLE IF NOT EXISTS departments (
        DepartmentId INT AUTO_INCREMENT PRIMARY KEY,
        Name VARCHAR(100) NOT NULL,
        Address TEXT
    )",
    "professors" => "CREATE TABLE IF NOT EXISTS professors (
        ProfessorId INT AUTO_INCREMENT PRIMARY KEY,
        FirstName VARCHAR(50) NOT NULL,
        LastName VARCHAR(50) NOT NULL,
        Email VARCHAR(100) NOT NULL UNIQUE,
        Password VARCHAR(255) NOT NULL,
        DepartmentId INT,
        FOREIGN KEY (DepartmentId) REFERENCES departments(DepartmentId) ON DELETE SET NULL
    )",
    "courses" => "CREATE TABLE IF NOT EXISTS courses (
        CourseID VARCHAR(20) PRIMARY KEY,
        Name VARCHAR(100) NOT NULL,
        ProfessorId INT,
        FOREIGN KEY (ProfessorId) REFERENCES professors(ProfessorId) ON DELETE SET NULL
    )",
    "students" => "CREATE TABLE IF NOT EXISTS students (
        StudentId INT AUTO_INCREMENT PRIMARY KEY,
        FirstName VARCHAR(50) NOT NULL,
        LastName VARCHAR(50) NOT NULL,
        Email VARCHAR(100) NOT NULL UNIQUE,
        Password VARCHAR(255) NOT NULL,
        DepartmentId INT,
        FOREIGN KEY (DepartmentId) REFERENCES departments(DepartmentId) ON DELETE SET NULL
    )",
    "study_groups" => "CREATE TABLE IF NOT EXISTS study_groups (
        GroupId INT AUTO_INCREMENT PRIMARY KEY,
        CourseId VARCHAR(20),
        LeaderStudentId INT,
        GroupName VARCHAR(100),
        GroupType VARCHAR(50),
        ProfessorApproval BOOLEAN DEFAULT FALSE,
        FOREIGN KEY (CourseId) REFERENCES courses(CourseID) ON DELETE CASCADE,
        FOREIGN KEY (LeaderStudentId) REFERENCES students(StudentId) ON DELETE SET NULL
    )",
    "requests" => "CREATE TABLE IF NOT EXISTS requests (
        StudentId INT,
        GroupId INT,
        Status ENUM('Pending', 'Accepted', 'Rejected') DEFAULT 'Pending',
        PRIMARY KEY (StudentId, GroupId),
        FOREIGN KEY (StudentId) REFERENCES students(StudentId) ON DELETE CASCADE,
        FOREIGN KEY (GroupId) REFERENCES study_groups(GroupId) ON DELETE CASCADE
    )"
];

// Create tables and store the status
$messages = [];
foreach ($tables as $table => $query) {
    if ($conn->query($query) === TRUE) {
        $messages[] = "<p class='text-success'>Table '$table' is ready! </p>";
    } else {
        $messages[] = "<p class='text-danger'>Error creating table '$table': " . $conn->error . "</p>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Study Group Matcher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container mt-4">
        <h2 class="text-center">Admin Dashboard</h2>
        <p class="text-center text-warning">⚠ Ensure all tables are created before performing operations!</p>

        <div class="text-center mt-4">
            <form action="manage.php" method="get">
                <button type="submit" class="btn btn-primary">Go to Data Management</button>
            </form>
        </div>

        <!-- Display table creation status -->
        <div>
            <?php foreach ($messages as $message) echo $message; ?>
        </div>

        <h2 class="mt-4 text-primary">Database Table Data</h2>

        <!-- Display Data from Each Table -->
        <?php
        foreach (array_keys($tables) as $table) {
            echo "<h3 class='text-warning mt-3'>Table: $table</h3>";
            $result = $conn->query("SELECT * FROM $table");
            if ($result->num_rows > 0) {
                echo "<div class='table-responsive'><table class='table table-dark table-striped'>";
                echo "<thead class='thead-light'><tr>";
                while ($field = $result->fetch_field()) {
                    echo "<th>{$field->name}</th>";
                }
                echo "</tr></thead><tbody>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>$value</td>";
                    }
                    echo "</tr>";
                }
                echo "</tbody></table></div>";
            } else {
                echo "<p class='text-muted'>No data available.</p>";
            }
        }
        ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close connection
$conn->close();
?>
