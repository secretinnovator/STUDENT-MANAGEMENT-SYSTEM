<?php
session_start();

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'db.php';

$add_error = '';
$success_message = '';

// Initialize variables to prevent undefined variable warnings
$room = $roomDescription = $user_id = '';  // Default empty strings for form fields

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and assign defaults if not set
    $room = $_POST['room'] ?? ''; // Default to empty string if not set
    $roomDescription = $_POST['roomDescription'] ?? ''; // Default to empty string if not set
    $user_id = $_POST['user_id'] ?? ''; // Default to empty string if not set

    // Validate inputs
    if (empty($room) || empty($user_id)) {
        $add_error = "Room name and User are required!";
    } else {
        // Prepare SQL query to insert room
        $stmt = $userDB->prepare("INSERT INTO room (user_id, room, roomDescription) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $room, $roomDescription);

        if ($stmt->execute()) {
            $success_message = "Room added successfully!";
        } else {
            $add_error = "Error adding room. Please try again.";
        }

        $stmt->close();
    }
}

// Fetch users for the dropdown
$users_result = $userDB->query("SELECT id, first_name, last_name FROM users");

if ($users_result === false) {
    $add_error = "Error fetching users. Please check the database connection.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #ffffff; /* White background for the body */
            color: black; /* Black text for contrast */
        }

        .addroom {
            margin-top: 80px;
            background: #f8f8f8; /* Light grey background for the form container */
            padding: 30px;
            border-radius: 8px;
            color: #333; /* Dark text for readability */
        }

        .navbar {
            background-color: #000000; /* Black navbar */
        }

        .navbar-brand, .navbar-nav .nav-link {
            color: white; /* White text for navbar */
        }

        .navbar-nav .nav-link:hover {
            color: #888; /* Lighter text color on hover */
        }

        footer {
            background: #000000; /* Black footer */
            color: white;
            text-align: center;
            padding: 10px 0;
        }

        .form-group label {
            font-weight: bold;
        }

        .table th {
            background: #000000; /* Black table headers */
            color: white; /* White text for table headers */
        }

        .form-control {
            background-color: #fff; /* White background for form fields */
            color: black; /* Black text for input fields */
            border: 1px solid #ccc; /* Light grey border for input fields */
        }

        .btn-primary {
            background-color: #000000; /* Black button background */
            border-color: #000000; /* Black button border */
        }

        .btn-primary:hover {
            background-color: #333; /* Darker black on hover */
            border-color: #333; /* Darker black border on hover */
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="admin_panel.php">Admin Panel</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="view_rooms.php">View Rooms</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Add Room Form -->
    <div class="container addroom mb-5">
        <h2 class="text-center">Add New Room</h2>

        <!-- Display error or success messages -->
        <?php if (!empty($add_error)): ?>
            <div class="alert alert-danger"><?php echo $add_error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <!-- Room Form -->
        <form action="add_room.php" method="POST">
            <div class="form-group">
                <label for="room">Room Name/Number</label>
                <input type="text" id="room" name="room" class="form-control" required value="<?php echo htmlspecialchars($room); ?>">
            </div>

            <div class="form-group">
                <label for="roomDescription">Room Description</label>
                <textarea id="roomDescription" name="roomDescription" class="form-control"><?php echo htmlspecialchars($roomDescription); ?></textarea>
            </div>

            <div class="form-group">
                <label for="user_id">Assign to User</label>
                <select id="user_id" name="user_id" class="form-control" required>
                    <option value="">Select User</option>
                    <?php
                    if ($users_result && $users_result->num_rows > 0) {
                        while ($row = $users_result->fetch_assoc()) {
                            echo "<option value='" . $row['id'] . "' " . ($user_id == $row['id'] ? "selected" : "") . ">" . htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>No users found</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group text-center">
                <button type="submit" class="btn btn-primary">Add Room</button>
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

<?php
// Close database connection
$userDB->close();
?>
