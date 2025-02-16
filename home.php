<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Group Matcher - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <li class="nav-item"><a class="nav-link text-light" href="#overview">Overview</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="student_dashboard.php">Student Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="professor_dashboard.php">Professor Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="student_profile.php">Student Profile</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="professor_profile.php">Professor Profile</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="study_group.php">Study Groups</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <section id="overview" class="text-center">
            <h2 class="text-light">Welcome to Study Group Matcher</h2>
            <p class="lead text-light">Find, join, and manage study groups for your courses easily!</p>
        </section>

        <section id="dashboard" class="mt-5">
            <div class="row mt-3">
                <div class="col-md-4">
                    <a href="groups.php" class="btn btn-primary w-100 mb-2">View Study Groups</a>
                </div>
                <div class="col-md-4">
                    <a href="create_group.php" class="btn btn-success w-100 mb-2">Create Study Group</a>
                </div>
                <div class="col-md-4">
                    <a href="join_group.php" class="btn btn-warning w-100">Join Study Group</a>
                </div>
            </div>
        </section>
    </div>

    <footer class="bg-dark text-center py-3 mt-5">
        <p class="text-light">&copy; 2025 Study Group Matcher</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
