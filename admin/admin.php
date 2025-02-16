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
    "students" => "CREATE TABLE IF NOT EXISTS students (
        StudentId INT AUTO_INCREMENT PRIMARY KEY,
        FirstName VARCHAR(50) NOT NULL,
        LastName VARCHAR(50) NOT NULL,
        Email VARCHAR(100) NOT NULL UNIQUE,
        Password VARCHAR(255) NOT NULL
    )",
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
        DepartmentId INT,
        FOREIGN KEY (ProfessorId) REFERENCES professors(ProfessorId) ON DELETE SET NULL,
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
        RequestId INT AUTO_INCREMENT PRIMARY KEY,
        StudentId INT,
        GroupId INT,
        Status ENUM('Pending', 'Accepted', 'Rejected') DEFAULT 'Pending',
        FOREIGN KEY (StudentId) REFERENCES students(StudentId) ON DELETE CASCADE,
        FOREIGN KEY (GroupId) REFERENCES study_groups(GroupId) ON DELETE CASCADE
    )"
];

// Create tables and display their data
foreach ($tables as $table => $query) {
    if ($conn->query($query) === TRUE) {
        echo "<p style='color: green;'>Table '$table' is ready!</p>";
    } else {
        echo "<p style='color: red;'>Error creating table '$table': " . $conn->error . "</p>";
    }

    // Fetch and display table data
    $result = $conn->query("SELECT * FROM $table");
    if ($result && $result->num_rows > 0) {
        echo "<h3>Data in '$table':</h3><table border='1'><tr>";
        while ($field = $result->fetch_field()) {
            echo "<th>{$field->name}</th>";
        }
        echo "</tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>$value</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No data found in '$table'.</p>";
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Study Group Matcher</title>
</head>
<body>
    <h2>Admin Dashboard</h2>
    <p style="color: blue; font-weight: bold;">⚠ Ensure all tables are created before performing operations!</p>
    
    <h3>Manage Data</h3>
    <form action="manage.php" method="get">
        <button type="submit">Go to Data Management</button>
    </form>
</body>
</html>
