<?php
header("Content-Type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operation Guide for Study Group Matcher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px; padding: 20px; background-color: #212529; color: white; }
        h1, h2, h3 { color: #f8f9fa; }
        pre { background: #343a40; padding: 10px; border-radius: 5px; overflow-x: auto; color: #f8f9fa; }
        code { font-family: monospace; color: #d63384; }
        .container { max-width: 900px; margin: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-danger">TO RESET EVERYTHING RUN</h1>
        <pre><code>DROP TABLE IF EXISTS requests, study_groups, courses, professors, departments, students;</code></pre>

        <h1 class="text-primary">OPERATION GUIDE FOR STUDY GROUP MATCHER</h1>

        <h2>1. Inserting Data (INSERT INTO)</h2>
        <h3>Insert a Student</h3>
        <pre><code>INSERT INTO students (FirstName, LastName, Email, Password) 
VALUES ('Han', 'Nguyen', 'hannguyen@email.com', 'hashedpassword');</code></pre>
        
        <h3>Insert a Department</h3>
        <pre><code>INSERT INTO departments (Name, Address)
VALUES ('Computer Science', '123 University Road');</code></pre>
        
        <h3>Insert a Professor</h3>
        <pre><code>INSERT INTO professors (FirstName, LastName, Email, Password, DepartmentId) 
VALUES ('Xinda', 'Wang', 'xindawang@email.com', 'hashedpassword', 1);</code></pre>
        
        <h3>Insert a Course</h3>
        <pre><code>INSERT INTO courses (CourseID, Name, ProfessorId, DepartmentId)
VALUES ('CS101', 'Introduction to Programming', 1, 1);</code></pre>
        
        <h3>Insert a Study Group</h3>
        <pre><code>INSERT INTO study_groups (CourseId, LeaderStudentId, GroupName, GroupType, ProfessorApproval)
VALUES ('CS4347', 1, 'SQL Wizards', 'Exam Prep', TRUE);</code></pre>
        
        <h3>Insert a Request to Join a Group</h3>
        <pre><code>INSERT INTO requests (StudentId, GroupId, Status)
VALUES (2, 1, 'Pending');</code></pre>

        <h2>2. Retrieving Data (SELECT)</h2>
        <h3>Retrieve All Students</h3>
        <pre><code>SELECT * FROM students;</code></pre>

        <h3>Retrieve All Study Groups</h3>
        <pre><code>SELECT * FROM study_groups WHERE CourseId = 'CS101';</code></pre>

        <h3>Retrieve All Requests</h3>
        <pre><code>SELECT * FROM requests WHERE GroupId = 1;</code></pre>

        <h3>Retrieve All Students Who Requested to Join a Group</h3>
        <pre><code>SELECT students.FirstName, students.LastName, requests.Status
FROM students
JOIN requests ON students.StudentId = requests.StudentId
WHERE requests.GroupId = 1;</code></pre>

        <h2>3. Updating Data (UPDATE)</h2>
        <h3>Update a Student's Email</h3>
        <pre><code>UPDATE students SET Email = 'newemail@example.com' WHERE StudentId = 1;</code></pre>

        <h3>Approve a Pending Request</h3>
        <pre><code>UPDATE requests SET Status = 'Accepted' WHERE RequestId = 2;</code></pre>

        <h3>Change the Leader of a Study Group</h3>
        <pre><code>UPDATE study_groups SET LeaderStudentId = 3 WHERE GroupId = 1;</code></pre>

        <h2>4. Deleting Data (DELETE)</h2>
        <h3>Delete a Student</h3>
        <pre><code>DELETE FROM students WHERE StudentId = 2;</code></pre>

        <h3>Delete a Study Group</h3>
        <pre><code>DELETE FROM study_groups WHERE GroupId = 1;</code></pre>

        <h2>5. Creating Relationships with JOIN</h2>
        <h3>Get All Study Groups with Course Names</h3>
        <pre><code>SELECT study_groups.GroupName, courses.Name AS CourseName, students.FirstName AS Leader
FROM study_groups
JOIN courses ON study_groups.CourseId = courses.CourseID
JOIN students ON study_groups.LeaderStudentId = students.StudentId;</code></pre>

        <h3>List Students and Their Study Groups</h3>
        <pre><code>SELECT students.FirstName, students.LastName, study_groups.GroupName
FROM students
JOIN requests ON students.StudentId = requests.StudentId
JOIN study_groups ON requests.GroupId = study_groups.GroupId
WHERE requests.Status = 'Accepted';</code></pre>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
