# OPERATION GUIDE FOR STUDY GROUP MATCHER

## 1. Inserting Data (INSERT INTO)
Used to add new records to a table.

### Insert a Student
```sql
INSERT INTO students (FirstName, LastName, Email)
VALUES ('John', 'Doe', 'johndoe@example.com');
```

### Insert a Department
```sql
INSERT INTO departments (Name, Address)
VALUES ('Computer Science', '123 University Road');
```

### Insert a Professor
```sql
INSERT INTO professors (FirstName, LastName, Email, DepartmentId)
VALUES ('Alice', 'Smith', 'alice.smith@university.edu', 1);
```

### Insert a Course
```sql
INSERT INTO courses (CourseID, Name, ProfessorId, DepartmentId)
VALUES ('CS101', 'Introduction to Programming', 1, 1);
```

### Insert a Study Group
```sql
INSERT INTO study_groups (CourseId, LeaderStudentId, GroupName, GroupType, ProfessorApproval)
VALUES ('CS101', 1, 'Python Wizards', 'Exam Prep', TRUE);
```

### Insert a Request to Join a Group
```sql
INSERT INTO requests (StudentId, GroupId, Status)
VALUES (2, 1, 'Pending');
```

---

## 2. Retrieving Data (SELECT)
Used to fetch data from a table.

### Retrieve All Students
```sql
SELECT * FROM students;
```

### Retrieve All Departments
```sql
SELECT * FROM departments;
```

### Retrieve All Study Groups for a Specific Course
```sql
SELECT * FROM study_groups WHERE CourseId = 'CS101';
```

### Retrieve All Requests for a Specific Study Group
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

---

## 3. Updating Data (UPDATE)
Used to modify existing records.

### Update a Student's Email
```sql
UPDATE students
SET Email = 'newemail@example.com'
WHERE StudentId = 1;
```

### Update a Department Name
```sql
UPDATE departments
SET Name = 'Software Engineering'
WHERE DepartmentId = 1;
```

### Approve a Pending Request
```sql
UPDATE requests
SET Status = 'Accepted'
WHERE RequestId = 2;
```

### Change the Leader of a Study Group
```sql
UPDATE study_groups
SET LeaderStudentId = 3
WHERE GroupId = 1;
```

---

## 4. Deleting Data (DELETE)
Used to remove records.

### Delete a Student
```sql
DELETE FROM students WHERE StudentId = 2;
```

### Delete a Department
```sql
DELETE FROM departments WHERE DepartmentId = 1;
```

### Delete a Study Group
```sql
DELETE FROM study_groups WHERE GroupId = 1;
```

### Delete All Pending Requests for a Specific Group
```sql
DELETE FROM requests WHERE GroupId = 1 AND Status = 'Pending';
```

---

## 5. Creating Relationships with JOIN
Used to combine data from multiple tables.

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

---

## Next Steps
- Try running these queries in `manage.php` and see the results.
- Modify data, insert more records, and experiment!
