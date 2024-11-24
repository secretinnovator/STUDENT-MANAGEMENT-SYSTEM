<?php
session_start();
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
    // Get form data and sanitize
    $schedule_from = $_POST['schedule_from'] ?? '';
    $schedule_to = $_POST['schedule_to'] ?? '';
    $user_id = $_POST['user_id'] ?? '';

    // Validate data (check if fields are empty)
    if (empty($schedule_from) || empty($schedule_to) || empty($user_id)) {
        $add_error = "All fields are required!";
    } else {
        // Prepare the SQL query to insert schedule
        $stmt = $userDB->prepare("INSERT INTO schedules (schedule_from, schedule_to, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $schedule_from, $schedule_to, $user_id);

        if ($stmt->execute()) {
            $success_message = "Schedule added successfully!";
        } else {
            $add_error = "Error adding schedule. Please try again.";
        }

        $stmt->close();
    }
}

// Fetch users for the dropdown
$users_result = $userDB->query("SELECT id, first_name, last_name FROM users");

if ($users_result === false) {
    // Error fetching users, handle the issue
    $add_error = "Error fetching users. Please check the database connection.";
}

$userDB->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/admin.png">
    <title>Add Schedule</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

.addsched {
    margin-top: 50px;
    background: #e0e0e0; /* Gray background for the form */
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Box shadow for form */
}

.form-group {
    margin-bottom: 1.5rem;
}

h2 {
    color: #000000; /* Black text for the header */
}

footer {
    background-color: #000000; /* Black footer background */
    color: white;
    text-align: center;
    padding: 10px 0;
}

.alert {
    border-radius: 4px; /* Rounded corners for alerts */
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
}

.btn-primary {
    background-color: #444444; /* Dark gray button */
    color: white;
    font-weight: bold;
    border-radius: 4px;
    padding: 8px 12px;
    transition: background-color 0.3s, transform 0.3s;
}

.btn-primary:hover {
    background-color: #00BFFF; /* Light blue for hover */
    transform: translateY(-2px);
}

.btn-primary:active {
    background-color: #00BFFF; /* Light blue active state */
    transform: translateY(1px);
}

.form-control {
    background-color: #000000; /* Black input fields */
    color: white; /* White text for input fields */
    border: 1px solid #555; /* Dark border for input fields */
}

.form-control:focus {
    background-color: #333; /* Darker background on focus */
    border-color: #00BFFF; /* Light blue border on focus */
}

    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="view_schedules.php">View Schedules</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Add Schedule Form -->
    <div class="container addsched">
        <h2 class="text-center text-dark">Add New Schedule</h2>

        <!-- Display error or success messages -->
        <?php if (!empty($add_error)): ?>
            <div class="alert alert-danger"><?php echo $add_error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <!-- Schedule Form -->
        <form action="add_schedule_form.php" method="POST">
            <div class="form-group">
                <label for="schedule_from">Schedule Start Time</label>
                <input type="time" id="schedule_from" name="schedule_from" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="schedule_to">Schedule End Time</label>
                <input type="time" id="schedule_to" name="schedule_to" class="form-control" required>
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

            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary">Add Schedule</button>
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