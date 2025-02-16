# Operation Guide for Study Group Matcher

## TO RESET EVERYTHING RUN
```sql
DROP TABLE IF EXISTS requests, study_groups, courses, professors, departments, students;
```

## 1. Inserting Data (INSERT INTO)

### Insert a Student
```sql
INSERT INTO students (FirstName, LastName, Email, Password) 
VALUES ('Han', 'Nguyen', 'hannguyen@email.com', 'hashedpassword');
```

### Insert a Department
```sql
INSERT INTO departments (Name, Address)
VALUES ('Computer Science', '123 University Road');
```

### Insert a Professor
```sql
INSERT INTO professors (FirstName, LastName, Email, Password, DepartmentId) 
VALUES ('Xinda', 'Wang', 'xindawang@email.com', 'hashedpassword', 1);
```

### Insert a Course
```sql
INSERT INTO courses (CourseID, Name, ProfessorId, DepartmentId)
VALUES ('CS4347', 'Database Systems', 1, 1);
```

### Insert a Study Group
```sql
INSERT INTO study_groups (CourseId, LeaderStudentId, GroupName, GroupType, ProfessorApproval)
VALUES ('CS4347', 1, 'SQL Wizards', 'Exam Prep', TRUE);
```

### Insert a Request to Join a Group
```sql
INSERT INTO requests (StudentId, GroupId, Status)
VALUES (2, 1, 'Pending');
```

## 2. Retrieving Data (SELECT)

### Retrieve All Students
```sql
SELECT * FROM students;
```

### Retrieve All Study Groups
```sql
SELECT * FROM study_groups WHERE CourseId = 'CS4347';
```

### Retrieve All Requests
```sql
SELECT * FROM requests WHERE GroupId = 1;
```

### Retrieve All Students Who Requested to Join a Group
```sql
SELECT students.FirstName, students.LastName, requests.Status
FROM students
JOIN requests ON students.StudentId = requests.StudentId
WHERE requests.GroupId = 1;
```

## 3. Updating Data (UPDATE)

### Update a Student's Email
```sql
UPDATE students SET Email = 'newemail@example.com' WHERE StudentId = 1;
```

### Approve a Pending Request
```sql
UPDATE requests SET Status = 'Accepted' WHERE StudentId = 2 AND GroupId = 1;
```

### Change the Leader of a Study Group
```sql
UPDATE study_groups SET LeaderStudentId = 3 WHERE GroupId = 1;
```

## 4. Deleting Data (DELETE)

### Delete a Student
```sql
DELETE FROM students WHERE StudentId = 2;
```

### Delete a Study Group
```sql
DELETE FROM study_groups WHERE GroupId = 1;
```

## 5. Creating Relationships with JOIN

### Get All Study Groups with Course Names
```sql
SELECT study_groups.GroupName, courses.Name AS CourseName, students.FirstName AS Leader
FROM study_groups
JOIN courses ON study_groups.CourseId = courses.CourseID
JOIN students ON study_groups.LeaderStudentId = students.StudentId;
```

### List Students and Their Study Groups
```sql
SELECT students.FirstName, students.LastName, study_groups.GroupName
FROM students
JOIN requests ON students.StudentId = requests.StudentId
JOIN study_groups ON requests.GroupId = study_groups.GroupId
WHERE requests.Status = 'Accepted';
```
