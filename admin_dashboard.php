<?php
// admin_dashboard.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php'; //Communicates with the DB.

// Process enrollment submission (Student Management tab)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll_student'])) {
    $student_id = intval($_POST['student_id']);
    $course_id  = intval($_POST['course_id']);
    try {
        $stmt = $pdo->prepare("CALL enroll_student_admin(?, ?)");
        $stmt->execute([$student_id, $course_id]);
        $stmt->closeCursor();
        header("Location: admin_dashboard.php?feature=student_management&status=enroll_success");
        exit;
    } catch (PDOException $e) {
        header("Location: admin_dashboard.php?feature=student_management&status=enroll_error&msg=" . urlencode($e->getMessage()));
        exit;
    }
}

//Add New Student(Student Management tab)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $student_name = trim($_POST['student_name']);
    $program      = trim($_POST['program']);
    $email        = trim($_POST['email']);
    try {
        $stmt = $pdo->prepare("CALL add_student_admin(?, ?, ?)");
        $stmt->execute([$student_name, $program, $email]);
        $stmt->closeCursor();
        header("Location: admin_dashboard.php?feature=student_management&status=student_success");
        exit;
    } catch (PDOException $e) {
        header("Location: admin_dashboard.php?feature=student_management&status=student_error&msg=" . urlencode($e->getMessage()));
        exit;
    }
}

//Add New Instructor (Instructors tab)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_instructor'])) {
    $instructor_name = trim($_POST['instructor_name']);
    $department_id   = intval($_POST['department_id']);
    $email           = trim($_POST['email']);
    try {
        $stmt = $pdo->prepare("CALL add_instructor_admin(?, ?, ?)");
        $stmt->execute([$instructor_name, $department_id, $email]);
        $stmt->closeCursor();
        header("Location: admin_dashboard.php?feature=instructors&status=instructor_success");
        exit;
    } catch (PDOException $e) {
        header("Location: admin_dashboard.php?feature=instructors&status=instructor_error&msg=" . urlencode($e->getMessage()));
        exit;
    }
}

//Add New Department (Departments section in Instructors tab)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_department'])) {
    $department_name = trim($_POST['department_name']);
    try {
        $stmt = $pdo->prepare("CALL add_department(?)");
        $stmt->execute([$department_name]);
        $stmt->closeCursor();
        header("Location: admin_dashboard.php?feature=instructors&status=department_success");
        exit;
    } catch (PDOException $e) {
        header("Location: admin_dashboard.php?feature=instructors&status=department_error&msg=" . urlencode($e->getMessage()));
        exit;
    }
}

//Update Grade (Grade Report tab)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_grade'])) {
    $enrollment_id = intval($_POST['enrollment_id']);
    $grade = trim($_POST['grade']);
    try {
        $stmt = $pdo->prepare("CALL update_grade_proc(?, ?)");
        $stmt->execute([$enrollment_id, $grade]);
        $stmt->closeCursor();
        header("Location: admin_dashboard.php?feature=grade_report&status=grade_update_success");
        exit;
    } catch (PDOException $e) {
        header("Location: admin_dashboard.php?feature=grade_report&status=grade_update_error&msg=" . urlencode($e->getMessage()));
        exit;
    }
}

//Create New Course (Course Management tab)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_course'])) {
    $course_name = trim($_POST['course_name']);
    $department_id = intval($_POST['department_id']);
    try {
        $stmt = $pdo->prepare("CALL create_course(?, ?)");
        $stmt->execute([$course_name, $department_id]);
        $stmt->closeCursor();
        header("Location: admin_dashboard.php?feature=course_management&status=course_success");
        exit;
    } catch (PDOException $e) {
        header("Location: admin_dashboard.php?feature=course_management&status=course_error&msg=" . urlencode($e->getMessage()));
        exit;
    }
}

//Create New Lecture (Course Management tab)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_lecture'])) {
    $course_id = intval($_POST['course_id']);
    $day = trim($_POST['day']);
    $time = trim($_POST['time']);
    $room = trim($_POST['room']);
    $instructor_id = intval($_POST['instructor_id']);
    try {
        $stmt = $pdo->prepare("CALL create_lecture(?, ?, ?, ?, ?)");
        $stmt->execute([$course_id, $day, $time, $room, $instructor_id]);
        $stmt->closeCursor();
        header("Location: admin_dashboard.php?feature=course_management&status=lecture_success");
        exit;
    } catch (PDOException $e) {
        header("Location: admin_dashboard.php?feature=course_management&status=lecture_error&msg=" . urlencode($e->getMessage()));
        exit;
    }
}

//Fetches data for Overview
try {
    $stmt = $pdo->query("SELECT COUNT(*) AS total_students FROM Students");
    $total_students = $stmt->fetch()['total_students'];

    $stmt = $pdo->query("SELECT COUNT(*) AS total_instructors FROM Instructors");
    $total_instructors = $stmt->fetch()['total_instructors'];

    $stmt = $pdo->query("SELECT COUNT(*) AS total_courses FROM Courses");
    $total_courses = $stmt->fetch()['total_courses'];

//Shows the recent enrollments
    $stmt = $pdo->query("
      SELECT E.enrollment_id, S.student_name, C.course_name, E.enrollment_date, E.grade 
      FROM Enrollments E 
      JOIN Students S ON E.student_id = S.student_id
      JOIN Courses C ON E.course_id = C.course_id
      ORDER BY E.enrollment_date DESC
    ");
    $recent_enrollments = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

$feature = isset($_GET['feature']) ? $_GET['feature'] : 'overview';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header>
    <h1>Admin Dashboard</h1>
</header>
<nav>
    <a href="admin_dashboard.php?feature=overview">Overview</a>
    <a href="admin_dashboard.php?feature=student_management">Student Management</a>
    <a href="admin_dashboard.php?feature=instructors">Instructors</a>
    <a href="admin_dashboard.php?feature=grade_report">Grade Report</a>
    <a href="admin_dashboard.php?feature=schedule">Student Schedule</a>
    <a href="admin_dashboard.php?feature=course_management">Course Management</a>
</nav>
<div class="container">
<?php if ($feature === 'overview'): ?>
    <!-- Overview Tab -->
    <h2>Overview</h2>
    <table>
        <thead>
            <tr>
                <th>Total Students</th>
                <th>Total Instructors</th>
                <th>Total Courses</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?php echo htmlspecialchars($total_students); ?></td>
                <td><?php echo htmlspecialchars($total_instructors); ?></td>
                <td><?php echo htmlspecialchars($total_courses); ?></td>
            </tr>
        </tbody>
    </table>
    <h3>Recent Enrollments</h3>
    <table>
       <thead>
           <tr>
               <th>Enrollment ID</th>
               <th>Student Name</th>
               <th>Course Name</th>
               <th>Enrollment Date</th>
               <th>Grade</th>
           </tr>
       </thead>
       <tbody>
           <?php foreach ($recent_enrollments as $enrollment): ?>
               <tr>
                   <td><?php echo htmlspecialchars($enrollment['enrollment_id']); ?></td>
                   <td><?php echo htmlspecialchars($enrollment['student_name']); ?></td>
                   <td><?php echo htmlspecialchars($enrollment['course_name']); ?></td>
                   <td><?php echo htmlspecialchars($enrollment['enrollment_date']); ?></td>
                   <td><?php echo htmlspecialchars($enrollment['grade']); ?></td>
               </tr>
           <?php endforeach; ?>
       </tbody>
    </table>

<?php elseif ($feature === 'student_management'): ?>
    <!-- Student Management Tab -->
    <h2>Student Management</h2>
    <!-- Enrollment Sub-Section -->
    <h3>Enroll a Student in a Course</h3>
    <form method="post" action="admin_dashboard.php?feature=student_management">
        <label for="student_id">Student ID:</label><br>
        <input type="number" name="student_id" id="student_id" required><br>
        <label for="course_id">Course ID:</label><br>
        <input type="number" name="course_id" id="course_id" required><br>
        <button type="submit" name="enroll_student">Enroll Student</button>
    </form>
    <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] === 'enroll_success') {
                echo '<p class="message" style="color:green;">Enrollment successful!</p>';
            } elseif ($_GET['status'] === 'enroll_error' && isset($_GET['msg'])) {
                echo '<p class="message" style="color:red;">Error: ' . htmlspecialchars($_GET['msg']) . '</p>';
            }
        }
    ?>
    <hr>
    <!-- Add New Student Sub-Section -->
    <h3>Add New Student</h3>
    <form method="post" action="admin_dashboard.php?feature=student_management">
        <label for="student_name">Student Name:</label><br>
        <input type="text" name="student_name" id="student_name" required><br>
        <label for="program">Program:</label><br>
        <input type="text" name="program" id="program" required><br>
        <label for="email">Email:</label><br>
        <input type="email" name="email" id="email" required><br>
        <button type="submit" name="add_student">Add Student</button>
    </form>
    <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] === 'student_success') {
                echo '<p class="message" style="color:green;">Student added successfully!</p>';
            } elseif ($_GET['status'] === 'student_error' && isset($_GET['msg'])) {
                echo '<p class="message" style="color:red;">Error: ' . htmlspecialchars($_GET['msg']) . '</p>';
            }
        }
    ?>
    <hr>
    <!-- Student Search -->
    <h3>Search / View Students</h3>
    <form method="get" action="admin_dashboard.php">
        <input type="hidden" name="feature" value="student_management">
        <label for="search_student_id">Search Student by ID:</label>
        <input type="number" name="search_student_id" id="search_student_id">
        <button type="submit">Search</button>
    </form>
    <?php
        if (isset($_GET['search_student_id']) && !empty($_GET['search_student_id'])) {
            $s_id = intval($_GET['search_student_id']);
            try {
                $stmt = $pdo->prepare("SELECT * FROM Students WHERE student_id = ?");
                $stmt->execute([$s_id]);
                $searched_student = $stmt->fetch();
                $stmt->closeCursor();
            } catch (PDOException $e) {
                echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
                $searched_student = false;
            }
            if ($searched_student) {
                echo "<h4>Student Details for ID " . htmlspecialchars($s_id) . ":</h4>";
                echo "<table><thead><tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Program</th>
                        <th>Email</th>
                      </tr></thead><tbody>";
                echo "<tr>";
                echo "<td>" . htmlspecialchars($searched_student['student_id']) . "</td>";
                echo "<td>" . htmlspecialchars($searched_student['student_name']) . "</td>";
                echo "<td>" . htmlspecialchars($searched_student['program']) . "</td>";
                echo "<td>" . htmlspecialchars($searched_student['email']) . "</td>";
                echo "</tr>";
                echo "</tbody></table>";
            } else {
                echo "<p>No student found with ID " . htmlspecialchars($s_id) . ".</p>";
            }
        }
        //Displays all students.
        try {
            $stmt = $pdo->query("SELECT * FROM Students ORDER BY student_id ASC");
            $students = $stmt->fetchAll();
            $stmt->closeCursor();
        } catch (PDOException $e) {
            echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            $students = [];
        }
    ?>
    <h4>All Students</h4>
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Program</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                    <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($student['program']); ?></td>
                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

<?php elseif ($feature === 'instructors'): ?>
    <!-- Instructors Tab (with Departments section) -->
    <h2>Instructor Management</h2>
    <!-- View All Instructors -->
    <h3>All Instructors</h3>
    <?php
        try {
            $stmt = $pdo->query("SELECT I.instructor_id, I.name, I.email, D.name AS department_name 
                                  FROM Instructors I 
                                  LEFT JOIN Departments D ON I.department_id = D.department_id
                                  ORDER BY I.instructor_id ASC");
            $instructors = $stmt->fetchAll();
            $stmt->closeCursor();
        } catch (PDOException $e) {
            echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            $instructors = [];
        }
    ?>
    <table>
        <thead>
            <tr>
                <th>Instructor ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($instructors as $inst): ?>
                <tr>
                    <td><?php echo htmlspecialchars($inst['instructor_id']); ?></td>
                    <td><?php echo htmlspecialchars($inst['name']); ?></td>
                    <td><?php echo htmlspecialchars($inst['email']); ?></td>
                    <td><?php echo htmlspecialchars($inst['department_name'] ?? 'N/A'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <hr>
    <!-- Add New Instructor Form -->
    <h3>Add New Instructor</h3>
    <form method="post" action="admin_dashboard.php?feature=instructors">
        <label for="instructor_name">Instructor Name:</label><br>
        <input type="text" name="instructor_name" id="instructor_name" required><br>
        <label for="department_id">Department ID:</label><br>
        <input type="number" name="department_id" id="department_id" required><br>
        <label for="email">Email:</label><br>
        <input type="email" name="email" id="email" required><br>
        <button type="submit" name="add_instructor">Add Instructor</button>
    </form>
    <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] === 'instructor_success') {
                echo '<p class="message" style="color:green;">Instructor added successfully!</p>';
            } elseif ($_GET['status'] === 'instructor_error' && isset($_GET['msg'])) {
                echo '<p class="message" style="color:red;">Error: ' . htmlspecialchars($_GET['msg']) . '</p>';
            }
        }
    ?>
    <hr>
    <!-- Departments Section -->
    <h2>Departments</h2>
    <!-- View All Departments -->
    <h3>All Departments</h3>
    <?php
        try {
            $stmt = $pdo->query("CALL list_departments()");
            $departments = $stmt->fetchAll();
            $stmt->closeCursor();
        } catch (PDOException $e) {
            echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            $departments = [];
        }
    ?>
    <table>
        <thead>
            <tr>
                <th>Department ID</th>
                <th>Department Name</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($departments as $dept): ?>
                <tr>
                    <td><?php echo htmlspecialchars($dept['department_id']); ?></td>
                    <td><?php echo htmlspecialchars($dept['name']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <hr>
    <!-- Add New Department Form -->
    <h3>Add New Department</h3>
    <form method="post" action="admin_dashboard.php?feature=instructors">
        <label for="department_name">Department Name:</label><br>
        <input type="text" name="department_name" id="department_name" required><br>
        <button type="submit" name="add_department">Add Department</button>
    </form>
    <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] === 'department_success') {
                echo '<p class="message" style="color:green;">Department added successfully!</p>';
            } elseif ($_GET['status'] === 'department_error' && isset($_GET['msg'])) {
                echo '<p class="message" style="color:red;">Error: ' . htmlspecialchars($_GET['msg']) . '</p>';
            }
        }
    ?>

<?php elseif ($feature === 'grade_report'): ?>
    <!-- Grade Report Tab -->
    <h2>Grade Report</h2>
    <!-- View Grade Report for a Specific Student -->
    <h3>View Grade Report by Student</h3>
    <form method="get" action="admin_dashboard.php">
        <input type="hidden" name="feature" value="grade_report">
        <label for="student_id">Student ID:</label>
        <input type="number" name="student_id" id="student_id" required>
        <button type="submit">Generate Grade Report</button>
    </form>
    <?php
    if (isset($_GET['student_id'])) {
        $student_id = intval($_GET['student_id']);
        try {
            $stmt = $pdo->prepare("CALL grade_report(?)");
            $stmt->execute([$student_id]);
            $grade_report = $stmt->fetchAll();
            $stmt->closeCursor();
        } catch (PDOException $e) {
            echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            $grade_report = [];
        }
        if ($grade_report) {
            echo "<h3>Grade Report for Student ID " . htmlspecialchars($student_id) . "</h3>";
            echo "<table><thead><tr>
                    <th>Enrollment ID</th>
                    <th>Student Name</th>
                    <th>Course Name</th>
                    <th>Instructor</th>
                    <th>Department</th>
                    <th>Grade</th>
                </tr></thead><tbody>";
            foreach ($grade_report as $record) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($record['enrollment_id'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($record['student_name'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($record['course_name'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($record['instructor'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($record['department_name'] ?? 'N/A') . "</td>";
                echo "<td>" . htmlspecialchars($record['grade'] ?? 'N/A') . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p>No grade report available for Student ID " . htmlspecialchars($student_id) . ".</p>";
        }
    }
    ?>
    <hr>
    <!-- View All Enrollments -->
    <h3>All Enrollments</h3>
    <?php
    try {
        $stmt = $pdo->query("CALL all_enrollments_report()");
        $all_enrollments = $stmt->fetchAll();
        $stmt->closeCursor();
    } catch (PDOException $e) {
        echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        $all_enrollments = [];
    }
    if ($all_enrollments) {
        echo "<table><thead><tr>
                <th>Enrollment ID</th>
                <th>Student Name</th>
                <th>Course Name</th>
                <th>Instructor</th>
                <th>Department</th>
                <th>Grade</th>
            </tr></thead><tbody>";
        foreach ($all_enrollments as $record) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($record['enrollment_id'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($record['student_name'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($record['course_name'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($record['instructor'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($record['department_name'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($record['grade'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>No enrollments found in the system.</p>";
    }
    ?>
    <hr>
    <!-- Update Grade Sub-Section -->
    <h3>Update Grade for Enrollment</h3>
    <form method="post" action="admin_dashboard.php?feature=grade_report">
        <label for="enrollment_id">Enrollment ID:</label><br>
        <input type="number" name="enrollment_id" id="enrollment_id" required><br>
        <label for="grade">Grade:</label><br>
        <input type="text" name="grade" id="grade" required><br>
        <button type="submit" name="update_grade">Update Grade</button>
    </form>
    <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] === 'grade_update_success') {
                echo '<p class="message" style="color:green;">Grade updated successfully!</p>';
            } elseif ($_GET['status'] === 'grade_update_error' && isset($_GET['msg'])) {
                echo '<p class="message" style="color:red;">Error: ' . htmlspecialchars($_GET['msg']) . '</p>';
            }
        }
    ?>

<?php elseif ($feature === 'schedule'): ?>
    <!-- Student Schedule Tab -->
    <h2>Student Schedule</h2>
    <form method="get" action="admin_dashboard.php">
        <input type="hidden" name="feature" value="schedule">
        <label for="schedule_student_id">Student ID:</label>
        <input type="number" name="student_id" id="schedule_student_id" required>
        <button type="submit">Generate Schedule</button>
    </form>
    <?php
        if (isset($_GET['student_id'])) {
            $student_id = intval($_GET['student_id']);
            try {
                $stmt = $pdo->prepare("CALL generate_student_schedule(?)");
                $stmt->execute([$student_id]);
                $schedule = $stmt->fetchAll();
                $stmt->closeCursor();
            } catch (PDOException $e) {
                echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
                $schedule = [];
            }
            if ($schedule) {
                echo "<h3>Schedule for Student ID " . htmlspecialchars($student_id) . "</h3>";
                echo "<table><thead><tr>
                    <th>Student Name</th>
                    <th>Course Name</th>
                    <th>Day</th>
                    <th>Time</th>
                    <th>Instructor</th>
                    <th>Room</th>
                </tr></thead><tbody>";
                foreach ($schedule as $row) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['student_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['course_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['day']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['time']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['instructor']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['room']) . "</td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p>No schedule available for Student ID " . htmlspecialchars($student_id) . ".</p>";
            }
        }
    ?>

<?php elseif ($feature === 'course_management'): ?>
    <!-- Course Management Tab -->
    <h2>Course Management</h2>
    <!-- Overview of Courses -->
    <h3>All Courses</h3>
    <?php
        try {
            $stmt = $pdo->query("SELECT * FROM Courses ORDER BY course_id ASC");
            $courses = $stmt->fetchAll();
            $stmt->closeCursor();
        } catch (PDOException $e) {
            echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            $courses = [];
        }
    ?>
    <table>
        <thead>
            <tr>
                <th>Course ID</th>
                <th>Course Name</th>
                <th>Department ID</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?php echo htmlspecialchars($course['course_id']); ?></td>
                    <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($course['department_id']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <!-- Overview of Lectures -->
    <h3>All Lectures</h3>
    <?php
        try {
            $stmt = $pdo->query("
              SELECT L.*, C.course_name, D.name AS department_name, I.name AS instructor
              FROM Lectures L 
              JOIN Courses C ON L.course_id = C.course_id
              LEFT JOIN Departments D ON C.department_id = D.department_id
              LEFT JOIN Instructors I ON L.instructor_id = I.instructor_id
              ORDER BY L.lecture_id ASC
            ");
            $lectures = $stmt->fetchAll();
            $stmt->closeCursor();
        } catch (PDOException $e) {
            echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            $lectures = [];
        }
    ?>
    <table>
        <thead>
            <tr>
                <th>Lecture ID</th>
                <th>Course Name</th>
                <th>Day</th>
                <th>Time</th>
                <th>Room</th>
                <th>Instructor</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lectures as $lecture): ?>
                <tr>
                    <td><?php echo htmlspecialchars($lecture['lecture_id']); ?></td>
                    <td><?php echo htmlspecialchars($lecture['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($lecture['day']); ?></td>
                    <td><?php echo htmlspecialchars($lecture['time']); ?></td>
                    <td><?php echo htmlspecialchars($lecture['room']); ?></td>
                    <td><?php echo htmlspecialchars($lecture['instructor']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <hr>
    <!-- Create New Course Form -->
    <h3>Create New Course</h3>
    <form method="post" action="admin_dashboard.php?feature=course_management">
        <label for="course_name">Course Name:</label><br>
        <input type="text" name="course_name" id="course_name" required><br>
        <label for="department_id">Department ID:</label><br>
        <input type="number" name="department_id" id="department_id" required><br>
        <button type="submit" name="create_course">Create Course</button>
    </form>
    <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] === 'course_success') {
                echo '<p class="message" style="color:green;">Course created successfully!</p>';
            } elseif ($_GET['status'] === 'course_error' && isset($_GET['msg'])) {
                echo '<p class="message" style="color:red;">Error: ' . htmlspecialchars($_GET['msg']) . '</p>';
            }
        }
    ?>
    <hr>
    <!-- Create New Lecture Form -->
    <h3>Create New Lecture</h3>
    <form method="post" action="admin_dashboard.php?feature=course_management">
        <label for="course_id">Course ID:</label><br>
        <input type="number" name="course_id" id="course_id" required><br>
        <label for="day">Day:</label><br>
        <input type="text" name="day" id="day" required><br>
        <label for="time">Time:</label><br>
        <input type="text" name="time" id="time" required><br>
        <label for="room">Room:</label><br>
        <input type="text" name="room" id="room" required><br>
        <label for="instructor_id">Instructor ID:</label><br>
        <input type="number" name="instructor_id" id="instructor_id" required><br>
        <button type="submit" name="create_lecture">Create Lecture</button>
    </form>
    <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] === 'lecture_success') {
                echo '<p class="message" style="color:green;">Lecture created successfully!</p>';
            } elseif ($_GET['status'] === 'lecture_error' && isset($_GET['msg'])) {
                echo '<p class="message" style="color:red;">Error: ' . htmlspecialchars($_GET['msg']) . '</p>';
            }
        }
    ?>

<?php endif; //End of feature switch ?> 
</div>
</body>
</html>
