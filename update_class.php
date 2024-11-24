<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db.php';

// Check if an ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: view_classes.php");
    exit();
}

$class_id = $_GET['id'];

// Fetch current class details with roomDescription
$query = "
    SELECT 
        class.room_id, 
        class.user_id, 
        class.subject_id, 
        class.schedule_id, 
        room.roomDescription 
    FROM 
        class 
    LEFT JOIN room ON class.room_id = room.room_id
    WHERE 
        class.class_id = ?";
$stmt = $userDB->prepare($query);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$result = $stmt->get_result();
$class = $result->fetch_assoc();

if (!$class) {
    echo "Class not found.";
    exit();
}

// Fetch dropdown data
$rooms_result = $userDB->query("SELECT room_id, room FROM room");
$descriptions_result = $userDB->query("SELECT DISTINCT roomDescription FROM room");
$users_result = $userDB->query("SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM users WHERE role = 'User'");
$subjects_result = $userDB->query("SELECT subject_id, subject_code FROM subjects");
$schedules_result = $userDB->query("SELECT schedule_id, schedule_from, schedule_to FROM schedules");

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $room_id = $_POST['room_id'];
    $user_id = $_POST['user_id'];
    $subject_id = $_POST['subject_id'];
    $schedule_id = $_POST['schedule_id'];
    $room_description = $_POST['room_description']; // Capture room description from form

    $update_query = "
        UPDATE class 
        SET room_id = ?, user_id = ?, subject_id = ?, schedule_id = ? 
        WHERE class_id = ?";
    $update_stmt = $userDB->prepare($update_query);
    $update_stmt->bind_param("iiiii", $room_id, $user_id, $subject_id, $schedule_id, $class_id);

    if ($update_stmt->execute()) {
        header("Location: view_class.php");
        exit();
    } else {
        echo "Error updating class: " . $userDB->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Class</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Update Class</h2>
        <form method="POST">
            <div class="form-group">
                <label for="room_id">Room</label>
                <select id="room_id" name="room_id" class="form-control" required>
                    <option value="">Select Room</option>
                    <?php while ($room = $rooms_result->fetch_assoc()): ?>
                        <option value="<?php echo $room['room_id']; ?>"
                            <?php echo $room['room_id'] == $class['room_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($room['room']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Dropdown for Room Description -->
            <div class="form-group">
                <label for="room_description">Room Description</label>
                <select id="room_description" name="room_description" class="form-control" required>
                    <option value="">Select Room Description</option>
                    <?php while ($description = $descriptions_result->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($description['roomDescription']); ?>"
                            <?php echo isset($class['roomDescription']) && $description['roomDescription'] == $class['roomDescription'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($description['roomDescription']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="user_id">User</label>
                <select id="user_id" name="user_id" class="form-control" required>
                    <option value="">Select User</option>
                    <?php while ($user = $users_result->fetch_assoc()): ?>
                        <option value="<?php echo $user['id']; ?>"
                            <?php echo $user['id'] == $class['user_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($user['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="subject_id">Subject</label>
                <select id="subject_id" name="subject_id" class="form-control" required>
                    <option value="">Select Subject</option>
                    <?php while ($subject = $subjects_result->fetch_assoc()): ?>
                        <option value="<?php echo $subject['subject_id']; ?>"
                            <?php echo $subject['subject_id'] == $class['subject_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($subject['subject_code']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="schedule_id">Schedule</label>
                <select id="schedule_id" name="schedule_id" class="form-control" required>
                    <option value="">Select Schedule</option>
                    <?php while ($schedule = $schedules_result->fetch_assoc()): ?>
                        <option value="<?php echo $schedule['schedule_id']; ?>"
                            <?php echo $schedule['schedule_id'] == $class['schedule_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($schedule['schedule_from'] . " - " . $schedule['schedule_to']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Update Class</button>
        </form>
    </div>
</body>

</html>

<?php $userDB->close(); ?>