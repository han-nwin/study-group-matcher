<?php
header("Content-Type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Operation Guide - Study Group Matcher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background-color: #11111b; color: white; margin: 0; padding: 20px; }
        .container { max-width: 900px; margin: auto; padding: 20px; background: #181825; border-radius: 10px; box-shadow: 0 0 10px rgba(255, 255, 255, 0.1); }
        h2 { color: #eed49f; }
        h3 { color: #f0c6c6; }
        pre { background: #343a40; padding: 10px; border-radius: 5px; overflow-x: auto; color: #f8f9fa; }
        code { font-family: monospace; color: #d63384; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center" style="color:#e64553">Reset Database</h1>
        <pre><code>DROP TABLE IF EXISTS 
    REQUEST, 
    STUDY_GROUP, 
    ENROLL, 
    STUDENT, 
    COURSE, 
    PROFESSOR, 
    DEPARTMENT;</code></pre>

        <h1 class="text-center" style="color:#209fb5">Database Operations</h1>
        
        <h2>1. Insert Data</h2>
        <h3>Insert a Department</h3>
        <pre><code>INSERT INTO DEPARTMENT (Name, Address) 
VALUES ('Computer Science', '123 University Road');</code></pre>
        
        <h3>Insert a Professor</h3>
        <pre><code>INSERT INTO PROFESSOR (FirstName, LastName, Email, Password, DepartmentId) 
VALUES ('Xinda', 'Wang', 'xindawang@email.com', 'hashedpassword', 1);</code></pre>
        
        <h3>Insert a Course</h3>
        <pre><code>INSERT INTO COURSE (CourseID, Name, DepartmentId, ProfessorId) 
VALUES ('CS4347', 'Database Systems', 1, 1);</code></pre>
        
        <h3>Insert a Student</h3>
        <pre><code>INSERT INTO STUDENT (FirstName, LastName, Email, Password, DepartmentId) 
VALUES ('Han', 'Nguyen', 'hannguyen@email.com', 'hashedpassword', 1);</code></pre>
        
        <h3>Insert a Study Group</h3>
        <pre><code>INSERT INTO STUDY_GROUP (CourseId, LeaderStudentId, GroupName, GroupType, ProfessorApproval) 
VALUES ('CS4347', 1, 'SQL Wizards', 'Exam Prep', TRUE);</code></pre>
        
        <h3>Insert a Request</h3>
        <pre><code>INSERT INTO REQUEST (StudentId, GroupId, Status) 
VALUES (1, 1, 'Pending');</code></pre>
        
        <h2>2. Retrieve Data</h2>
        <h3>Get All Students</h3>
        <pre><code>SELECT * FROM STUDENT;</code></pre>
        
        <h3>Get All Study Groups for a Course</h3>
        <pre><code>SELECT * FROM STUDY_GROUP 
WHERE CourseId = 'CS4347';</code></pre>
        
        <h3>Get All Requests for a Group</h3>
        <pre><code>SELECT * FROM REQUEST 
WHERE GroupId = 1;</code></pre>
        
        <h2>3. Update Data</h2>
        <h3>Update a Student Email</h3>
        <pre><code>UPDATE STUDENT 
SET Email = 'newemail@example.com' 
WHERE StudentId = 1;</code></pre>
        
        <h3>Approve a Pending Request</h3>
        <pre><code>UPDATE REQUEST 
SET Status = 'Accepted' 
WHERE StudentId = 2 
AND GroupId = 1;</code></pre>
        
        <h2>4. Delete Data</h2>
        <h3>Delete a Student</h3>
        <pre><code>DELETE FROM STUDENT 
WHERE StudentId = 2;</code></pre>
        
        <h3>Delete a Study Group</h3>
        <pre><code>DELETE FROM STUDY_GROUP 
WHERE GroupId = 1;</code></pre>
        
        <h2>5. Relationships Using JOIN</h2>
        <h3>Get All Study Groups with Course Names</h3>
        <pre><code>SELECT STUDY_GROUP.GroupName, COURSE.Name AS CourseName, STUDENT.FirstName AS Leader 
FROM STUDY_GROUP 
JOIN COURSE ON STUDY_GROUP.CourseId = COURSE.CourseID 
JOIN STUDENT ON STUDY_GROUP.LeaderStudentId = STUDENT.StudentId;</code></pre>
        
        <h3>List Students and Their Study Groups</h3>
        <pre><code>SELECT STUDENT.FirstName, STUDENT.LastName, STUDY_GROUP.GroupName 
FROM STUDENT 
JOIN REQUEST ON STUDENT.StudentId = REQUEST.StudentId 
JOIN STUDY_GROUP ON REQUEST.GroupId = STUDY_GROUP.GroupId 
WHERE REQUEST.Status = 'Accepted';</code></pre>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
