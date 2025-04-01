<?php
session_start();
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Group Matcher - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container mt-4">
        <section id="overview" class="text-center">
            <h2 class="text-light">Welcome to Study Group Matcher</h2>
            <p class="lead text-light">Find, join, and manage study groups for your courses easily!</p>
        </section>

        <?php if (isset($_SESSION["StudentId"])): ?>
            <!-- Student Actions -->
            <section id="dashboard" class="mt-5">
                <div class="row mt-3">
                    <div class="col-md-4">
                        <a href="study_group.php" class="btn btn-primary w-100 mb-2">View Study Groups</a>
                    </div>
                    <div class="col-md-4">
                        <a href="student_dashboard.php" class="btn btn-success w-100 mb-2">View Dashboard</a>
                    </div>
                    <div class="col-md-4">
                        <a href="student_profile.php" class="btn btn-warning w-100">View Profile</a>
                    </div>
                </div>
            </section>
        <?php elseif (isset($_SESSION["ProfessorId"])): ?>
            <!-- Professor Actions -->
            <section id="dashboard" class="mt-5">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <a href="professor_dashboard.php" class="btn btn-info w-100 mb-2">View Dashboard</a>
                    </div>
                    <div class="col-md-6">
                        <a href="study_group.php" class="btn btn-secondary w-100">View Study Groups</a>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </div>

    <footer class="bg-dark text-center py-3 mt-5">
        <p class="text-light">&copy; 2025 Study Group Matcher</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
