<?php
session_start();
$servername = "mysql";
$username = "root";
$password = "";
$dbname = "study_group_matcher";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Check student login
    $stmt = $conn->prepare("SELECT StudentId, Password FROM STUDENT WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($studentId, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["StudentId"] = $studentId;
            $_SESSION["UserType"] = "Student";
            header("Location: home.php");
            exit();
        }
    }
    $stmt->close();

    // Check professor login
    $stmt = $conn->prepare("SELECT ProfessorId, Password FROM PROFESSOR WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($professorId, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION["ProfessorId"] = $professorId;
            $_SESSION["UserType"] = "Professor";
            header("Location: home.php");
            exit();
        }
    }
    $stmt->close();

    $error = "Invalid email or password!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Study Group Matcher</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-dark text-light">
    <div class="container mt-5">
        <h2 class="text-center">User Login</h2>
        <?php if (isset($error)) echo "<p class='text-danger text-center'>$error</p>"; ?>
        <form method="POST" class="w-50 mx-auto">
            <div class="mb-3">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password:</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <p class="text-center mt-3">Don't have an account? <a href="register.php" class="text-info">Register here</a></p>
    </div>
</body>
</html>
