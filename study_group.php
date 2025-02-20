<?php
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (isset($_POST["create_group"])) {
            $courseId = sanitize_input($_POST["course_id"]);
            $leaderId = sanitize_input($_POST["leader_id"]);
            $groupName = sanitize_input($_POST["group_name"]);
            $groupType = sanitize_input($_POST["group_type"]);

            $stmt = $conn->prepare("INSERT INTO study_groups (CourseId, LeaderStudentId, GroupName, GroupType, ProfessorApproval) VALUES (?, ?, ?, ?, FALSE)");
            $stmt->bind_param("siss", $courseId, $leaderId, $groupName, $groupType);
            if ($stmt->execute()) {
                echo "<p style='color: green;'>Study group created successfully!</p>";
            } else {
                throw new Exception($stmt->error);
            }
            $stmt->close();
        } elseif (isset($_POST["join_group"])) {
            $studentId = sanitize_input($_POST["student_id"]);
            $groupId = sanitize_input($_POST["group_id"]);
            $courseId = sanitize_input($_POST["course_id"]);
            
            $stmt = $conn->prepare("INSERT INTO requests (StudentId, GroupId, Status) VALUES (?, ?, 'Pending')");
            $stmt->bind_param("ii", $studentId, $groupId);
            if ($stmt->execute()) {
                echo "<p style='color: green;'>Join request submitted successfully!</p>";
            } else {
                throw new Exception($stmt->error);
            }
            $stmt->close();
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}

$result = $conn->query("SELECT study_groups.GroupId, study_groups.CourseId, study_groups.GroupName, study_groups.GroupType, study_groups.ProfessorApproval, courses.Name 
AS CourseName FROM study_groups JOIN courses ON study_groups.CourseId = courses.CourseID");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Groups</title>
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
<body class="bg-dark text-light">
    <div class="container mt-4">
        <h2 class="text-light">Available Study Groups</h2>
        <table class="table table-dark table-striped">
            <thead>
                <tr>
                    <th>Group Id</th>
                    <th>Group Name</th>
                    <th>Course Id</th>
                    <th>Course Name</th>
                    <th>Type</th>
                    <th>Professor Approval</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["GroupId"]) ?></td>
                        <td><?= htmlspecialchars($row["GroupName"]) ?></td>
                        <td><?= htmlspecialchars($row["CourseId"]) ?></td>
                        <td><?= htmlspecialchars($row["CourseName"]) ?></td>
                        <td><?= htmlspecialchars($row["GroupType"]) ?></td>
                        <td><?= htmlspecialchars($row["ProfessorApproval"]) ?></td>
                        <td><button class="btn btn-warning" onclick="openJoinModal(<?= $row['GroupId'] ?>, '<?= $row['CourseId'] ?>')">Request to Join</button></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2 class="text-light mt-5">Create a Study Group</h2>
        <form method="post">
            <input type="text" name="course_id" class="form-control mb-2" placeholder="Course ID" required>
            <input type="text" name="leader_id" class="form-control mb-2" placeholder="Leader Student ID" required>
            <input type="text" name="group_name" class="form-control mb-2" placeholder="Group Name" required>
            <input type="text" name="group_type" class="form-control mb-2" placeholder="Group Type (e.g., Exam Prep)" required>
            <button type="submit" name="create_group" class="btn btn-success w-100">Create Study Group</button>
        </form>
    </div>

    <div class="modal fade" id="joinModal" tabindex="-1" aria-labelledby="joinModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-light">
                <div class="modal-header">
                    <h5 class="modal-title" id="joinModalLabel">Request to Join Study Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post">
                        <input type="hidden" name="group_id" id="group_id">
                        <input type="hidden" name="course_id" id="course_id">
                        <input type="text" name="student_id" class="form-control mb-2" placeholder="Your Student ID" required>
                        <button type="submit" name="join_group" class="btn btn-warning w-100">Submit Request</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php $conn->close(); ?>
