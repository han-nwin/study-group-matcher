<?php
$servername = "localhost";
$username = "root";  // Replace with your MySQL user
$password = "";  // Replace with your MySQL password
$dbname = "testdb"; // The database to connect to

// Step 1: Connect to MySQL (without selecting a database)
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 2: Create the database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database '$dbname' is ready!<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// Step 3: Now connect to the newly created database
$conn->select_db($dbname);

// Final Check: Confirm successful connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully to '$dbname'";

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Database Connection</title>
</head>
<body>
    <br><br>
    <form action="home.php" method="get">
        <button type="submit">Go to Home</button>
    </form>
</body>
</html>
