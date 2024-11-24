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
$adminDB->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/admin.png">
    <title>View Students</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        body {
            background-color: #ffffff; /* White background for the body */
            color: black; /* Black text for general content */
        }

        .navbar {
            background-color: #000000; /* Black navbar background */
        }

        .navbar-brand {
            color: white;
            font-weight: bold;
        }

        .navbar-nav .nav-link {
            color: white; /* White text for navbar links */
        }

        .navbar-nav .nav-link:hover {
            color: #00BFFF; /* Light blue on hover */
        }

        .welcome-message {
            font-size: 1.3rem;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        .table-responsive {
            margin-top: 20px;
        }

        .table {
            background-color: #e0e0e0; /* Gray background for table */
            border-radius: 8px;
            border-collapse: separate;
            border-spacing: 0 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        .table th {
            background-color: #000000; /* Black header */
            color: white;
            text-align: center;
        }

        .table td {
            background-color: #f9f9f9; /* Light gray for table rows */
            text-align: center;
        }

        .table td a, .table td button {
            font-weight: bold;
        }

        .btn {
            background-color: #444; /* Dark gray button */
            color: white;
            font-weight: bold;
            border-radius: 4px;
            padding: 8px 12px;
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn:hover {
            background-color: #00BFFF; /* Light blue on hover */
            transform: translateY(-2px);
        }

        .btn:active {
            background-color: #00BFFF; /* Light blue active state */
            transform: translateY(1px);
        }

        footer {
            background-color: #000000; /* Black footer */
            color: white;
            text-align: center;
            padding: 10px 0;
            margin-top: 40px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="admin_panel.php">Admin Panel</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="add_student_form.php">Add Student</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Welcome Message -->
    <div class="container my-4">
        <p class="welcome-message">Welcome, <?php echo htmlspecialchars($admin_username); ?>!</p>
    </div>

    <!-- Students Table -->
    <div class="container mb-5">
        <div class="table-responsive">
            <h3 class="text-center text-dark">All Students</h3>
            <?php if (count($students) > 0): ?>
                <table class="table table-bordered table-striped table-hover">
                    <thead>
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
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
                                <td>
                                    <a href="update_student.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-primary">Update</a>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $student['id']; ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center text-dark">No students found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Student Management System</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const deleteButtons = document.querySelectorAll('.delete-btn');
            deleteButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const studentId = button.getAttribute('data-id');

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `delete_student.php?id=${studentId}`;
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>
