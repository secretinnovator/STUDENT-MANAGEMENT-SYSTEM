<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db.php';

$add_error = '';
$success_message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $subject_code = $_POST['subject_code'] ?? '';
    $user_id = $_POST['user_id'] ?? '';

    // Validate inputs
    if (empty($subject_code) || empty($user_id)) {
        $add_error = "All fields are required!";
    } else {
        // Check current number of subjects for the user
        $stmt = $userDB->prepare("SELECT COUNT(*) as subject_count FROM subjects WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $subject_count = $row['subject_count'];
        $stmt->close();

        if ($subject_count >= 9) {
            $add_error = "This user already has the maximum number of 9 subjects.";
        } else {
            // Insert new subject
            $stmt = $userDB->prepare("INSERT INTO subjects (subject_code, user_id) VALUES (?, ?)");
            $stmt->bind_param("si", $subject_code, $user_id);

            if ($stmt->execute()) {
                $success_message = "Subject added successfully!";
            } else {
                $add_error = "Error adding subject. Please try again.";
            }

            $stmt->close();
        }
    }
}

// Fetch users for the dropdown
$users_result = $userDB->query("SELECT id, first_name, last_name FROM users");

if ($users_result === false) {
    $add_error = "Error fetching users. Please check the database connection.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Subject</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #ffffff; /* White background for the body */
            color: black; /* Black text for readability */
        }

        .addsub {
            margin-top: 50px;
            background: #f8f8f8; /* Light grey background for the content section */
            padding: 30px;
            border-radius: 8px;
            color: black; /* Black text for content */
        }

        .navbar {
            background-color: #000000; /* Black navbar */
        }

        .navbar-brand, .navbar-nav .nav-link {
            color: white; /* White text for navbar */
        }

        .navbar-nav .nav-link:hover {
            color: #888; /* Lighter grey text on hover */
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

        .alert-danger {
            background-color: #f8d7da; /* Light red background for error */
            color: black; /* Black text for error messages */
        }

        .alert-success {
            background-color: #d4edda; /* Light green background for success */
            color: black; /* Black text for success messages */
        }

        .form-group label {
            font-weight: bold; /* Make labels bold */
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
                        <a class="nav-link" href="view_subjects.php">View Subjects</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Add Subject Form -->
    <div class="container addsub mb-5">
        <h2 class="text-center">Add New Subject</h2>

        <!-- Display error or success messages -->
        <?php if (!empty($add_error)): ?>
            <div class="alert alert-danger"><?php echo $add_error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <!-- Subject Form -->
        <form action="add_subject.php" method="POST">
            <div class="form-group">
                <label for="subject_code">Subject Code</label>
                <input type="text" id="subject_code" name="subject_code" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="user_id">Assign to User</label>
                <select id="user_id" name="user_id" class="form-control" required>
                    <option value="">Select User</option>
                    <?php
                    if ($users_result && $users_result->num_rows > 0) {
                        while ($row = $users_result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>No users found</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group text-center mt-3">
                <button type="submit" class="btn btn-primary">Add Subject</button>
            </div>
        </form>
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
