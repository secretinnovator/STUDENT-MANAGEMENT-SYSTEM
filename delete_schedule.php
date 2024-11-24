<?php
session_start();

// Ensure the user is authorized
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Include the database connection
include 'db.php';

// Check if 'id' (schedule_id) is provided in the query string
if (isset($_GET['id'])) {
    $schedule_id = intval($_GET['id']); // Ensure the ID is an integer

    // Verify the database connection
    if ($userDB) {
        // Delete related rows in 'class' table
        $stmtDeleteClass = $userDB->prepare("DELETE FROM class WHERE schedule_id = ?");
        $stmtDeleteClass->bind_param("i", $schedule_id);
        $stmtDeleteClass->execute();
        $stmtDeleteClass->close();

        // Delete the schedule
        $stmtDeleteSchedule = $userDB->prepare("DELETE FROM schedules WHERE schedule_id = ?");
        $stmtDeleteSchedule->bind_param("i", $schedule_id);

        if ($stmtDeleteSchedule->execute()) {
            // Redirect to the schedules page with a success message
            header("Location: view_schedules.php?message=Schedule deleted successfully.");
        } else {
            // Redirect with an error message
            header("Location: view_schedules.php?error=Error deleting schedule: " . $stmtDeleteSchedule->error);
        }

        $stmtDeleteSchedule->close();
    } else {
        die("Database connection error.");
    }
} else {
    // Redirect if no ID is provided
    header("Location: view_schedules.php?error=No schedule ID provided.");
}

$userDB->close();
