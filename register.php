<?php
session_start();
require_once "db.php"; // Call the db
/** @var \mysqli $conn */ // Get the $conn global var

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userType = $_POST["user_type"];
    $firstName = trim($_POST["first_name"]);
    $lastName = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash password

    // Check if email already exists
    $stmt = $conn->prepare("SELECT Email FROM STUDENT WHERE Email = ? UNION SELECT Email FROM PROFESSOR WHERE Email = ?");
    $stmt->bind_param("ss", $email, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $message = "Email is already registered!";
    } else {
        if ($userType == "Student") {
            $stmt = $conn->prepare("INSERT INTO STUDENT (FirstName, LastName, Email, Password, DepartmentId) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $firstName, $lastName, $email, $hashed_password, $_POST["department_id"]);
        } else {
            $stmt = $conn->prepare("INSERT INTO PROFESSOR (FirstName, LastName, Email, Password, DepartmentId) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $firstName, $lastName, $email, $hashed_password, $_POST["department_id"]);
        }

        if ($stmt->execute()) {
            header("Location: login.php");
            exit();
        } else {
            $message = "Registration failed!";
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Study Group Matcher</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-dark text-light">
    <div class="container mt-5">
        <h2 class="text-center">User Registration</h2>
        <?php if (!empty($message)) echo "<p class='text-danger text-center'>$message</p>"; ?>
        <form method="POST" class="w-50 mx-auto">
            <div class="mb-3">
                <label>First Name:</label>
                <input type="text" name="first_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Last Name:</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Department ID:</label>
                <input type="number" name="department_id" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>User Type:</label>
                <select name="user_type" class="form-control" required>
                    <option value="Student">Student</option>
                    <option value="Professor">Professor</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
        <p class="text-center mt-3">Already have an account? <a href="login.php" class="text-info">Login here</a></p>
    </div>
</body>
</html>
