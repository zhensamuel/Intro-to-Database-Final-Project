-- Enroll a student
DELIMITER //
DROP PROCEDURE IF EXISTS enroll_student_admin//
CREATE PROCEDURE enroll_student_admin(
    IN p_student_id INT,
    IN p_course_id INT
)
BEGIN
    IF EXISTS(SELECT 1 FROM Enrollments WHERE student_id = p_student_id AND course_id = p_course_id) THEN
       SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Student is already enrolled in this course';
    ELSE
       INSERT INTO Enrollments (student_id, course_id, enrollment_date) 
       VALUES (p_student_id, p_course_id, CURDATE());
    END IF;
END //
DELIMITER ;

-- Add a student
DELIMITER //
DROP PROCEDURE IF EXISTS add_student_admin//
CREATE PROCEDURE add_student_admin(
    IN p_student_name VARCHAR(50),
    IN p_program VARCHAR(50),
    IN p_email VARCHAR(50)
)
BEGIN
    INSERT INTO Students (student_name, program, email) 
    VALUES (p_student_name, p_program, p_email);
END //
DELIMITER ;

-- Update a student's grade
DELIMITER //
DROP PROCEDURE IF EXISTS update_grade_proc//
CREATE PROCEDURE update_grade_proc(
    IN p_enrollment_id INT,
    IN p_grade VARCHAR(10)
)
BEGIN
    UPDATE Enrollments
    SET grade = p_grade
    WHERE enrollment_id = p_enrollment_id;
END //
DELIMITER ;

-- Add a new Instructor
DELIMITER //
DROP PROCEDURE IF EXISTS add_instructor_admin//
CREATE PROCEDURE add_instructor_admin(
    IN p_instructor_name VARCHAR(50),
    IN p_department_id INT,
    IN p_email VARCHAR(50)
)
BEGIN
    INSERT INTO Instructors (name, department_id, email)
    VALUES (p_instructor_name, p_department_id, p_email);
END //
DELIMITER ;

-- Add a new department
DELIMITER //
DROP PROCEDURE IF EXISTS add_department//
CREATE PROCEDURE add_department(
    IN p_department_name VARCHAR(50)
)
BEGIN
    INSERT INTO Departments (name) VALUES (p_department_name);
END //
DELIMITER ;

-- List all Departments
DELIMITER //
DROP PROCEDURE IF EXISTS list_departments//
CREATE PROCEDURE list_departments()
BEGIN
    SELECT department_id, name FROM Departments ORDER BY department_id ASC;
END //
DELIMITER ;

-- Create a new course
DELIMITER //
DROP PROCEDURE IF EXISTS create_course//
CREATE PROCEDURE create_course(
    IN p_course_name VARCHAR(100),
    IN p_department_id INT
)
BEGIN
    INSERT INTO Courses (course_name, department_id)
    VALUES (p_course_name, p_department_id);
END //
DELIMITER ;

-- Create a new lecture  
DELIMITER //
DROP PROCEDURE IF EXISTS create_lecture//
CREATE PROCEDURE create_lecture(
    IN p_course_id INT,
    IN p_day VARCHAR(20),
    IN p_time VARCHAR(20),
    IN p_room VARCHAR(50),
    IN p_instructor_id INT
)
BEGIN
    INSERT INTO Lectures (course_id, day, time, room, instructor_id)
    VALUES (p_course_id, p_day, p_time, p_room, p_instructor_id);
END //
DELIMITER ;

-- Grade report
DELIMITER //
DROP PROCEDURE IF EXISTS grade_report//
CREATE PROCEDURE grade_report(IN p_student_id INT)
BEGIN
    SELECT 
        E.enrollment_id,
        S.student_name,
        C.course_name,
        I.name AS instructor,
        D.name AS department_name,
        E.grade
    FROM Enrollments E
    JOIN Students S ON E.student_id = S.student_id
    JOIN Courses C ON E.course_id = C.course_id
    LEFT JOIN Lectures L ON C.course_id = L.course_id
    LEFT JOIN Instructors I ON L.instructor_id = I.instructor_id
    LEFT JOIN Departments D ON C.department_id = D.department_id
    WHERE S.student_id = p_student_id
    ORDER BY C.course_name;
END //
DELIMITER ;

-- Retrieve all enrollments
DELIMITER //
DROP PROCEDURE IF EXISTS all_enrollments_report//
CREATE PROCEDURE all_enrollments_report()
BEGIN
    SELECT 
        E.enrollment_id,
        S.student_name,
        C.course_name,
        I.name AS instructor,
        D.name AS department_name,
        E.grade
    FROM Enrollments E
    JOIN Students S ON E.student_id = S.student_id
    JOIN Courses C ON E.course_id = C.course_id
    LEFT JOIN Lectures L ON C.course_id = L.course_id
    LEFT JOIN Instructors I ON L.instructor_id = I.instructor_id
    LEFT JOIN Departments D ON C.department_id = D.department_id
    ORDER BY S.student_name, C.course_name;
END //
DELIMITER ;
