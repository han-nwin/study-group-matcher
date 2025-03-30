<?php
ob_start();
session_start();
include 'navbar.php';
require_once "db.php"; // Call the db
/** @var \mysqli $conn */ // Get the $conn global var

if (!isset($_SESSION["StudentId"])) {
    header("Location: login.php");
    exit();
}

$studentId = $_SESSION["StudentId"];
$enrolledCourses = [];
$joinedGroups = [];

// Fetch enrolled courses
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
    $enrolledCourses[] = $row;
}
$stmt->close();

// Fetch joined study groups
$stmt = $conn->prepare("
    SELECT sg.GroupId, sg.GroupName, sg.GroupType, sg.Schedule, sg.ProfessorApproval, c.Name AS CourseName, r.Status
    FROM REQUEST r
    JOIN STUDY_GROUP sg ON r.GroupId = sg.GroupId
    JOIN COURSE c ON sg.CourseId = c.CourseID
    WHERE r.StudentId = ?
");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $joinedGroups[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container mt-5">
        <h2>Welcome, Student</h2>
        <p>Your enrolled courses and study groups are listed below.</p>

        <h4 class="mt-4">📚 Enrolled Courses</h4>
        <?php if (count($enrolledCourses) > 0): ?>
            <ul class="list-group list-group-flush">
                <?php foreach ($enrolledCourses as $course): ?>
                    <li class="list-group-item bg-dark text-light">
                        <?= htmlspecialchars($course["Name"]) ?> (<?= htmlspecialchars($course["CourseID"]) ?>)
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>You are not enrolled in any courses yet.</p>
        <?php endif; ?>

        <h4 class="mt-5">👥 Your Study Groups</h4>
        <?php if (count($joinedGroups) > 0): ?>
            <table class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th>Group Name</th>
                        <th>Course</th>
                        <th>Type</th>
                        <th>Schedule</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($joinedGroups as $group): ?>
                        <tr>
                            <td><?= htmlspecialchars($group["GroupName"]) ?></td>
                            <td><?= htmlspecialchars($group["CourseName"]) ?></td>
                            <td><?= htmlspecialchars($group["GroupType"]) ?></td>
                            <td>
                                <?php
                                $schedule = json_decode($group["Schedule"], true);
                                if ($schedule) {
                                    foreach ($schedule as $item) {
                                        echo htmlspecialchars($item["day"]) . ": " . htmlspecialchars($item["start"]) . " - " . htmlspecialchars($item["end"]) . "<br>";
                                    }
                                } else {
                                    echo "Not set";
                                }
                                ?>
                            </td>
                              <td>
                                  <?php
                                  if ($group["Status"] === "Accepted") {
                                      echo "✅ Accepted";
                                  } elseif ($group["Status"] === "Pending") {
                                      echo "⌛ Pending Approval";
                                  } elseif ($group["Status"] === "Rejected") {
                                      echo "❌ Rejected";
                                  }
                                  ?>
                              </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You haven't joined any study groups yet.</p>
        <?php endif; ?>

        <a href="logout.php" class="btn btn-danger mt-4">Logout</a>
    </div>
</body>
</html>

<?php $conn->close(); ?>
<?php ob_end_flush(); ?>
