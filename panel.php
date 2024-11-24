<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}
?>

<h1>User Panel</h1>
<p>Welcome, <?php echo $_SESSION['username']; ?></p>
<a href="view_schedules.php">View Your Schedules</a>
<a href="logout.php">Logout</a>
