<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

$admin_username = $_SESSION['username'];

// Include database connection
include 'db.php';

// Call the stored procedure to get all students
$stmt = $userDB->prepare("CALL GetAllStudents()");
$stmt->execute();
$result = $stmt->get_result();

// Fetch all students
$students = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$userDB->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/admin.png">
    <title>Admin Panel</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #222;
            color: #ddd;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 0 20px;
        }

        .dashboard-container {
            max-width: 1200px;
            width: 100%;
            background-color: #333;
            color: #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .dashboard-header {
            background: #444;
            padding: 30px;
            color: #fff;
            text-align: center;
            border-bottom: 3px solid #00BFFF; /* Light blue accent */
        }

        .dashboard-header h1 {
            font-size: 2.4rem;
            margin-bottom: 10px;
        }

        .welcome-message {
            font-size: 1.1rem;
            color: #ccc;
        }

        .nav-bar {
            display: flex;
            justify-content: space-around;
            padding: 15px;
            background: #555;
        }

        .nav-bar a {
            color: white;
            font-weight: bold;
            text-decoration: none;
            padding: 10px 20px;
            background: #666;
            border-radius: 4px;
            transition: background 0.3s;
        }

        .nav-bar a:hover {
            background: #00BFFF; /* Light blue for hover effect */
            color: #333; /* Dark text for contrast */
        }

        .content {
            padding: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 1rem;
            color: #ddd;
        }

        table,
        th,
        td {
            border: 1px solid #555;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #444;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #555;
        }

        tr:hover {
            background-color: #666;
        }

        .dashboard-footer {
            text-align: center;
            padding: 15px;
            background: #444;
            color: white;
            font-size: 0.9rem;
        }

        .button {
            display: inline-block;
            padding: 12px 30px;
            margin: 10px;
            background: #444;
            color: white;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 1rem;
            text-decoration: none;
            border-radius: 5px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .button:hover {
            background: #00BFFF; /* Light blue for hover effect */
            color: #333; /* Dark text for contrast */
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .button:active {
            transform: translateY(1px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        /* Light yellow accent on headers and buttons */
        .accent-light-yellow {
            color: #FFD700;
        }

        @media (max-width: 768px) {
            .nav-bar {
                flex-direction: column;
                align-items: center;
            }

            table,
            th,
            td {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <p class="welcome-message accent-light-yellow">Welcome, <?php echo htmlspecialchars($admin_username); ?>!</p>
        </div>

        <!-- Navigation Bar -->
        <div class="nav-bar">
            <a href="add_student_form.php" class="button">Add Student</a>
            <a href="view_schedules.php" class="button">View Schedules</a>
            <a href="view_students.php" class="button">View Students</a>
            <a href="view_class.php" class="button">View Classes</a>
            <a href="view_rooms.php" class="button">View Rooms</a>
            <a href="view_subjects.php" class="button">View Subjects</a>
            <a href="login.php" class="button">Logout</a>
        </div>

        <!-- Main Content Area -->
        <div class="content">
            <h2 class="accent-light-yellow">All Students</h2>
            <?php if (count($students) > 0): ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Middle Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Birthday</th>
                        <th>Contact</th>
                        <th>Email</th>
                        <th>Username</th>
                    </tr>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?php echo $student['id']; ?></td>
                            <td><?php echo htmlspecialchars($student['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($student['middle_name']); ?></td>
                            <td><?php echo $student['age']; ?></td>
                            <td><?php echo htmlspecialchars($student['gender']); ?></td>
                            <td><?php echo $student['birthday']; ?></td>
                            <td><?php echo htmlspecialchars($student['contact']); ?></td>
                            <td><?php echo htmlspecialchars($student['email']); ?></td>
                            <td><?php echo htmlspecialchars($student['username']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No students found.</p>
            <?php endif; ?>
        </div>

        <!-- Footer Section -->
        <div class="dashboard-footer">
            &copy; <?php echo date("Y"); ?> Student Management System. All rights reserved.
        </div>
    </div>
</body>

</html>
