<?php
session_start();
include 'navbar.php';
require_once "db.php"; // Call the db
/** @var \mysqli $conn */ // Get the $conn global var

// Determine user type
$isStudent = isset($_SESSION["StudentId"]);
$isProfessor = isset($_SESSION["ProfessorId"]);
$studentId = $isStudent ? $_SESSION["StudentId"] : null;
$professorId = $isProfessor ? $_SESSION["ProfessorId"] : null;

$message = "";

// Student enrollment
if ($isStudent && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["enroll_course_id"])) {
    $courseId = trim($_POST["enroll_course_id"]);

    $stmt = $conn->prepare("SELECT * FROM ENROLL WHERE StudentId = ? AND CourseId = ?");
    $stmt->bind_param("is", $studentId, $courseId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $insert = $conn->prepare("INSERT INTO ENROLL (StudentId, CourseId) VALUES (?, ?)");
        $insert->bind_param("is", $studentId, $courseId);
        if ($insert->execute()) {
            $message = "Enrolled successfully!";
        } else {
            $message = "Error enrolling.";
        }
        $insert->close();
    } else {
        $message = "Already enrolled in that course.";
    }
    $stmt->close();
}

// Professor claims a course
if ($isProfessor && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["claim_course_id"])) {
    $courseId = trim($_POST["claim_course_id"]);

    $stmt = $conn->prepare("UPDATE COURSE SET ProfessorId = ? WHERE CourseID = ? AND ProfessorId IS NULL");
    $stmt->bind_param("is", $professorId, $courseId);
    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $message = "Course claimed successfully!";
    } else {
        $message = "Failed to claim course (maybe already taken).";
    }
    $stmt->close();
}

// Professor creates a new course
if ($isProfessor && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["new_course_name"])) {
    $newCourseName = trim($_POST["new_course_name"]);
    if (!empty($newCourseName)) {
        $courseId = uniqid("C"); // Generate unique course ID like "C641d3e7dcb14c"
        $stmt = $conn->prepare("INSERT INTO COURSE (CourseID, Name, ProfessorId) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $courseId, $newCourseName, $professorId);
        if ($stmt->execute()) {
            $message = "New course created!";
        } else {
            $message = "Failed to create course.";
        }
        $stmt->close();
    } else {
        $message = "Course name cannot be empty.";
    }
}

// Fetch available courses
if ($isStudent) {
    $stmt = $conn->prepare("
        SELECT CourseID, Name FROM COURSE 
        WHERE CourseID NOT IN (
            SELECT CourseId FROM ENROLL WHERE StudentId = ?
        )
    ");
    $stmt->bind_param("i", $studentId);
} elseif ($isProfessor) {
    $stmt = $conn->prepare("
        SELECT CourseID, Name FROM COURSE 
        WHERE ProfessorId IS NULL
    ");
}
$stmt->execute();
$courses = $stmt->get_result();

// Fetch enrolled courses to show below (only for students)
$enrolledCourses = [];
if ($isStudent) {
    $stmt = $conn->prepare("
        SELECT c.CourseID, c.Name 
        FROM COURSE c
        JOIN ENROLL e ON c.CourseID = e.CourseId
        WHERE e.StudentId = ?
    ");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $enrolledCourses = $stmt->get_result();
}


// Student unenroll
if ($isStudent && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["unenroll_course_id"])) {
    $courseId = trim($_POST["unenroll_course_id"]);

    // 1. Delete student's requests to groups in this course
    $deleteRequests = $conn->prepare("
        DELETE r FROM REQUEST r
        JOIN STUDY_GROUP sg ON r.GroupId = sg.GroupId
        WHERE r.StudentId = ? AND sg.CourseId = ?
    ");
    $deleteRequests->bind_param("is", $studentId, $courseId);
    $deleteRequests->execute();
    $deleteRequests->close();

    // 2. Delete unapproved groups the student leads in this course
    $deleteGroups = $conn->prepare("
        DELETE FROM STUDY_GROUP 
        WHERE LeaderStudentId = ? AND CourseId = ? AND ProfessorApproval = FALSE
    ");
    $deleteGroups->bind_param("is", $studentId, $courseId);
    $deleteGroups->execute();
    $deleteGroups->close();

    // 3. Unenroll from the course
    $stmt = $conn->prepare("DELETE FROM ENROLL WHERE StudentId = ? AND CourseId = ?");
    $stmt->bind_param("is", $studentId, $courseId);
    if ($stmt->execute()) {
        $message = "Unenrolled successfully! All your group requests related to this course is also removed.";
    } else {
        $message = "Error unenrolling.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enroll in or Manage Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container mt-5">
        <h2><?= $isStudent ? "Available Courses to Enroll" : "Unassigned Courses to Claim" ?></h2>

        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if ($courses->num_rows > 0): ?>
            <table class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th>Course Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $courses->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row["Name"]) ?></td>
                            <td>
                                <form method="post" class="d-inline">
                                    <input type="hidden" name="<?= $isStudent ? 'enroll_course_id' : 'claim_course_id' ?>" value="<?= $row["CourseID"] ?>">
                                    <button type="submit" class="btn btn-<?= $isStudent ? 'success' : 'primary' ?> btn-sm">
                                        <?= $isStudent ? "Enroll" : "Claim Course" ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No available courses right now.</p>
        <?php endif; ?>

        <?php if ($isStudent && $enrolledCourses->num_rows > 0): ?>
            <hr>
            <h3>Your Enrolled Courses</h3>
              <ul class="list-group list-group-flush">
                  <?php while ($row = $enrolledCourses->fetch_assoc()): ?>
                      <li class="list-group-item bg-dark text-light d-flex justify-content-between align-items-center">
                          <span><?= htmlspecialchars($row["Name"]) ?> (<?= htmlspecialchars($row["CourseID"]) ?>)</span>
                          <form method="post" class="d-inline">
                              <input type="hidden" name="unenroll_course_id" value="<?= $row["CourseID"] ?>">
                              <button type="submit" class="btn btn-outline-danger btn-sm">Unenroll</button>
                          </form>
                      </li>
                  <?php endwhile; ?>
              </ul>
        <?php endif; ?>

        <?php if ($isProfessor): ?>
            <hr>
            <h3>Create a New Course</h3>
            <form method="post">
                <input type="text" name="new_course_name" class="form-control mb-2" placeholder="New course name" required>
                <button type="submit" class="btn btn-success">Create Course</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

<?php $conn->close(); ?>

