<?php
session_start();
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch the logged-in user's ID and username
$user_id = $_SESSION['id'];
$username = $_SESSION['username'];  // Retrieve the username from session
$role = $_SESSION['role'];  // Retrieve the role (e.g., student)

$query = "
    SELECT 
        class.class_id,
        room.room,
        room.roomDescription,
        subjects.subject_code,
        schedules.schedule_from,
        schedules.schedule_to
    FROM class
    INNER JOIN room ON class.room_id = room.room_id
    INNER JOIN subjects ON class.subject_id = subjects.subject_id
    INNER JOIN schedules ON class.schedule_id = schedules.schedule_id
    WHERE class.user_id = ?
";
$stmt = $userDB->prepare($query);
$stmt->bind_param("i", $user_id);  // Bind the user_id parameter
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Classes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Add your styles here */
    </style>
</head>

<body>
    <div class="sidebar">
        <!-- Use first name stored in session -->
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?></h2>
        <a href="view_my_class.php">View My Class</a>

        <!-- Display additional links based on user role (Admin, Student, etc.) -->
        <?php if ($role === 'Student'): ?>
            <a href="view_my_class.php">View My Classes</a>
        <?php endif; ?>

        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="container mt-5">
        <h1>My Classes</h1>

        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Class ID</th>
                        <th>Room</th>
                        <th>Room Description</th>
                        <th>Subject Code</th>
                        <th>Schedule From</th>
                        <th>Schedule To</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($class = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($class['class_id']); ?></td>
                            <td><?php echo htmlspecialchars($class['room']); ?></td>
                            <td><?php echo htmlspecialchars($class['roomDescription']); ?></td>
                            <td><?php echo htmlspecialchars($class['subject_code']); ?></td>
                            <td><?php echo htmlspecialchars($class['schedule_from']); ?></td>
                            <td><?php echo htmlspecialchars($class['schedule_to']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No classes assigned.</p>
        <?php endif; ?>
    </div>
</body>

</html>