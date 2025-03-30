<?php
session_start();
include 'navbar.php';
if (!isset($_SESSION["ProfessorId"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function openJoinModal(groupId, courseId) {
            document.getElementById('group_id').value = groupId;
            document.getElementById('course_id').value = courseId;
            var joinModal = new bootstrap.Modal(document.getElementById('joinModal'));
            joinModal.show();
        }
    </script>
</head>

<body class="bg-dark text-light">
    <div class="container mt-5">
        <h2>Welcome, Professor</h2>
        <p>Your assigned courses and study group approvals will be shown here.</p>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>

