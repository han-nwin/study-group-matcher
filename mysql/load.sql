-- Use the correct database
USE study_group_matcher;

-- Load Department data
LOAD DATA INFILE '/docker-entrypoint-initdb.d/department.csv'
INTO TABLE DEPARTMENT
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 LINES
(Name, Address);

-- Load Professor data
LOAD DATA INFILE '/docker-entrypoint-initdb.d/professor.csv'
INTO TABLE PROFESSOR
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 LINES
(FirstName, LastName, Email, Password, DepartmentId);

-- Load Course data
LOAD DATA INFILE '/docker-entrypoint-initdb.d/course.csv'
INTO TABLE COURSE
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 LINES
(CourseID, Name, DepartmentId, ProfessorId);

-- Load Student data
LOAD DATA INFILE '/docker-entrypoint-initdb.d/student.csv'
INTO TABLE STUDENT
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 LINES
(FirstName, LastName, Email, Password, DepartmentId);

-- Load Study Group data
LOAD DATA INFILE '/docker-entrypoint-initdb.d/study_group.csv'
INTO TABLE STUDY_GROUP
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 LINES
(CourseId, LeaderStudentId, GroupName, GroupType, Schedule, ProfessorApproval);

-- Load Enroll data
LOAD DATA INFILE '/docker-entrypoint-initdb.d/enroll.csv'
INTO TABLE ENROLL
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 LINES
(StudentId, CourseId);

-- Load Request data
LOAD DATA INFILE '/docker-entrypoint-initdb.d/request.csv'
INTO TABLE REQUEST
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 LINES
(StudentId, GroupId, Status);
