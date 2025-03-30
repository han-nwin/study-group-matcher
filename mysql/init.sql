-- Create Database
CREATE DATABASE IF NOT EXISTS study_group_matcher;
USE study_group_matcher;

--  Create Tables
CREATE TABLE IF NOT EXISTS DEPARTMENT (
    DepartmentId INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    Address TEXT
);

CREATE TABLE IF NOT EXISTS PROFESSOR (
    ProfessorId INT AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    DepartmentId INT,
    FOREIGN KEY (DepartmentId) REFERENCES DEPARTMENT(DepartmentId) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS COURSE (
    CourseID VARCHAR(20) PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    DepartmentId INT,
    ProfessorId INT,
    FOREIGN KEY (DepartmentId) REFERENCES DEPARTMENT(DepartmentId) ON DELETE SET NULL,
    FOREIGN KEY (ProfessorId) REFERENCES PROFESSOR(ProfessorId) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS STUDENT (
    StudentId INT AUTO_INCREMENT PRIMARY KEY UNIQUE,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,
    DepartmentId INT,
    FOREIGN KEY (DepartmentId) REFERENCES DEPARTMENT(DepartmentId) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS STUDY_GROUP (
    GroupId INT AUTO_INCREMENT PRIMARY KEY,
    CourseId VARCHAR(20) NOT NULL,
    LeaderStudentId INT,
    GroupName VARCHAR(100),
    GroupType VARCHAR(50),
    Schedule JSON,
    ProfessorApproval BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (CourseId) REFERENCES COURSE(CourseID) ON DELETE CASCADE,
    FOREIGN KEY (LeaderStudentId) REFERENCES STUDENT(StudentId) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS REQUEST (
    StudentId INT,
    GroupId INT,
    Status ENUM('Pending', 'Accepted', 'Rejected') DEFAULT 'Pending',
    PRIMARY KEY (StudentId, GroupId),
    FOREIGN KEY (StudentId) REFERENCES STUDENT(StudentId) ON DELETE CASCADE,
    FOREIGN KEY (GroupId) REFERENCES STUDY_GROUP(GroupId) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS ENROLL (
    StudentId INT,
    CourseId VARCHAR(20),
    PRIMARY KEY (StudentId, CourseId),
    FOREIGN KEY (StudentId) REFERENCES STUDENT(StudentId) ON DELETE CASCADE,
    FOREIGN KEY (CourseId) REFERENCES COURSE(CourseID) ON DELETE CASCADE
);

-- Insert Sample Data
-- Insert Departments
INSERT INTO DEPARTMENT (Name, Address) VALUES
('Computer Science', '123 Tech Building, University Campus'),
('Mathematics', '456 Math Hall, University Campus'),
('Physics', '789 Science Center, University Campus');

-- Insert Professors (passwords are hashed using SHA-1 for 'prof123')
INSERT INTO PROFESSOR (FirstName, LastName, Email, Password, DepartmentId) VALUES
('John', 'Smith', 'john.smith@university.edu', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 1), -- Computer Science
('Emily', 'Johnson', 'emily.johnson@university.edu', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 2), -- Mathematics
('Michael', 'Brown', 'michael.brown@university.edu', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 3); -- Physics

-- Insert Courses
INSERT INTO COURSE (CourseID, Name, DepartmentId, ProfessorId) VALUES
('CS101', 'Introduction to Programming', 1, 1), -- Computer Science, John Smith
('MATH201', 'Calculus I', 2, 2), -- Mathematics, Emily Johnson
('PHYS101', 'Physics I', 3, 3); -- Physics, Michael Brown

-- Insert Students (passwords are hashed using SHA-1 for 'student123')
INSERT INTO STUDENT (FirstName, LastName, Email, Password, DepartmentId) VALUES
('Alice', 'Davis', 'alice.davis@university.edu', 'a948904f2f0f479b8f8197694b30184b0d2ed1c', 1), -- Computer Science
('Bob', 'Wilson', 'bob.wilson@university.edu', 'a948904f2f0f479b8f8197694b30184b0d2ed1c', 1), -- Computer Science
('Charlie', 'Miller', 'charlie.miller@university.edu', 'a948904f2f0f479b8f8197694b30184b0d2ed1c', 2); -- Mathematics

-- Insert Study Groups
INSERT INTO STUDY_GROUP (CourseId, LeaderStudentId, GroupName, GroupType, Schedule, ProfessorApproval) VALUES
('CS101', 1, 'CS101 Study Group 1', 'Collaborative', '[{"day": "Monday", "start": "14:00", "end": "16:00"}]', TRUE),
('CS101', 2, 'CS101 Study Group 2', 'Peer-led', '[{"day": "Wednesday", "start": "10:00", "end": "12:00"}]', FALSE),
('MATH201', 3, 'Calculus Study Group', 'Collaborative', '[{"day": "Friday", "start": "13:00", "end": "15:00"}]', TRUE);

-- Insert Requests
INSERT INTO REQUEST (StudentId, GroupId, Status) VALUES
(2, 1, 'Pending'), -- Bob requests to join Alice's group
(3, 1, 'Accepted'), -- Charlie is accepted into Alice's group
(1, 3, 'Pending'); -- Alice requests to join Charlie's group

-- Insert Enrollments
INSERT INTO ENROLL (StudentId, CourseId) VALUES
(1, 'CS101'), -- Alice enrolled in CS101
(2, 'CS101'), -- Bob enrolled in CS101
(3, 'MATH201'); -- Charlie enrolled in MATH201
