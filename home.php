<?php
$servername = "localhost";
$username = "root";
$password = ""; // Change if necessary
$dbname = "testdb";
$tablename = "users";

// Connect to MySQL (without specifying a database)
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === true) {
    echo "<p style='color: green;'>Database '$dbname' is ready!</p>";
} else {
    echo "<p style='color: red;'>Error creating database: " . $conn->error . "</p>";
}

// Now connect to the newly created database
$conn->select_db($dbname);

// Check if the table exists
$tableExists = false;
$checkTable = $conn->query("SHOW TABLES LIKE '$tablename'");
if ($checkTable->num_rows > 0) {
    $tableExists = true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["create_table"])) {
        $sql = "CREATE TABLE IF NOT EXISTS $tablename (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE
        )";
        if ($conn->query($sql) === true) {
            echo "<p style='color: green;'>Table '$tablename' is ready!</p>";
            $tableExists = true;
        } else {
            echo "<p style='color: red;'>Error creating table: " . $conn->error . "</p>";
        }
    } elseif (isset($_POST["insert_user"])) {
        $name = $_POST["name"];
        $email = $_POST["email"];

        // Check if email already exist
        $sql_check = "SELECT * FROM $tablename WHERE email='$email'";
        $check_result = $conn->query($sql_check);
        if ($check_result->num_rows > 0) {
            echo "<p style='color: red;'> Error: email has already been taken </p>";
        } else {
            // Use prepared statement
            $stmt = $conn->prepare("INSERT INTO $tablename (name, email) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $email);
            if ($stmt->execute()) {
                echo "<p style='color: green;'>New user added: $name ($email)</p>";
            } else {
                echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
            }
            $stmt->close();
        }
    } elseif (isset($_POST["retrieve_users"])) {
        $sql = "SELECT * FROM $tablename";
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
        $newName = $_POST["new_name"];
        $oldEmail = $_POST["old_email"];

        // Use prepared statement
        $stmt = $conn->prepare("UPDATE $tablename SET name=? WHERE email=?");
        $stmt->bind_param("ss", $newName, $oldEmail);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo "<p style='color: green;'>User updated successfully!</p>";
        } else {
            echo "<p style='color: red;'>No user found with that email. Update failed.</p>";
        }
        $stmt->close();
    } elseif (isset($_POST["delete_user"])) {
        $deleteEmail = $_POST["delete_email"];

        // Use prepared statement
        $stmt = $conn->prepare("DELETE FROM $tablename WHERE email=?");
        $stmt->bind_param("s", $deleteEmail);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo "<p style='color: green;'>User deleted successfully!</p>";
        } else {
            echo "<p style='color: red;'>No user found with that email. Deletion failed.</p>";
        }
        $stmt->close();
    } elseif (isset($_POST["remove_table"]) && $tableExists) {
        $sql = "DROP TABLE $tablename";
        if ($conn->query($sql) === true) {
            echo "<p style='color: red;'>Table '$tablename' has been removed.</p>";
            $tableExists = false;
        } else {
            echo "<p style='color: red;'>Error removing table: " . $conn->error . "</p>";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap">
    <script src="script.js" defer></script>
    <title>Study Group Matcher</title>
</head>
<body>
    <h2>PHP MySQL CRUD with Input Fields Demo</h2>
    <p style="color: red; font-weight: bold;">⚠ A Table must be created first before using other buttons!</p>

    <form method="post">
        <button type="submit" name="create_table" <?php echo $tableExists ? 'disabled' : ''; ?>>Create Table</button>
        <button type="submit" name="remove_table" <?php echo $tableExists ? '' : 'disabled'; ?>>Remove Table</button>
    </form>

    <h3>Insert User</h3>
    <form method="post">
        <input type="text" name="name" placeholder="Enter Name" required>
        <input type="email" name="email" placeholder="Enter Email" required> </br>
        <button type="submit" name="insert_user" <?php echo $tableExists ? '' : 'disabled'; ?>>Insert User</button>
    </form>

    <h3>Retrieve Users</h3>
    <form method="post">
        <button type="submit" name="retrieve_users" <?php echo $tableExists ? '' : 'disabled'; ?>>Retrieve Users</button>
    </form>

    <h3>Update User</h3>
    <form method="post">
        <input type="email" name="old_email" placeholder="Current Email" required>
        <input type="text" name="new_name" placeholder="New Name" required> </br>
        <button type="submit" name="update_user" <?php echo $tableExists ? '' : 'disabled'; ?>>Update User</button>
    </form>

    <h3>Delete User</h3>
    <form method="post">
        <input type="email" name="delete_email" placeholder="Enter Email to Delete" required> </br>
        <button type="submit" name="delete_user" <?php echo $tableExists ? '' : 'disabled'; ?>>Delete User</button>
    </form>

</body>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        /* General Button Styles */
        button {
            font-size: 16px;
            padding: 10px 20px;
            margin: 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        /* Make "Create Table" "Remove Table" Buttons Stand Out */
        button:is([name="create_table"], [name="remove_table"]) {
            background-color: #ff5733;
            color: white;
            font-weight: bold;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        }

        /* Styling Button That Is Disabled */
        button:disabled {
            background-color: #aaaaaa !important; /* Grey */
            cursor: not-allowed !important;
        }

        /* Other Buttons */
        button:not([name="create_table"]):not([name="remove_table"]) {
            background-color: #007bff; /* Standard blue */
            color: white;
            font-weight: bold;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        }

        /* Hover Effect */
        button:hover {
            opacity: 0.8;
        }
    </style>
</html>
