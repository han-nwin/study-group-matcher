-- Create Database
CREATE DATABASE IF NOT EXISTS study_group_matcher;
USE study_group_matcher;

-- Create Tables (unchanged from your schema)
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
('Physics', '789 Science Center, University Campus'),
('Biology', '101 Bio Lab, University Campus'),
('English', '202 Lit Hall, University Campus');

-- Insert Professors (using bcrypt hashes instead of SHA-1; password 'prof123' for simplicity)
INSERT INTO PROFESSOR (FirstName, LastName, Email, Password, DepartmentId) VALUES
('John', 'Smith', 'john.smith@university.edu', '$2y$10$exampleHashForProf123abc', 1), -- CS
('Emily', 'Johnson', 'emily.johnson@university.edu', '$2y$10$exampleHashForProf123def', 2), -- Math
('Michael', 'Brown', 'michael.brown@university.edu', '$2y$10$exampleHashForProf123ghi', 3), -- Physics
('Professor', 'Nguyen', 'professorNguyen@utdallas.edu', '$2y$12$Ud2lhb4yRH912Y.kvSejhukqWg8YGcOhOcMBkEPID.DCR9b8soq1K', 1), -- CS
('Sarah', 'Lee', 'sarah.lee@university.edu', '$2y$10$exampleHashForProf123jkl', 4), -- Biology (New)
('David', 'Clark', 'david.clark@university.edu', '$2y$10$exampleHashForProf123mno', 5); -- English (New)

-- Insert Courses 
INSERT INTO COURSE (CourseID, Name, DepartmentId, ProfessorId) VALUES
('CS101', 'Introduction to Programming', 1, 1), -- CS, John Smith
('MATH201', 'Calculus I', 2, 2), -- Math, Emily Johnson
('PHYS101', 'Physics I', 3, 3), -- Physics, Michael Brown
('BIO101', 'Intro to Biology', 4, 5), -- Biology, Sarah Lee
('ENG101', 'English Literature', 5, 6), -- English, David Clark
('CS202', 'Data Structures', 1, 4); -- CS, Professor Nguyen
-- Inser Courses without a professor
INSERT INTO COURSE (CourseID, Name, DepartmentId) VALUES
('CS102', 'Programming Fundamental I', 1), -- CS, 'No Professor'
('CS103', 'Programming Fundamental II', 1), -- CS, 'No Professor'
('MATH202', 'Calculus II', 2), -- Math, 'No Professor'
('PHYS102', 'Physics 2', 3); -- Physics, 'No Professor'

-- Insert Students
INSERT INTO STUDENT (FirstName, LastName, Email, Password, DepartmentId) VALUES
('Alice', 'Davis', 'alice.davis@university.edu', '$2y$10$exampleHashForStud123abc', 1), -- CS
('Bob', 'Wilson', 'bob.wilson@university.edu', '$2y$10$exampleHashForStud123def', 1), -- CS
('Charlie', 'Miller', 'charlie.miller@university.edu', '$2y$10$exampleHashForStud123ghi', 2), -- Math
('Tan Han', 'Nguyen', 'txn200004@utdallas.edu', '$2y$12$cLmg797CwTSbmg8qGXBPpODmbkLWLyS.x5LjM3dKBFhSnkmU2PT72', 1), -- CS
('Diana', 'Evans', 'diana.evans@university.edu', '$2y$10$exampleHashForStud123jkl', 4), -- Biology
('Eve', 'Taylor', 'eve.taylor@university.edu', '$2y$10$exampleHashForStud123mno', 5), -- English 
('Frank', 'Harris', 'frank.harris@university.edu', '$2y$10$exampleHashForStud123pqr', 1); -- CS

-- Insert Study Groups
INSERT INTO STUDY_GROUP (CourseId, LeaderStudentId, GroupName, GroupType, Schedule, ProfessorApproval) VALUES
('CS101', 1, 'CS101 Study Group 1', 'Collaborative', '[{"day": "Monday", "start": "14:00", "end": "16:00"}]', TRUE), -- Alice leads
('CS101', 2, 'CS101 Study Group 2', 'Peer-led', '[{"day": "Wednesday", "start": "10:00", "end": "12:00"}]', FALSE), -- Bob leads
('MATH201', 3, 'Calculus Study Group', 'Collaborative', '[{"day": "Friday", "start": "13:00", "end": "15:00"}]', TRUE), -- Charlie leads
('BIO101', 5, 'Biology Study Group', 'Peer-led', '[{"day": "Tuesday", "start": "15:00", "end": "17:00"}]', FALSE), -- Diana leads
('ENG101', 6, 'Lit Discussion Group', 'Collaborative', '[{"day": "Thursday", "start": "11:00", "end": "13:00"}]', TRUE), -- Eve leads 
('CS202', 7, 'Data Structures Group', 'Study', '[{"day": "Monday", "start": "16:00", "end": "18:00"}]', FALSE); -- Frank leads

-- Insert Enrollments
INSERT INTO ENROLL (StudentId, CourseId) VALUES
(1, 'CS101'), -- Alice in CS101
(2, 'CS101'), -- Bob in CS101
(3, 'MATH201'), -- Charlie in MATH201
(4, 'CS101'), -- Tan Han in CS101
(4, 'CS202'), -- Tan Han in CS202
(5, 'BIO101'), -- Diana in BIO101
(6, 'ENG101'), -- Eve in ENG101
(7, 'CS101'), -- Frank in CS101
(7, 'CS202'); -- Frank in CS202

-- Insert Requests
INSERT INTO REQUEST (StudentId, GroupId, Status) VALUES
(2, 1, 'Pending'), -- Bob requests Alice’s CS101 group (enrolled in CS101)
(4, 1, 'Accepted'), -- Tan Han requests Alice’s CS101 group (enrolled in CS101)
(4, 6, 'Pending'), -- Tan Han requests Frank’s CS202 group (enrolled in CS202)
(7, 1, 'Rejected'), -- Frank requests Alice’s CS101 group (enrolled in CS101)
(5, 4, 'Accepted'), -- Diana requests her own BIO101 group (enrolled in BIO101)
(6, 5, 'Pending'); -- Eve requests her own ENG101 group (enrolled in ENG101)
