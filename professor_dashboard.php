<?php
ob_start();
session_start();
include 'navbar.php';
require_once "db.php"; // Call the db
/** @var \mysqli $conn */ // Get the $conn global var

if (!isset($_SESSION["ProfessorId"])) {
    header("Location: login.php");
    exit();
}

$professorId = $_SESSION["ProfessorId"];

// Fetch professor's name for welcome message
$stmt = $conn->prepare("SELECT FirstName, LastName FROM PROFESSOR WHERE ProfessorId = ?");
$stmt->bind_param("i", $professorId);
$stmt->execute();
$professor = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch all courses taught by the professor
$coursesStmt = $conn->prepare("SELECT CourseID, Name FROM COURSE WHERE ProfessorId = ? ORDER BY CourseID");
$coursesStmt->bind_param("i", $professorId);
$coursesStmt->execute();
$coursesResult = $coursesStmt->get_result();
$courses = [];
while ($row = $coursesResult->fetch_assoc()) {
    $courses[$row['CourseID']] = $row['Name'];
}
$coursesStmt->close();

// Fetch study groups with member and pending request counts
$groupsStmt = $conn->prepare("
    SELECT 
        c.CourseID, 
        c.Name AS CourseName, 
        sg.GroupId, 
        sg.GroupName, 
        sg.LeaderStudentId, 
        s.FirstName AS LeaderFirstName, 
        s.LastName AS LeaderLastName, 
        sg.Schedule, 
        sg.ProfessorApproval,
        COUNT(CASE WHEN r.Status = 'Accepted' THEN 1 END) AS MemberCount,
        COUNT(CASE WHEN r.Status = 'Pending' THEN 1 END) AS PendingCount
    FROM COURSE c
    LEFT JOIN STUDY_GROUP sg ON c.CourseID = sg.CourseId
    LEFT JOIN STUDENT s ON sg.LeaderStudentId = s.StudentId
    LEFT JOIN REQUEST r ON sg.GroupId = r.GroupId
    WHERE c.ProfessorId = ?
    GROUP BY sg.GroupId, c.CourseID, c.Name, sg.GroupName, sg.LeaderStudentId, s.FirstName, s.LastName, sg.Schedule, sg.ProfessorApproval
    ORDER BY c.CourseID, sg.GroupId
");
$groupsStmt->bind_param("i", $professorId);
$groupsStmt->execute();
$groupsResult = $groupsStmt->get_result();
$studyGroups = [];
while ($row = $groupsResult->fetch_assoc()) {
    if ($row['GroupId']) { // Only add if there's a study group
        $studyGroups[] = [
            'CourseID' => $row['CourseID'],
            'CourseName' => $row['CourseName'],
            'GroupId' => $row['GroupId'],
            'GroupName' => $row['GroupName'],
            'LeaderName' => $row['LeaderFirstName'] . ' ' . $row['LeaderLastName'],
            'Schedule' => json_decode($row['Schedule'], true),
            'ProfessorApproval' => $row['ProfessorApproval'],
            'MemberCount' => $row['MemberCount'] ?? 0, // Default to 0 if NULL
            'PendingCount' => $row['PendingCount'] ?? 0 // Default to 0 if NULL
        ];
    }
}
$groupsStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
    <div class="container mt-5">
        <h2>Welcome, Professor <?php echo htmlspecialchars($professor['FirstName'] . ' ' . $professor['LastName']); ?></h2>
        <p>View your assigned courses and manage study groups below.</p>

        <!-- Courses Section -->
        <div class="mt-4">
            <h3>Courses You Teach</h3>
            <?php if (empty($courses)): ?>
                <p>No courses assigned to you yet.</p>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($courses as $courseId => $courseName): ?>
                        <div class="col-md-4">
                            <div class="card bg-secondary text-light mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($courseId); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars($courseName); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Study Groups Section -->
        <div class="mt-4">
            <h3>Study Groups Under Your Courses</h3>
            <?php if (empty($studyGroups)): ?>
                <p>No study groups exist for your courses yet.</p>
            <?php else: ?>
                <table class="table table-dark table-striped">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Group Name</th>
                            <th>Leader</th>
                            <th>Schedule</th>
                            <th>Members</th>
                            <th>Pending Requests</th>
                            <th>Approval Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($studyGroups as $group): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($group['CourseID'] . ' - ' . $group['CourseName']); ?></td>
                                <td><?php echo htmlspecialchars($group['GroupName']); ?></td>
                                <td><?php echo htmlspecialchars($group['LeaderName']); ?></td>
                                <td>
                                    <?php 
                                    foreach ($group['Schedule'] as $slot) {
                                        echo htmlspecialchars($slot['day'] . ': ' . $slot['start'] . ' - ' . $slot['end']) . '<br>';
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($group['MemberCount']); ?></td>
                                <td><?php echo htmlspecialchars($group['PendingCount']); ?></td>
                                <td>
                                    <?php 
                                    echo $group['ProfessorApproval'] ? 
                                        '<span class="badge bg-success">Approved</span>' : 
                                        '<span class="badge bg-warning">Pending</span>'; 
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
<?php ob_end_flush(); ?>
