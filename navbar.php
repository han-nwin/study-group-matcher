<!-- navbar.php -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="home.php">Study Group Matcher</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link text-light" href="overview.php">Overview</a></li>

                <?php if (isset($_SESSION["StudentId"])): ?>
                    <li class="nav-item"><a class="nav-link text-light" href="student_dashboard.php">Student Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="enroll_course.php">Enroll Course</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="study_group.php">Study Groups</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="student_profile.php">Student Profile</a></li>

                <?php elseif (isset($_SESSION["ProfessorId"])): ?>
                    <li class="nav-item"><a class="nav-link text-light" href="professor_dashboard.php">Professor Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="professor_profile.php">Professor Profile</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="enroll_course.php">Enroll Course</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="study_group.php">Study Groups</a></li>

                <?php else: ?>
                    <li class="nav-item"><a class="nav-link text-light" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="register.php">Register</a></li>
                <?php endif; ?>

                <?php if (isset($_SESSION["StudentId"]) || isset($_SESSION["ProfessorId"])): ?>
                    <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

