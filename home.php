<?php
$servername = "localhost";
$username = "root";
$password = ""; // Change if necessary
$dbname = "testdb";

// Connect to MySQL (without specifying a database)
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "<p style='color: green;'>Database '$dbname' is ready!</p>";
} else {
    echo "<p style='color: red;'>Error creating database: " . $conn->error . "</p>";
}

// Now connect to the newly created database
$conn->select_db($dbname);

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
        $name = "Han Nguyen";
        $email = "han.nwin@example.com";
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
        $newName = "NAH NEYUGN";
        $sql = "UPDATE users SET name='$newName' WHERE email='han.nwin@example.com'";
        if ($conn->query($sql) === TRUE) {
            echo "<p style='color: green;'>User updated successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error updating user: " . $conn->error . "</p>";
        }
    } elseif (isset($_POST["delete_user"])) {
        $sql = "DELETE FROM users WHERE email='han.nwin@example.com'";
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
    <p style="color: red; font-weight: bold;">⚠ Click "Create Table" first before using other buttons!</p>

    <form method="post">
        <button type="submit" name="create_table">Create Table</button>
        <button type="submit" name="insert_user">Insert User</button>
        <button type="submit" name="retrieve_users">Retrieve Users</button>
        <button type="submit" name="update_user">Update User</button>
        <button type="submit" name="delete_user">Delete User</button>
    </form>
</body>
    <style>
        /* General Button Styles */
        button {
            font-size: 16px;
            padding: 10px 20px;
            margin: 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        /* Make "Create Table" Button Stand Out */
        button[name="create_table"] {
            background-color: #ff5733; /* Bright red-orange */
            color: white;
            font-weight: bold;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        }

        /* Make "Create Table" Button Change Color After Clicked */
        .clicked {
            background-color: #28a745; /* Green */
            color: white;
        }

        /* Other Buttons */
        button:not([name="create_table"]) {
            background-color: #007bff; /* Standard blue */
            color: white;
        }

        /* Hover Effect */
        button:hover {
            opacity: 0.8;
        }
    </style>
</html>
