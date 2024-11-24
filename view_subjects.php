<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db.php';

// Fetch all subjects with user information
$query = "
    SELECT 
        subjects.subject_id,
        subjects.subject_code,
        users.first_name,
        users.last_name
    FROM 
        subjects
    JOIN 
        users ON subjects.user_id = users.id
";
$result = $userDB->query($query);

if ($result === false) {
    die("Error fetching subjects: " . $userDB->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Subjects</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #ffffff; /* White background for the body */
            color: black; /* Black text for readability */
        }

        .subjects {
            margin-top: 50px;
            background: #f8f8f8; /* Light grey background for the content section */
            padding: 30px;
            border-radius: 8px;
            color: #333; /* Dark text for contrast */
        }

        .navbar {
            background-color: #000000; /* Black navbar */
        }

        .navbar-brand, .navbar-nav .nav-link {
            color: white; /* White text for navbar */
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

        .table td {
            background: #ffffff; /* White background for table rows */
            color: black; /* Black text for table cells */
        }

        .btn-primary {
            background-color: #000000; /* Black button background */
            border-color: #000000; /* Black button border */
        }

        .btn-primary:hover {
            background-color: #333; /* Darker black on hover */
            border-color: #333; /* Darker black border on hover */
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
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Subjects Table -->
    <div class="container subjects">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Subjects List</h2>
            <a href="add_subject.php" class="btn btn-primary">Add New Subject</a>
        </div>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subject Code</th>
                    <th>Assigned User</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['subject_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['subject_code']); ?></td>
                            <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="text-center">No subjects found.</td>
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
