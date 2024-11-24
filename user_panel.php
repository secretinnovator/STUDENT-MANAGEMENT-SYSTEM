<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection
include 'db.php';

// Fetch user details including first_name
$username = $_SESSION['username'];
$stmt = $userDB->prepare("SELECT role, profile_picture, first_name FROM Users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Query to fetch class information
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
$stmt->bind_param("i", $_SESSION['id']);  // Bind user_id parameter
$stmt->execute();
$classResult = $stmt->get_result();

// Handle profile picture upload
$errorMessages = [];  // Array to store error messages

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['profile_picture'])) {
    // Define the target directory where the image will be stored
    $targetDir = __DIR__ . '/uploads/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);  // Create directory if it doesn't exist
    }

    $fileName = time() . '_' . basename($_FILES["profile_picture"]["name"]);
    $targetFile = $targetDir . $fileName;
    $uploadOk = true;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if the file is an actual image or a fake image
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check === false) {
        $errorMessages[] = "File is not an image.";
        $uploadOk = false;
    }

    // Check if the file already exists in the uploads folder
    if (file_exists($targetFile)) {
        $errorMessages[] = "Sorry, file already exists.";
        $uploadOk = false;
    }

    // Limit file size to 5MB
    if ($_FILES["profile_picture"]["size"] > 5000000) {
        $errorMessages[] = "Sorry, your file is too large.";
        $uploadOk = false;
    }

    // Allow only certain file formats (e.g., JPG, PNG, JPEG, GIF)
    if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
        $errorMessages[] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = false;
    }

    // Check MIME type of the file for extra security
    $mimeType = mime_content_type($_FILES["profile_picture"]["tmp_name"]);
    if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif'])) {
        $errorMessages[] = "Invalid file type. Please upload a valid image.";
        $uploadOk = false;
    }

    // If everything is OK, attempt to upload the file
    if ($uploadOk) {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFile)) {
            // Save the file path to the database
            $relativeFilePath = 'uploads/' . $fileName; // Use relative path for displaying
            $stmt = $userDB->prepare("UPDATE Users SET profile_picture = ? WHERE username = ?");
            $stmt->bind_param("ss", $relativeFilePath, $username);
            if ($stmt->execute()) {
                $successMessage = "Profile picture updated successfully.";
                // Refresh the page to load the updated profile picture
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $errorMessages[] = "Database update failed.";
            }
        } else {
            $errorMessages[] = "Sorry, there was an error uploading your file.";
        }
    }
}

$userDB->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/profile.png">
    <title>User Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            background-color: #343a40;
            color: white;
            padding: 20px;
            height: 100vh;
        }
        .sidebar h2 {
            color: #6c757d;
            font-size: 1.5rem;
            text-align: center;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
            margin: 5px 0;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background-color: #6c757d;
        }
        .sidebar .logout {
            position: absolute;
            bottom: 20px;
            width: auto; /* Remove the width: 100% */
            background-color: #dc3545;
            padding: 8px 12px; /* Adjust padding to make it smaller */
            font-size: 0.9rem; /* Reduce font size */
            text-align: center;
            border-radius: 5px; /* Optional: add rounded corners */
        }
        .main-content {
            padding: 20px;
            flex: 1;
        }
        .card {
            margin-bottom: 1rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #343a40;
            color: white;
            font-size: 1.25rem;
            padding: 1rem;
        }
        .profile-picture img {
            border-radius: 50%;
            width: 120px;
            height: 120px;
            object-fit: cover;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Welcome, <?php echo htmlspecialchars($user['first_name']); ?></h2>
            <a href="user_panel.php" class="active">Dashboard</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="card">
                <div class="card-header">
                    <h2><?php echo htmlspecialchars($username); ?>'s Information</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="profile-picture">
                                <?php if (!empty($user['profile_picture'])): ?>
                                    <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
                                <?php else: ?>
                                    <img src="images/default-profile.png" alt="Default Profile Picture">
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <p><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
                            <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>

                            <!-- Profile Picture Update Form -->
                            <form action="user_panel.php" method="post" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="profile_picture" class="form-label">Update Profile Picture:</label>
                                    <input type="file" name="profile_picture" id="profile_picture" class="form-control" accept="image/*">
                                </div>
                                <button type="submit" class="btn btn-primary">Upload Picture</button>
                            </form>

                            <!-- Display Error and Success Messages -->
                            <?php if (!empty($errorMessages)): ?>
                                <div class="alert alert-danger mt-3">
                                    <?php foreach ($errorMessages as $message): ?>
                                        <p><?php echo htmlspecialchars($message); ?></p>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($successMessage)): ?>
                                <div class="alert alert-success mt-3">
                                    <p><?php echo htmlspecialchars($successMessage); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Classes Table -->
            <div class="card">
                <div class="card-header">
                    <h2>Your Classes</h2>
                </div>
                <div class="card-body">
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
                            <?php while ($row = $classResult->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['class_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['room']); ?></td>
                                    <td><?php echo htmlspecialchars($row['roomDescription']); ?></td>
                                    <td><?php echo htmlspecialchars($row['subject_code']); ?></td>
                                    <td><?php echo htmlspecialchars($row['schedule_from']); ?></td>
                                    <td><?php echo htmlspecialchars($row['schedule_to']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>

</html>
