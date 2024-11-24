<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db.php';

// Check if the ID is set and valid
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $student_id = $_GET['id'];

    // Prepare and execute delete statement
    $stmt = $userDB->prepare("DELETE FROM Users WHERE id = ?");  // Adjusted table name here
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        // Redirect back to the view students page with success message
        header("Location: view_students.php?message=Student deleted successfully.");
    } else {
        // Redirect back with an error message
        header("Location: view_students.php?error=Failed to delete student.");
    }

    $stmt->close();
} else {
    // Redirect back if ID is invalid
    header("Location: view_students.php?error=Invalid student ID.");
}

$userDB->close();
