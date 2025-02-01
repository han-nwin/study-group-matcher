<?php
$servername = "localhost";
$username = "root";
$password = ""; // Change if necessary
$dbname = "testdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Button Actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["create_table"])) {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE
        )";
        if ($conn->query($sql) === TRUE) {
            echo "<p style='color: green;'>Table 'users' is ready!</p>";
        } else {
            echo "<p style='color: red;'>Error creating table: " . $conn->error . "</p>";
        }
    } elseif (isset($_POST["insert_user"])) {
        $name = "John Doe";
        $email = "john.doe@example.com";
        $sql = "INSERT INTO users (name, email) VALUES ('$name', '$email')";
        if ($conn->query($sql) === TRUE) {
            echo "<p style='color: green;'>New user added!</p>";
        } else {
            echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
        }
    } elseif (isset($_POST["retrieve_users"])) {
        $sql = "SELECT * FROM users";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            echo "<h3>User List:</h3><ul>";
            while ($row = $result->fetch_assoc()) {
                echo "<li>ID: " . $row["id"] . " - Name: " . $row["name"] . " - Email: " . $row["email"] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color: red;'>No users found.</p>";
        }
    } elseif (isset($_POST["update_user"])) {
        $newName = "John Smith";
        $sql = "UPDATE users SET name='$newName' WHERE email='john.doe@example.com'";
        if ($conn->query($sql) === TRUE) {
            echo "<p style='color: green;'>User updated successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error updating user: " . $conn->error . "</p>";
        }
    } elseif (isset($_POST["delete_user"])) {
        $sql = "DELETE FROM users WHERE email='john.doe@example.com'";
        if ($conn->query($sql) === TRUE) {
            echo "<p style='color: green;'>User deleted successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error deleting user: " . $conn->error . "</p>";
        }
    }
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>PHP MySQL Demo</title>
</head>
<body>
    <h2>PHP MySQL CRUD Demo</h2>
    <form method="post">
        <button type="submit" name="create_table">Create Table</button>
        <button type="submit" name="insert_user">Insert User</button>
        <button type="submit" name="retrieve_users">Retrieve Users</button>
        <button type="submit" name="update_user">Update User</button>
        <button type="submit" name="delete_user">Delete User</button>
    </form>
</body>
</html>
