# Database Operation Guide - Study Group Matcher

## Reset Database
To reset the entire database, run the following SQL command:
```sql
DROP TABLE IF EXISTS requests, study_groups, courses, students, professors, departments;
```

## Database Operations

### 1. Insert Data
#### Insert a Department
```sql
INSERT INTO departments (Name, Address) VALUES ('Computer Science', '123 University Road');
```

#### Insert a Professor
```sql
INSERT INTO professors (FirstName, LastName, Email, Password, DepartmentId)
VALUES ('Xinda', 'Wang', 'xindawang@email.com', 'hashedpassword', 1);
```

#### Insert a Course
```sql
INSERT INTO courses (CourseID, Name, ProfessorId)
VALUES ('CS4347', 'Database Systems', 1);
```

#### Insert a Student
```sql
INSERT INTO students (FirstName, LastName, Email, Password, DepartmentId)
VALUES ('Han', 'Nguyen', 'hannguyen@email.com', 'hashedpassword', 1);
```

#### Insert a Study Group
```sql
INSERT INTO study_groups (CourseId, LeaderStudentId, GroupName, GroupType, ProfessorApproval)
VALUES ('CS4347', 1, 'SQL Wizards', 'Exam Prep', TRUE);
```

#### Insert a Request
```sql
INSERT INTO requests (StudentId, GroupId, Status)
VALUES (2, 1, 'Pending');
```

### 2. Retrieve Data
#### Get All Students
```sql
SELECT * FROM students;
```

#### Get All Study Groups for a Course
```sql
SELECT * FROM study_groups WHERE CourseId = 'CS4347';
```

#### Get All Requests for a Group
```sql
SELECT * FROM requests WHERE GroupId = 1;
```

### 3. Update Data
#### Update a Student Email
```sql
UPDATE students SET Email = 'newemail@example.com' WHERE StudentId = 1;
```

#### Approve a Pending Request
```sql
UPDATE requests SET Status = 'Accepted' WHERE StudentId = 2 AND GroupId = 1;
```

### 4. Delete Data
#### Delete a Student
```sql
DELETE FROM students WHERE StudentId = 2;
```

#### Delete a Study Group
```sql
DELETE FROM study_groups WHERE GroupId = 1;
```

### 5. Relationships Using JOIN
#### Get All Study Groups with Course Names
```sql
SELECT study_groups.GroupName, courses.Name AS CourseName, students.FirstName AS Leader
FROM study_groups
JOIN courses ON study_groups.CourseId = courses.CourseID
JOIN students ON study_groups.LeaderStudentId = students.StudentId;
```

#### List Students and Their Study Groups
```sql
SELECT students.FirstName, students.LastName, study_groups.GroupName
FROM students
JOIN requests ON students.StudentId = requests.StudentId
JOIN study_groups ON requests.GroupId = study_groups.GroupId
WHERE requests.Status = 'Accepted';
```

