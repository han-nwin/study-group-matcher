<?php
session_start();
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overview - Study Group Matcher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container mt-4">
        <h2 class="text-center text-light">Welcome to Study Group Matcher!</h2>
        <p class="text-center lead">A platform to help students and professors manage study groups efficiently.</p>

        <?php if (isset($_SESSION["StudentId"])): ?>
            <!-- Overview for Students -->
            <div class="card bg-light text-dark mb-4">
                <div class="card-body">
                    <h3 class="text-danger">📚 How to Use This Platform (For Students)</h3>
                    <ul>
                        <li><strong>View Enrolled Courses:</strong> Go to <a href="student_dashboard.php" class="text-info">Student Dashboard</a> to see your enrolled courses.</li>
                        <li><strong>Create a Study Group:</strong> Navigate to <a href="study_group.php" class="text-info">Study Groups</a> and start a new group for your course.</li>
                        <li><strong>Join a Study Group:</strong> Browse existing groups and submit a request to join.</li>
                        <li><strong>Manage Your Profile:</strong> Update your details in <a href="student_profile.php" class="text-info">Student Profile</a>.</li>
                    </ul>
                </div>
            </div>

        <?php elseif (isset($_SESSION["ProfessorId"])): ?>
            <!-- Overview for Professors -->
            <div class="card bg-secondary text-light mb-4">
                <div class="card-body">
                    <h3 class="text-warning">🎓 How to Use This Platform (For Professors)</h3>
                    <ul>
                        <li><strong>View Your Courses:</strong> Go to <a href="professor_dashboard.php" class="text-info">Professor Dashboard</a> to see the courses you teach.</li>
                        <li><strong>Approve Study Groups:</strong> In <a href="study_group.php" class="text-info">Study Groups</a>, review and approve student-created groups.</li>
                        <li><strong>Manage Your Profile:</strong> Update your details in <a href="professor_profile.php" class="text-info">Professor Profile</a>.</li>
                    </ul>
                </div>
            </div>

        <?php else: ?>
            <!-- Overview for Visitors (Not Logged In) -->
            <div class="card bg-secondary text-light mb-4">
                <div class="card-body">
                    <h3 class="text-warning">🔑 Getting Started</h3>
                    <ul>
                        <li><strong>Students:</strong> Register and enroll in courses to join or create study groups.</li>
                        <li><strong>Professors:</strong> Register to manage and approve student study groups.</li>
                        <li><strong>Login to Get Started:</strong> <a href="login.php" class="btn btn-primary btn-sm">Login</a> or <a href="register.php" class="btn btn-success btn-sm">Register</a></li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <h3 class="text-center text-light mt-4">🚀 Why Use Study Group Matcher?</h3>
        <p class="text-center">This platform makes it easy to collaborate, form study groups, and manage academic discussions efficiently!</p>
    </div>

    <footer class="bg-dark text-center py-3 mt-5">
        <p class="text-light">&copy; 2025 Study Group Matcher</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

