<?php
session_start();
include 'db.php';

$login_error = '';

// Define constant admin credentials for login
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'adminperalta');

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize user input
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Check if the submitted credentials match the hardcoded admin credentials
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        // Set session variables for the admin
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'Admin';

        // Redirect to the admin panel
        header("Location: admin_panel.php");
        exit();
    }

    // Prepare statement to check credentials from the database
    $stmt = $userDB->prepare("SELECT id, role, password FROM Users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify password for database users
        if (password_verify($password, $row['password'])) {
            // Set session variables for regular users
            $_SESSION['id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['username'] = $username;

            // Redirect based on role
            if ($_SESSION['role'] === 'Admin') {
                header("Location: admin_panel.php");
            } else {
                header("Location: user_panel.php");
            }
            exit();
        } else {
            $login_error = "Account you entered does not exist.";
        }
    } else {
        $login_error = "Account you entered does not exist.";
    }

    $stmt->close();
}

$userDB->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/login.png">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            font-family: 'Arial', sans-serif;
            background-color: #f9fafb;
            margin: 0;
        }

        .container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .login-card h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.6rem;
            color: #333;
        }

        .form-label {
            color: #666;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px;
            font-size: 1rem;
        }

        .btn-primary {
            background-color: #5c6bc0;
            border-color: #5c6bc0;
            font-weight: bold;
            padding: 12px;
            font-size: 1rem;
            border-radius: 8px;
        }

        .btn-primary:hover {
            background-color: #3f4e9b;
            border-color: #3f4e9b;
        }

        .error-message {
            color: #e74c3c;
            text-align: center;
            font-weight: bold;
            margin-top: 10px;
        }

        .footer {
            text-align: center;
            padding: 1rem;
            font-size: 0.9rem;
            color: #7f8c8d;
            background-color: #f4f6f9;
            position: absolute;
            bottom: 0;
            width: 100%;
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #888;
            font-size: 1.5rem;
        }

        @media (max-width: 767px) {
            .login-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="login-card">
            <h2>Login</h2>

            <?php if (!empty($login_error)): ?>
                <p class="error-message" id="error-message"><?php echo $login_error; ?></p>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                
                <div class="mb-3 password-wrapper">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <span class="toggle-password" onclick="togglePassword()">🔒</span> <!-- Emoji inside password field -->
                </div>

                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>

    <div class="footer">
        &copy; <?php echo date("Y"); ?> Student Management System. All rights reserved.
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = '🔓'; <!-- Unlock emoji -->
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = '🔒'; 
            }
        }

        // Hide the error message after 1.5 seconds
        window.addEventListener('load', () => {
            const errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                setTimeout(() => {
                    errorMessage.style.display = 'none';
                }, 1500);
            }
        });
    </script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
