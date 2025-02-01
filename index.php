<?php
$servername = "localhost";
$username = "root";  // Replace with your MySQL user
$password = "";  // Replace with your MySQL password
$dbname = "testdb";      // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully";

// Close connection
$conn->close();
?>

