<?php
session_start();

// Redirect logged-in users to home.php
if (isset($_SESSION["StudentId"]) || isset($_SESSION["ProfessorId"])) {
    header("Location: home.php");
    exit();
}

$servername = "mysql";
$username = "root";  // Replace with your MySQL user
$password = "";  // Replace with your MySQL password
$dbname = "study_group_matcher"; // Updated database name

// Step 1: Connect to MySQL
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 2: Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database '$dbname' is ready!<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Step 3: Connect to the newly created database
$conn->select_db($dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully to '$dbname'";

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Welcome to Study Group Matcher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light text-center">
    <div class="container mt-5">
        <h2>Welcome to Study Group Matcher</h2>
        <p>Please login or register to continue.</p>
        <a href="login.php" class="btn btn-primary">Login</a>
        <a href="register.php" class="btn btn-success">Register</a>
    </div>
</body>
</html>
