<?php 
include 'db.php';
session_start();

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
    $role = $_POST['role']; // Retrieve selected role

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        if ($role === 'Admin') {
            // Use the admin database connection for admins
            $stmt = $adminDB->prepare("CALL RegisterAdmin(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "ssssisssss",
                $first_name,
                $last_name,
                $middle_name,
                $age,
                $gender,
                $birthday,
                $contact,
                $email,
                $username,
                $hashed_password
            );
        } else {
            // Use the user database connection for regular users
            $stmt = $userDB->prepare("CALL RegisterUser(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "ssssissssss",
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
                $role // Role can still be 'User' by default
            );
        }

        if ($stmt->execute()) {
            $success_message = "Account created successfully!";
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
$adminDB->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/register.png">
    <title>Create an Account</title>
    <style>
        /* Basic page styling */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc);
        }
        .register-card {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.8);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        .register-card h2 {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .register-card form {
            display: flex;
            flex-direction: column;
        }
        .register-card input[type="text"],
        .register-card input[type="password"],
        .register-card input[type="date"],
        .register-card input[type="tel"],
        .register-card input[type="number"],
        .register-card input[type="submit"],
        .register-card select {
            padding: 10px;
            margin-bottom: 1rem;
            border: none;
            border-radius: 4px;
        }
        .register-card input[type="submit"] {
            background-color: #6a11cb;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        .register-card p {
            text-align: center;
        }
        .register-card a {
            color: #6a11cb;
            text-decoration: none;
        }
        .register-card a:hover {
            text-decoration: underline;
        }
        .success-message {
            color: green;
            font-weight: bold;
            text-align: center;
            margin-bottom: 1rem;
        }
        .error-message {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <h2>Signup for Free</h2>

        <?php if ($success_message): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php elseif ($error_message): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <label for="first_name">First Name:</label>
            <input type="text" id="first_name" name="first_name" required>
            
            <label for="last_name">Last Name:</label>
            <input type="text" id="last_name" name="last_name" required>

            <label for="middle_name">Middle Name:</label>
            <input type="text" id="middle_name" name="middle_name">

            <label for="age">Age:</label>
            <input type="number" id="age" name="age" required>

            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

            <label for="birthday">Birthday:</label>
            <input type="date" id="birthday" name="birthday" required>

            <label for="contact">Contact:</label>
            <input type="tel" id="contact" name="contact" required>

            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <label for="role">Role:</label>
<select id="role" name="role" required>
    <option value="User">User</option>
    <option value="Admin">Admin</option>
</select>


            <input type="submit" value="Register">
        </form>

        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</body>
</html>
