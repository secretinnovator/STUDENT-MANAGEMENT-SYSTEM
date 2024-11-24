<?php
// Include database connection
include 'db.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $room_id = $_POST['room_id']; // Fetch the selected room_id from the dropdown
    $user_id = $_POST['user_id'];
    $subject_id = $_POST['subject_id'];
    $schedule_id = $_POST['schedule_id'];

    // Insert the class details
    $class_query = "INSERT INTO class (room_id, user_id, subject_id, schedule_id) 
                    VALUES (?, ?, ?, ?)";
    $class_stmt = $userDB->prepare($class_query);
    $class_stmt->bind_param("iiii", $room_id, $user_id, $subject_id, $schedule_id);

    if ($class_stmt->execute()) {
        // Redirect to view_class.php upon successful insertion
        header('Location: view_class.php');
        exit; // Ensure no further code is executed
    } else {
        echo "Error adding class: " . $userDB->error;
    }
}

// Fetch data for dropdowns
$rooms_result = $userDB->query("SELECT room_id, room, roomDescription FROM room");
$users_result = $userDB->query("SELECT id, first_name, last_name FROM users WHERE role = 'User'");
$subjects_result = $userDB->query("SELECT subject_id, subject_code FROM subjects");
$schedules_result = $userDB->query("SELECT schedule_id, schedule_from, schedule_to FROM schedules");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Class</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            color: white; /* White text for contrast */
        }

        .container {
            background-color: #1f1f1f; /* Slightly lighter background for the form container */
            padding: 30px;
            border-radius: 10px;
            margin-top: 50px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .form-group label {
            color: #ccc; /* Lighter text for labels */
        }

        .form-control {
            background-color: #333; /* Dark background for inputs */
            color: white;
            border: 1px solid #555; /* Slightly lighter border */
        }

        .form-control:focus {
            background-color: #444; /* Darken on focus */
            border-color: #aaa;
        }

        .btn-primary {
            background-color: #007bff; /* Blue button */
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        footer {
            background-color: #121212;
            color: #888;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
    </style>
</head>

<body>
    <div class="container p-5">
        <h2>Add New Class</h2>

        <form action="add_class.php" method="POST">
            <div class="form-group">
                <label for="room_id">Room</label>
                <select id="room_id" name="room_id" class="form-control" required>
                    <option value="">Select Room</option>
                    <?php
                    while ($row = $rooms_result->fetch_assoc()) {
                        echo "<option value='" . $row['room_id'] . "'>" . htmlspecialchars($row['room']) . " - " . htmlspecialchars($row['roomDescription']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="subject_id">Subject</label>
                <select id="subject_id" name="subject_id" class="form-control" required>
                    <option value="">Select Subject</option>
                    <?php
                    while ($row = $subjects_result->fetch_assoc()) {
                        echo "<option value='" . $row['subject_id'] . "'>" . htmlspecialchars($row['subject_code']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="user_id">User</label>
                <select id="user_id" name="user_id" class="form-control" required>
                    <option value="">Select User</option>
                    <?php
                    while ($row = $users_result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['first_name'] . " " . $row['last_name']) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="schedule_id">Schedule</label>
                <select id="schedule_id" name="schedule_id" class="form-control" required>
                    <option value="">Select Schedule</option>
                    <?php
                    while ($row = $schedules_result->fetch_assoc()) {
                        $schedule = $row['schedule_from'] . ' to ' . $row['schedule_to'];
                        echo "<option value='" . $row['schedule_id'] . "'>" . htmlspecialchars($schedule) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary">Add Class</button>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Student Management System</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
// Close database connection
$userDB->close();
?>
