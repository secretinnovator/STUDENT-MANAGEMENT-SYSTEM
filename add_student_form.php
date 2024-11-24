<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

$admin_username = $_SESSION['username'];

// Include database connection
include 'db.php';

$success_message = ''; // Initialize an empty success message
$error_message = '';   // Initialize an empty error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $middle_name = $_POST['middle_name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'User'; // Set role to Student

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Use the user database connection for students
        $stmt = $userDB->prepare("CALL RegisterUser(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "sssisssssss",
            $first_name,
            $last_name,
            $middle_name,
            $age,
            $gender,
            $birthday,
            $contact,
            $email,
            $username,
            $hashed_password,
            $role
        );

        if ($stmt->execute()) {
            $success_message = "Student account created successfully!";
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 1500); // Redirect after 1.5 seconds
                  </script>";
        } else {
            $error_message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Close the database connections
$userDB->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/register.png">
    <title>Create a Student Account</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Custom Styles -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f0f0f0, #e0e0e0); /* Subtle gradient */
        }

        .navbar {
            background-color: #5a5a5a; /* Dark gray navbar */
        }

        .navbar-brand {
            color: white;
            font-weight: bold;
        }

        .navbar-nav .nav-link {
            color: white;
        }

        .navbar-nav .nav-link:hover {
            color: #00BFFF; /* Light blue on hover */
        }

        .register-card {
            width: 100%;
            max-width: 800px;
            padding: 2rem;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .register-card h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: #333;
        }

        .form-label {
            font-weight: bold;
            color: #555;
        }

        .form-control,
        .form-select {
            padding: 10px;
            margin-bottom: 1rem;
            border-radius: 4px;
            background: #f9f9f9;
            color: #333;
            border: 1px solid #ccc;
        }

        .form-control::placeholder,
        .form-select::placeholder {
            color: #bbb;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #888;
            background: #fff;
        }

        .success-message {
            color: #00FF7F;
            font-weight: bold;
            text-align: center;
            margin-bottom: 1rem;
        }

        .error-message {
            color: #FF6347;
            font-weight: bold;
            text-align: center;
            margin-bottom: 1rem;
        }

        .btn {
            background-color: #444;
            color: white;
            font-weight: bold;
            border-radius: 4px;
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn:hover {
            background-color: #00BFFF;
            transform: translateY(-2px);
        }

        .btn:active {
            background-color: #00BFFF;
            transform: translateY(1px);
        }

        footer {
            background-color: #444;
            color: white;
            text-align: center;
            padding: 10px 0;
            margin-top: 40px;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="admin_panel.php">Admin Panel</a>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
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

    <!-- Add Student Form -->
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="register-card">
            <h2>Add New Student</h2>

            <?php
            if (!empty($success_message)) {
                echo "<p class='success-message'>$success_message</p>";
            } elseif (!empty($error_message)) {
                echo "<p class='error-message'>$error_message</p>";
            }
            ?>

            <form action="add_student_form.php" method="POST">
                <div class="row">
                    <div class="col-sm-4">
                        <label for="first_name" class="form-label">First Name:</label>
                        <input type="text" id="first_name" name="first_name" class="form-control" required placeholder="Enter First Name">
                    </div>
                    <div class="col-sm-4">
                        <label for="last_name" class="form-label">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" class="form-control" required placeholder="Enter Last Name">
                    </div>
                    <div class="col-sm-4">
                        <label for="middle_name" class="form-label">Middle Name:</label>
                        <input type="text" id="middle_name" name="middle_name" class="form-control" placeholder="Enter Middle Name">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <label for="age" class="form-label">Age:</label>
                        <input type="number" id="age" name="age" class="form-control" required placeholder="Enter Age">
                    </div>
                    <div class="col-sm-4">
                        <label for="gender" class="form-label">Gender:</label>
                        <select id="gender" name="gender" class="form-select" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-sm-4">
                        <label for="birthday" class="form-label">Birthday:</label>
                        <input type="date" id="birthday" name="birthday" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label for="contact" class="form-label">Contact Number:</label>
                        <input type="text" id="contact" name="contact" class="form-control" required placeholder="Enter Contact Number">
                    </div>
                    <div class="col-sm-6">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" required placeholder="Enter Email">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" id="username" name="username" class="form-control" required placeholder="Enter Username">
                    </div>
                    <div class="col-sm-6">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" id="password" name="password" class="form-control" required placeholder="Enter Password">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <label for="confirm_password" class="form-label">Confirm Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required placeholder="Confirm Password">
                    </div>
                </div>

                <button type="submit" class="btn btn-block mt-4 w-100">Add Student</button>
            </form>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Student Management System. All rights reserved.</p>
    </footer>

    <!-- Bootstrap JS and SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
</body>

</html>
