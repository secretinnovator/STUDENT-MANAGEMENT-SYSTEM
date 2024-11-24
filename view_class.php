<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db.php';

// Query to fetch class data
$query = "
    SELECT 
        class.class_id,
        room.room AS classroom,
        room.roomDescription AS room_description,
        users.first_name,
        users.last_name,
        schedules.schedule_from,
        schedules.schedule_to,
        subjects.subject_code
    FROM 
        class
    LEFT JOIN users ON class.user_id = users.id
    LEFT JOIN room ON class.room_id = room.room_id
    LEFT JOIN schedules ON class.schedule_id = schedules.schedule_id
    LEFT JOIN subjects ON class.subject_id = subjects.subject_id
    ORDER BY class.class_id;
";

$result = $userDB->query($query);

if ($result === false) {
    die("Error fetching class data: " . $userDB->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Classes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
     body {
            background-color: white; /* White background for the body */
            color: #333; /* Dark text for the body */
        }

        .ClassList {
            margin-top: 50px;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            color: #333;
        }

        .navbar {
            background-color: black; /* Dark navbar */
        }

        .navbar-brand {
            font-weight: bold;
        }

        footer {
            background: black; /* Dark footer */
            color: white;
            text-align: center;
            padding: 10px 0;
        }

        

        .table th {
            background-color: #333; /* Dark background for thead */
            color: white; /* White text for header */
            text-align: center;
        }

        .table td {
            text-align: center;
        }

        .table-responsive {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark justify-content-center">
        <div class="container">
            <a class="navbar-brand" href="admin_panel.php">Admin Panel</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="add_class.php">Add Class</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Class Table -->
    <div class="container ClassList">
        <h2 class="text-center">Classes List</h2>
        <table class="table table-bordered table-striped p-5">
            <thead>
                <tr>
                    <th>Class ID</th>
                    <th>Room</th>
                    <th>Room Description</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Schedule From</th>
                    <th>Schedule To</th>
                    <th>Subject</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['class_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['classroom']); ?></td>
                            <td><?php echo htmlspecialchars($row['room_description']); ?></td>
                            <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['schedule_from']); ?></td>
                            <td><?php echo htmlspecialchars($row['schedule_to']); ?></td>
                            <td><?php echo htmlspecialchars($row['subject_code']); ?></td>
                            <td>
                                <a href="update_class.php?id=<?php echo $row['class_id']; ?>" class="btn btn-sm btn-primary">Update</a>
                                <a href="delete_class.php?id=<?php echo $row['class_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this class?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">No classes found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Student Management System</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
// Close database connection
$userDB->close();
?>