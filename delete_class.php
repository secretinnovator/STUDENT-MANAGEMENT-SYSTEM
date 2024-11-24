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

// Delete the class
$delete_query = "DELETE FROM class WHERE class_id = ?";
$stmt = $userDB->prepare($delete_query);
$stmt->bind_param("i", $class_id);

if ($stmt->execute()) {
    header("Location: view_class.php");
    exit();
} else {
    echo "Error deleting class: " . $userDB->error;
}

$stmt->close();
$userDB->close();
