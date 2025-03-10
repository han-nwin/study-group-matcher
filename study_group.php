<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "study_group_matcher";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Redirect if not logged in
if (!isset($_SESSION["StudentId"]) && !isset($_SESSION["ProfessorId"])) {
    header("Location: login.php");
    exit();
}

// Determine user type
$isStudent = isset($_SESSION["StudentId"]);
$isProfessor = isset($_SESSION["ProfessorId"]);
$studentId = $isStudent ? $_SESSION["StudentId"] : null;
$professorId = $isProfessor ? $_SESSION["ProfessorId"] : null;

// Fetch enrolled courses for students
$studentCourses = [];
if ($isStudent) {
    $stmt = $conn->prepare("
        SELECT c.CourseID, c.Name 
        FROM COURSE c
        JOIN ENROLL e ON c.CourseID = e.CourseId
        WHERE e.StudentId = ?
    ");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $studentCourses[] = $row;
    }
    $stmt->close();
}

// Fetch courses taught by professors
$professorCourses = [];
if ($isProfessor) {
    $stmt = $conn->prepare("
        SELECT c.CourseID, c.Name 
        FROM COURSE c
        WHERE c.ProfessorId = ?
    ");
    $stmt->bind_param("i", $professorId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $professorCourses[] = $row;
    }
    $stmt->close();
}

// Fetch study groups relevant to user
if ($isStudent) {
    $stmt = $conn->prepare("
        SELECT sg.GroupId, sg.CourseId, sg.GroupName, sg.GroupType, sg.ProfessorApproval, c.Name AS CourseName, sg.LeaderStudentId
        FROM STUDY_GROUP sg
        JOIN COURSE c ON sg.CourseId = c.CourseID
        JOIN ENROLL e ON sg.CourseId = e.CourseId
        WHERE e.StudentId = ?
    ");
    $stmt->bind_param("i", $studentId);
} elseif ($isProfessor) {
    $stmt = $conn->prepare("
        SELECT sg.GroupId, sg.CourseId, sg.GroupName, sg.GroupType, sg.ProfessorApproval, c.Name AS CourseName
        FROM STUDY_GROUP sg
        JOIN COURSE c ON sg.CourseId = c.CourseID
        WHERE c.ProfessorId = ?
    ");
    $stmt->bind_param("i", $professorId);
}
$stmt->execute();
$studyGroups = $stmt->get_result();

// Handle Study Group Approvals (Professor Only) and Reload Page
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["approve_group"]) && $isProfessor) {
    $groupId = sanitize_input($_POST["group_id"]);
    $updateStmt = $conn->prepare("UPDATE STUDY_GROUP SET ProfessorApproval = TRUE WHERE GroupId = ?");
    $updateStmt->bind_param("i", $groupId);
    if ($updateStmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']); // Reload page
        exit();
    }
    $updateStmt->close();
}

// Handle Study Group Creation (Student Only) and Reload Page
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create_group"]) && $isStudent) {
    $courseId = sanitize_input($_POST["course_id"]);
    $groupName = sanitize_input($_POST["group_name"]);
    $groupType = sanitize_input($_POST["group_type"]);

    $stmt = $conn->prepare("
        INSERT INTO STUDY_GROUP (CourseId, LeaderStudentId, GroupName, GroupType, ProfessorApproval) 
        VALUES (?, ?, ?, ?, FALSE)
    ");
    $stmt->bind_param("siss", $courseId, $studentId, $groupName, $groupType);
    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']); // Reload page
        exit();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Groups</title>
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

    <div class="container mt-4">
        <h2 class="text-light">Available Study Groups</h2>
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>Group Name</th>
                    <th>Course</th>
                    <th>Type</th>
                    <th>Professor Approval</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $studyGroups->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["GroupName"]) ?></td>
                        <td><?= htmlspecialchars($row["CourseName"]) ?></td>
                        <td><?= htmlspecialchars($row["GroupType"]) ?></td>
                        <td><?= $row["ProfessorApproval"] ? "Approved" : "Pending" ?></td>
                        <td>
                            <?php if ($isStudent): ?>
                                <?php if ($row["LeaderStudentId"] == $studentId): ?>
                                    <button class="btn btn-secondary" disabled>Leader</button>
                                <?php else: ?>
                                    <button class="btn btn-warning">Request to Join</button>
                                <?php endif; ?>
                            <?php elseif ($isProfessor && !$row["ProfessorApproval"]): ?>
                                <form method="post">
                                    <input type="hidden" name="group_id" value="<?= $row['GroupId'] ?>">
                                    <button type="submit" name="approve_group" class="btn btn-success">Approve</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php if ($isStudent): ?>
            <h2 class="text-light mt-5">Create a Study Group</h2>
            <form method="post">
                <label>Select a Course:</label>
                <select name="course_id" class="form-control mb-2" required>
                    <?php foreach ($studentCourses as $course): ?>
                        <option value="<?= $course['CourseID'] ?>"><?= $course['Name'] ?> (<?= $course['CourseID'] ?>)</option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="group_name" class="form-control mb-2" placeholder="Group Name" required>
                <input type="text" name="group_type" class="form-control mb-2" placeholder="Group Type (e.g., Exam Prep)" required>
                <button type="submit" name="create_group" class="btn btn-success w-100">Create Study Group</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

<?php $conn->close(); ?>
