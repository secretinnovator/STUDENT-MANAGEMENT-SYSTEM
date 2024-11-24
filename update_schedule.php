<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $schedule_id = $_POST['schedule_id'];
    $schedule_from = $_POST['schedule_from'];
    $schedule_to = $_POST['schedule_to'];
    $user_id = $_POST['user_id']; // Added user_id to update the student linked to the schedule

    // Update the schedule
    $stmt = $userDB->prepare("UPDATE schedules SET schedule_from = ?, schedule_to = ?, user_id = ? WHERE schedule_id = ?");
    $stmt->bind_param("ssii", $schedule_from, $schedule_to, $user_id, $schedule_id);

    if ($stmt->execute()) {
        echo "Schedule updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $userDB->close();
    exit();
} else {
    // Retrieve the schedule for the given ID
    $schedule_id = $_GET['id'];
    $stmt = $userDB->prepare("SELECT schedule_id, schedule_from, schedule_to, user_id FROM schedules WHERE schedule_id = ?");
    $stmt->bind_param("i", $schedule_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $schedule = $result->fetch_assoc();
    } else {
        echo "No schedule found for the given ID.";
        exit();
    }

    // Fetch all students for the dropdown
    $students_stmt = $userDB->prepare("SELECT id, first_name, last_name FROM users");
    $students_stmt->execute();
    $students_result = $students_stmt->get_result();
    $students = $students_result->fetch_all(MYSQLI_ASSOC);

    $students_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
        }

        .table-responsive {
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
        }

        .table th {
            background: #6a11cb;
            color: white;
        }

        .navbar {
            background-color: #6a11cb;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .welcome-message {
            font-size: 1.2rem;
        }

        footer {
            background: #6a11cb;
            color: white;
            text-align: center;
            padding: 10px 0;
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
                        <a class="nav-link" href="add_schedule_form.php">Add Schedule</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_students.php">View Students</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="text-center">Update Schedule</h2>
        <form action="update_schedule.php" method="POST" class="mt-4">
            <input type="hidden" name="schedule_id" value="<?php echo htmlspecialchars($schedule['schedule_id']); ?>">

            <div class="mb-3">
                <label for="schedule_from" class="form-label">Schedule From:</label>
                <input type="time" name="schedule_from" class="form-control" value="<?php echo htmlspecialchars($schedule['schedule_from']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="schedule_to" class="form-label">Schedule To:</label>
                <input type="time" name="schedule_to" class="form-control" value="<?php echo htmlspecialchars($schedule['schedule_to']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="user_id" class="form-label">Student:</label>
                <select name="user_id" class="form-select" required>
                    <?php foreach ($students as $student): ?>
                        <option value="<?php echo $student['id']; ?>"
                            <?php echo $student['id'] == $schedule['user_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Schedule</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>