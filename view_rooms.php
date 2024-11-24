<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db.php';

// Fetch all rooms with user information
$query = "
    SELECT 
        room.room_id,
        room.room,
        room.roomDescription,
        users.first_name,
        users.last_name
    FROM 
        room
    JOIN 
        users ON room.user_id = users.id
";
$result = $userDB->query($query);

if ($result === false) {
    die("Error fetching rooms: " . $userDB->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Rooms</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #ffffff; /* White background for the body */
            color: black; /* Black text for contrast */
            padding-top: 80px; /* Added padding for the navbar */
        }

        .rooms {
            margin-top: 50px;
            background: #f8f8f8; /* Light grey background for the rooms container */
            padding: 30px;
            border-radius: 8px;
            color: #333; /* Dark text for readability */
        }

        .navbar {
            background-color: #000000; /* Black navbar */
        }

        .navbar-brand {
            font-weight: bold;
            color: white; /* White text for navbar brand */
        }

        .navbar-nav .nav-link {
            color: white; /* White text for navbar links */
        }

        .navbar-nav .nav-link:hover {
            color: #888; /* Lighter text color on hover */
        }

        footer {
            background: #000000; /* Black footer */
            color: white;
            text-align: center;
            padding: 10px 0;
        }

        .table th {
            background: #000000; /* Black table headers */
            color: white; /* White text for table headers */
        }

        .table-bordered, .table-striped {
            border-color: #ddd; /* Light grey borders for the table */
        }

        .table tbody tr:nth-child(odd) {
            background-color: #f2f2f2; /* Alternating row colors */
        }

        .table tbody tr:hover {
            background-color: #e0e0e0; /* Hover effect for table rows */
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="admin_panel.php">Admin Panel</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="add_room.php">Add Room</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Rooms Table -->
    <div class="container rooms mb-5">
        <h2 class="text-center">Rooms List</h2>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Room</th>
                    <th>Description</th>
                    <th>Assigned User</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['room_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['room']); ?></td>
                            <td><?php echo htmlspecialchars($row['roomDescription']); ?></td>
                            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">No rooms found.</td>
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
