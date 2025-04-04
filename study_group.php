<?php
ob_start();
session_start();
include 'navbar.php';
require_once "db.php"; // Call the db
/** @var \mysqli $conn */ // Get the $conn global var

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

// Handle Request to Join Study Group (Student Only)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["request_group"]) && $isStudent) {
    $groupId = intval($_POST["group_id"]);

    // Check if a request already exists (safety check)
    $stmt = $conn->prepare("SELECT * FROM REQUEST WHERE StudentId = ? AND GroupId = ?");
    $stmt->bind_param("ii", $studentId, $groupId);
    $stmt->execute();
    $existingRequest = $stmt->get_result();
    $stmt->close();

    if ($existingRequest->num_rows === 0) {
        // Create new request
        $stmt = $conn->prepare("INSERT INTO REQUEST (StudentId, GroupId, Status) VALUES (?, ?, 'Pending')");
        $stmt->bind_param("ii", $studentId, $groupId);
        if ($stmt->execute()) {
            header("Location: " . $_SERVER["PHP_SELF"]); // Refresh page
            exit();
        } else {
            echo "<script>alert('Failed to send join request');</script>";
        }
        $stmt->close();
    }
}

// Fetch study groups with member count and leader name
if ($isStudent) {
    $stmt = $conn->prepare("
        SELECT 
            sg.GroupId, 
            sg.CourseId, 
            sg.GroupName, 
            sg.GroupType, 
            sg.ProfessorApproval, 
            sg.Schedule, 
            c.Name AS CourseName, 
            sg.LeaderStudentId,
            s.FirstName AS LeaderFirstName,
            s.LastName AS LeaderLastName,
            COUNT(CASE WHEN r.Status = 'Accepted' THEN 1 END) AS MemberCount
        FROM STUDY_GROUP sg
        JOIN COURSE c ON sg.CourseId = c.CourseID
        JOIN ENROLL e ON sg.CourseId = e.CourseId
        LEFT JOIN STUDENT s ON sg.LeaderStudentId = s.StudentId
        LEFT JOIN REQUEST r ON sg.GroupId = r.GroupId
        WHERE e.StudentId = ?
        GROUP BY sg.GroupId, sg.CourseId, sg.GroupName, sg.GroupType, sg.ProfessorApproval, sg.Schedule, c.Name, sg.LeaderStudentId, s.FirstName, s.LastName
    ");
    $stmt->bind_param("i", $studentId);
} elseif ($isProfessor) {
    $stmt = $conn->prepare("
        SELECT 
            sg.GroupId, 
            sg.CourseId, 
            sg.GroupName, 
            sg.GroupType, 
            sg.ProfessorApproval, 
            sg.Schedule, 
            c.Name AS CourseName,
            sg.LeaderStudentId,
            s.FirstName AS LeaderFirstName,
            s.LastName AS LeaderLastName,
            COUNT(CASE WHEN r.Status = 'Accepted' THEN 1 END) AS MemberCount
        FROM STUDY_GROUP sg
        JOIN COURSE c ON sg.CourseId = c.CourseID
        LEFT JOIN STUDENT s ON sg.LeaderStudentId = s.StudentId
        LEFT JOIN REQUEST r ON sg.GroupId = r.GroupId
        WHERE c.ProfessorId = ?
        GROUP BY sg.GroupId, sg.CourseId, sg.GroupName, sg.GroupType, sg.ProfessorApproval, sg.Schedule, c.Name, sg.LeaderStudentId, s.FirstName, s.LastName
    ");
    $stmt->bind_param("i", $professorId);
}
$stmt->execute();
$studyGroups = $stmt->get_result();

// Handle Study Group Approvals (Professor Only)
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

// Handle Study Group Deletion (Leader Only)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_group"]) && $isStudent) {
    $groupId = sanitize_input($_POST["group_id"]);
    $deleteStmt = $conn->prepare("DELETE FROM STUDY_GROUP WHERE GroupId = ? AND LeaderStudentId = ? AND ProfessorApproval = FALSE");
    $deleteStmt->bind_param("ii", $groupId, $studentId);
    if ($deleteStmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']); // Reload page
        exit();
    }
    $deleteStmt->close();
}

// Handle Study Group Creation (Student Only)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create_group"]) && $isStudent) {
    $courseId = sanitize_input($_POST["course_id"]);
    $groupName = sanitize_input($_POST["group_name"]);
    $groupType = sanitize_input($_POST["group_type"]);
    $scheduleJson = trim($_POST["schedule"]); // JSON schedule input

    $stmt = $conn->prepare("
        INSERT INTO STUDY_GROUP (CourseId, LeaderStudentId, GroupName, GroupType, ProfessorApproval, Schedule) 
        VALUES (?, ?, ?, ?, FALSE, ?)
    ");
    $stmt->bind_param("sisss", $courseId, $studentId, $groupName, $groupType, $scheduleJson);
    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']); // Reload page
        exit();
    }
    $stmt->close();
}

// Fetch groups for leaders to manage
$leaderRequests = [];
if ($isStudent) {
    $stmt = $conn->prepare("
        SELECT r.StudentId, s.FirstName, s.LastName, r.Status, sg.GroupId, sg.GroupName
        FROM REQUEST r
        JOIN STUDENT s ON r.StudentId = s.StudentId
        JOIN STUDY_GROUP sg ON r.GroupId = sg.GroupId
        WHERE sg.LeaderStudentId = ?
    ");
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $leaderRequests = $stmt->get_result();
}

// Handle Accept or Reject actions (Leader Only)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["manage_request"]) && $isStudent) {
    $targetStudentId = intval($_POST["target_student_id"]);
    $targetGroupId = intval($_POST["target_group_id"]);
    $action = $_POST["action"]; // Should be 'Accepted' or 'Rejected'

    if (in_array($action, ['Accepted', 'Rejected'])) {
        $stmt = $conn->prepare("
            UPDATE REQUEST 
            SET Status = ? 
            WHERE StudentId = ? AND GroupId = ?
        ");
        $stmt->bind_param("sii", $action, $targetStudentId, $targetGroupId);
        $stmt->execute();
        $stmt->close();

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
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
    <div class="container mt-4">
        <h2 class="text-light">Available Study Groups</h2>
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>Group Name</th>
                    <th>Course</th>
                    <th>Type</th>
                    <th>Leader</th>
                    <th>Members</th>
                    <th>Professor Approval</th>
                    <th>Schedule</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $studyGroups->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["GroupName"]) ?></td>
                        <td><?= htmlspecialchars($row["CourseName"]) ?></td>
                        <td><?= htmlspecialchars($row["GroupType"]) ?></td>
                        <td><?= htmlspecialchars($row["LeaderFirstName"] . " " . $row["LeaderLastName"]) ?></td>
                        <td><?= htmlspecialchars($row["MemberCount"] ?? 0) ?></td>
                        <td><?= $row["ProfessorApproval"] ? "Approved" : "Pending" ?></td>
                        <td>
                            <?php 
                                $schedule = json_decode($row["Schedule"], true);
                                if ($schedule) {
                                    foreach ($schedule as $day) {
                                        echo htmlspecialchars($day['day']) . ": " . htmlspecialchars($day['start']) . " - " . htmlspecialchars($day['end']) . "<br>";
                                    }
                                } else {
                                    echo "Not Set";
                                }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ($isStudent) {
                                $groupId = $row["GroupId"];
                                $isLeader = $row["LeaderStudentId"] == $studentId;

                                if ($isLeader && !$row["ProfessorApproval"]) {
                                    // Group leader can delete unapproved group
                                    ?>
                                    <form method="post">
                                        <input type="hidden" name="group_id" value="<?= $groupId ?>">
                                        <button type="submit" name="delete_group" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                    <?php
                                } elseif (!$isLeader) {
                                    // Check request status
                                    $checkRequest = $conn->prepare("SELECT Status FROM REQUEST WHERE StudentId = ? AND GroupId = ?");
                                    $checkRequest->bind_param("ii", $studentId, $groupId);
                                    $checkRequest->execute();
                                    $requestResult = $checkRequest->get_result();

                                    if ($requestResult->num_rows > 0) {
                                        $status = $requestResult->fetch_assoc()["Status"];
                                        echo "<span class='badge bg-secondary'>" . htmlspecialchars($status) . "</span>";
                                    } else {
                                        // No request yet — show request button
                                        ?>
                                        <form method="post">
                                            <input type="hidden" name="group_id" value="<?= $groupId ?>">
                                            <button type="submit" name="request_group" class="btn btn-warning btn-sm">Request to Join</button>
                                        </form>
                                        <?php
                                    }
                                    $checkRequest->close();
                                }
                            } elseif ($isProfessor && !$row["ProfessorApproval"]) {
                                ?>
                                <form method="post">
                                    <input type="hidden" name="group_id" value="<?= $row['GroupId'] ?>">
                                    <button type="submit" name="approve_group" class="btn btn-success btn-sm">Approve</button>
                                </form>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php if ($isStudent && $leaderRequests->num_rows > 0): ?>
            <h2 class="text-light mt-5">📥 Requests for My Groups</h2>
            <table class="table table-dark table-bordered">
                <thead>
                    <tr>
                        <th>Group Name</th>
                        <th>Student Name</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($req = $leaderRequests->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($req["GroupName"]) ?></td>
                            <td><?= htmlspecialchars($req["FirstName"] . " " . $req["LastName"]) ?></td>
                            <td>
                                <?php if ($req["Status"] === "Pending"): ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php elseif ($req["Status"] === "Accepted"): ?>
                                    <span class="badge bg-success">Accepted</span>
                                <?php elseif ($req["Status"] === "Rejected"): ?>
                                    <span class="badge bg-danger">Rejected</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($req["Status"] === "Pending"): ?>
                                    <form method="post" class="d-inline">
                                        <input type="hidden" name="target_student_id" value="<?= $req["StudentId"] ?>">
                                        <input type="hidden" name="target_group_id" value="<?= $req["GroupId"] ?>">
                                        <input type="hidden" name="manage_request" value="1">
                                        <button type="submit" name="action" value="Accepted" class="btn btn-success btn-sm">Accept</button>
                                        <button type="submit" name="action" value="Rejected" class="btn btn-danger btn-sm">Reject</button>
                                    </form>
                                <?php elseif ($req["Status"] === "Accepted"): ?>
                                    <span class="badge bg-success">You Have Accepted This Request</span>
                                <?php elseif ($req["Status"] === "Rejected"): ?>
                                    <span class="badge bg-danger">You Have Rejected This Request</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>

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
                <input type="text" name="group_type" class="form-control mb-2" placeholder="Group Type" required>
                <textarea 
                    name="schedule" class="form-control mb-2" 
                    placeholder='Enter JSON schedule e.g. [{"day":"Monday","start":"14:00","end":"16:00"}, {"day":"Tuesday","start":"12:00","end":"15:00"}]' 
                    required
                ></textarea>
                <button type="submit" name="create_group" class="btn btn-success w-100">Create Study Group</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>

<?php $conn->close(); ?>
<?php ob_end_flush(); ?>
