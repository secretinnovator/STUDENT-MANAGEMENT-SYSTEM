<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

$admin_username = $_SESSION['username'];

// Include database connection
include 'db.php';

// Prepare the query to fetch the schedules with correct column names
$stmt = $userDB->prepare("SELECT schedules.schedule_id, schedules.schedule_from, schedules.schedule_to, users.first_name, users.last_name
                          FROM schedules
                          JOIN users ON users.id = schedules.user_id");
$stmt->execute();
$result = $stmt->get_result();

// Fetch all schedules
$schedules = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$userDB->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/admin.png">
    <title>View Schedules</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
    body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #ffffff; /* White background for the body */
}

.navbar {
    background-color: #000000; /* Black background for the navbar */
}

.navbar-brand {
    color: white; /* White text for the brand */
    font-weight: bold;
}

.navbar-nav .nav-link {
    color: white; /* White text for nav links */
}

.navbar-nav .nav-link:hover {
    color: #00BFFF; /* Light blue for hover */
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
    background-color: #e0e0e0; /* Gray background for the table */
    border-radius: 8px;
    border-collapse: separate;
    border-spacing: 0 15px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Box shadow for the table */
}

.table th {
    background-color: #000000; /* Black header background */
    color: white; /* White text for the header */
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2); /* Box shadow for thead */
}

.table td {
    background-color: #000000; /* Black background for table data */
    color: white; /* White text for table data */
    text-align: center;
}

.table td a, .table td button {
    font-weight: bold;
}

.btn {
    background-color: #444444; /* Dark gray button */
    color: white;
    font-weight: bold;
    border-radius: 4px;
    padding: 8px 12px;
    transition: background-color 0.3s, transform 0.3s;
}

.btn:hover {
    background-color: #00BFFF; /* Light blue for hover */
    transform: translateY(-2px);
}

.btn:active {
    background-color: #00BFFF; /* Light blue active state */
    transform: translateY(1px);
}

footer {
    background-color: #000000; /* Black footer background */
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
                        <a class="nav-link" href="add_schedule_form.php">Add Schedule</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Welcome Message -->
    <div class="container">
        <p class="welcome-message">Welcome, <?php echo htmlspecialchars($admin_username); ?>!</p>
    </div>

    <!-- Schedules Table -->
    <div class="container mb-5">
        <div class="table-responsive">
            <h3 class="text-center text-dark">All Schedules</h3>
            <?php if (count($schedules) > 0): ?>
                <table class="table table-bordered table-striped table-hover p-5">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Schedule From</th>
                            <th>Schedule To</th>
                            <th>Student Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td><?php echo $schedule['schedule_id']; ?></td>
                                <td><?php echo $schedule['schedule_from']; ?></td>
                                <td><?php echo $schedule['schedule_to']; ?></td>
                                <td><?php echo htmlspecialchars($schedule['first_name']) . ' ' . htmlspecialchars($schedule['last_name']); ?></td>
                                <td>
                                    <a href="update_schedule.php?id=<?php echo $schedule['schedule_id']; ?>" class="btn btn-sm btn-primary">Update</a>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="<?php echo $schedule['schedule_id']; ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center text-dark">No schedules found.</p>
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
                    const scheduleId = button.getAttribute('data-id');

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
                            window.location.href = `delete_schedule.php?id=${scheduleId}`;
                        }
                    });
                });
            });
        });
    </script>

</body>
</html>
