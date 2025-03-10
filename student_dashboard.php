<?php
session_start();
if (!isset($_SESSION["StudentId"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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
                      <!-- Student-specific links -->
                      <li class="nav-item"><a class="nav-link text-light" href="student_dashboard.php">Student Dashboard</a></li>
                      <li class="nav-item"><a class="nav-link text-light" href="student_profile.php">Student Profile</a></li>
                      <li class="nav-item"><a class="nav-link text-light" href="study_group.php">Study Groups</a></li>

                  <?php elseif (isset($_SESSION["ProfessorId"])): ?>
                      <!-- Professor-specific links -->
                      <li class="nav-item"><a class="nav-link text-light" href="professor_dashboard.php">Professor Dashboard</a></li>
                      <li class="nav-item"><a class="nav-link text-light" href="professor_profile.php">Professor Profile</a></li>
                      <li class="nav-item"><a class="nav-link text-light" href="study_group.php">Study Groups</a></li>

                  <?php else: ?>
                      <!-- Links for non-logged-in users -->
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

    <div class="container mt-5">
        <h2>Welcome, Student</h2>
        <p>Your enrolled courses and study groups will be shown here.</p>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</body>
</html>

