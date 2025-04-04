<?php
ob_start();  
session_start();
include 'navbar.php';
require_once "db.php"; // Call the db
/** @var \mysqli $conn */ // Get the $conn global var

// Check if professor is logged in
if (!isset($_SESSION["ProfessorId"])) {
    header("Location: login.php");
    exit();
}

$professorId = $_SESSION["ProfessorId"];
$message = "";

// Handle update submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST["first_name"]);
    $lastName = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Check if email already exists for another professor
    $checkEmail = $conn->prepare("SELECT ProfessorId FROM PROFESSOR WHERE Email = ? AND ProfessorId != ?");
    $checkEmail->bind_param("si", $email, $professorId);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        $message = "Email is already taken by another professor.";
    } else {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE PROFESSOR SET FirstName = ?, LastName = ?, Email = ?, Password = ? WHERE ProfessorId = ?");
            $stmt->bind_param("ssssi", $firstName, $lastName, $email, $hashedPassword, $professorId);
        } else {
            $stmt = $conn->prepare("UPDATE PROFESSOR SET FirstName = ?, LastName = ?, Email = ? WHERE ProfessorId = ?");
            $stmt->bind_param("sssi", $firstName, $lastName, $email, $professorId);
        }

        if ($stmt->execute()) {
            $message = "Profile updated successfully!";
        } else {
            $message = "Error updating profile.";
        }
        $stmt->close();
    }

    $checkEmail->close();
}

// Fetch current info
$stmt = $conn->prepare("SELECT FirstName, LastName, Email, DepartmentId FROM PROFESSOR WHERE ProfessorId = ?");
$stmt->bind_param("i", $professorId);
$stmt->execute();
$result = $stmt->get_result();
$professor = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Professor Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container mt-5">
        <h2>Professor Profile</h2>
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <form method="post" class="mt-4">
            <div class="mb-3">
                <label>First Name:</label>
                <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($professor['FirstName']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Last Name:</label>
                <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($professor['LastName']) ?>" required>
            </div>
            <div class="mb-3">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($professor['Email']) ?>" required>
            </div>
            <div class="mb-3">
                <label>New Password (leave blank to keep current):</label>
                <input type="password" name="password" class="form-control" placeholder="Enter new password">
            </div>
            <div class="mb-3">
                <label>Department ID:</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($professor['DepartmentId']) ?>" disabled>
            </div>
            <button type="submit" class="btn btn-success w-100">Update Profile</button>
        </form>
    </div>
</body>
</html>

<?php $conn->close(); ?>
<?php ob_end_flush(); ?>
