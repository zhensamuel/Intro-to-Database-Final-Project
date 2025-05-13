INSERT INTO Departments (name) VALUES 
('Computer Science'),
('Mathematics'),
('Physics'),
('Chemistry'),
('Biology');

INSERT INTO Instructors (name, department_id, email) VALUES 
('Alice Johnson', 1, 'alice.johnson@university.edu'),
('Bob Smith', 2, 'bob.smith@university.edu'),
('Charlie Brown', 3, 'charlie.brown@university.edu'),
('Dana White', 4, 'dana.white@university.edu'),
('Evan Taylor', 5, 'evan.taylor@university.edu');

INSERT INTO Students (student_name, program, email) VALUES 
('David Miller', 'BS Computer Science', 'david.miller@university.edu'),
('Eva Green', 'BS Mathematics', 'eva.green@university.edu'),
('Frank Wright', 'BS Physics', 'frank.wright@university.edu'),
('Gina Davis', 'BS Chemistry', 'gina.davis@university.edu'),
('Henry Adams', 'BS Biology', 'henry.adams@university.edu');

INSERT INTO Courses (course_name, department_id) VALUES 
('Intro to Programming', 1),
('Calculus I', 2),
('Mechanics', 3),
('Organic Chemistry', 4),
('Genetics', 5);

INSERT INTO Lectures (course_id, day, time, room, instructor_id) VALUES 
(1, 'Monday', '10:00', 'Room 101', 1),
(2, 'Tuesday', '09:00', 'Room 202', 2),
(3, 'Wednesday', '14:00', 'Room 303', 3),
(4, 'Thursday', '11:00', 'Room 404', 4),
(5, 'Friday', '13:00', 'Room 505', 5);

INSERT INTO Enrollments (student_id, course_id, enrollment_date, grade) VALUES 
(1, 1, '2025-05-01', 'A'),
(2, 2, '2025-05-02', 'B+'),
(3, 3, '2025-05-03', 'A-'),
(4, 4, '2025-05-04', 'B'),
(5, 5, '2025-05-05', 'A+');

-- Original insert was lost so inserts here are different than what is in the DB, but the syntax is the same.