<?php
$servername = "mysql";
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
    "DEPARTMENT" => "CREATE TABLE IF NOT EXISTS DEPARTMENT (
        DepartmentId INT AUTO_INCREMENT PRIMARY KEY,
        Name VARCHAR(100) NOT NULL,
        Address TEXT
    )",
    "PROFESSOR" => "CREATE TABLE IF NOT EXISTS PROFESSOR (
        ProfessorId INT AUTO_INCREMENT PRIMARY KEY,
        FirstName VARCHAR(50) NOT NULL,
        LastName VARCHAR(50) NOT NULL,
        Email VARCHAR(100) NOT NULL UNIQUE,
        Password VARCHAR(255) NOT NULL,
        DepartmentId INT,
        FOREIGN KEY (DepartmentId) REFERENCES DEPARTMENT(DepartmentId) ON DELETE SET NULL
    )",
    "COURSE" => "CREATE TABLE IF NOT EXISTS COURSE (
        CourseID VARCHAR(20) PRIMARY KEY,
        Name VARCHAR(100) NOT NULL,
        DepartmentId INT,
        ProfessorId INT,
        FOREIGN KEY (DepartmentId) REFERENCES DEPARTMENT(DepartmentId) ON DELETE SET NULL,
        FOREIGN KEY (ProfessorId) REFERENCES PROFESSOR(ProfessorId) ON DELETE SET NULL
    )",
    "STUDENT" => "CREATE TABLE IF NOT EXISTS STUDENT (
        StudentId INT AUTO_INCREMENT PRIMARY KEY UNIQUE,
        FirstName VARCHAR(50) NOT NULL,
        LastName VARCHAR(50) NOT NULL,
        Email VARCHAR(100) NOT NULL UNIQUE,
        Password VARCHAR(255) NOT NULL,
        DepartmentId INT,
        FOREIGN KEY (DepartmentId) REFERENCES DEPARTMENT(DepartmentId) ON DELETE SET NULL
    )",
    "STUDY_GROUP" => "CREATE TABLE IF NOT EXISTS STUDY_GROUP (
        GroupId INT AUTO_INCREMENT PRIMARY KEY,
        CourseId VARCHAR(20) NOT NULL,
        LeaderStudentId INT,
        GroupName VARCHAR(100),
        GroupType VARCHAR(50),
        Schedule JSON,
        ProfessorApproval BOOLEAN DEFAULT FALSE,
        FOREIGN KEY (CourseId) REFERENCES COURSE(CourseID) ON DELETE CASCADE,
        FOREIGN KEY (LeaderStudentId) REFERENCES STUDENT(StudentId) ON DELETE SET NULL
    )",
    "REQUEST" => "CREATE TABLE IF NOT EXISTS REQUEST (
        StudentId INT,
        GroupId INT,
        Status ENUM('Pending', 'Accepted', 'Rejected') DEFAULT 'Pending',
        PRIMARY KEY (StudentId, GroupId),
        FOREIGN KEY (StudentId) REFERENCES STUDENT(StudentId) ON DELETE CASCADE,
        FOREIGN KEY (GroupId) REFERENCES STUDY_GROUP(GroupId) ON DELETE CASCADE
    )",
    "ENROLL" => "CREATE TABLE IF NOT EXISTS ENROLL (
        StudentId INT,
        CourseId VARCHAR(20),
        PRIMARY KEY (StudentId, CourseId),
        FOREIGN KEY (StudentId) REFERENCES STUDENT(StudentId) ON DELETE CASCADE,
        FOREIGN KEY (CourseId) REFERENCES COURSE(CourseID) ON DELETE CASCADE
    )",
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
        <!-- <p class="text-center text-warning">⚠ Ensure all tables are created before performing operations!</p> -->

        <div class="text-center mt-4">
            <form action="manage.php" method="get">
                <button type="submit" class="btn btn-primary">Go to Data Management</button>
            </form>
        </div>

        <!-- Display table creation status -->
        <!--
        <div>
            <?php foreach ($messages as $message) echo $message; ?>
        </div>

        -->
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
