<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit();
}

// Verify the 'id' is passed in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the current data for the student
    $stmt = $userDB->prepare("SELECT * FROM Users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();  // Fetch student data
    } else {
        echo "No student found with the ID: " . $id;
        exit();
    }
    $stmt->close();
} else {
    echo "ID not set in URL.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $middle_name = trim($_POST['middle_name']);
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];

    // Server-side validation
    $errors = [];

    // Check if names only contain letters
    if (!preg_match("/^[a-zA-Z\s]+$/", $first_name)) {
        $errors[] = "First Name must only contain letters.";
    }
    if (!preg_match("/^[a-zA-Z\s]+$/", $last_name)) {
        $errors[] = "Last Name must only contain letters.";
    }
    if (!empty($middle_name) && !preg_match("/^[a-zA-Z\s]+$/", $middle_name)) {
        $errors[] = "Middle Name must only contain letters.";
    }

    // Check if names are unique
    if ($first_name === $last_name || $first_name === $middle_name || $last_name === $middle_name) {
        $errors[] = "First Name, Last Name, and Middle Name must all be unique.";
    }

    // If errors exist, stop the process
    if (!empty($errors)) {
        echo '<script>
                alert("' . implode("\\n", $errors) . '");
                window.history.back();
              </script>';
        exit();
    }

    // Update the student details using the stored procedure
    $stmt = $userDB->prepare("CALL UpdateStudent(?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssissss", $id, $first_name, $last_name, $middle_name, $age, $gender, $birthday, $contact, $email);

    if ($stmt->execute()) {
        // Redirect after successful update
        echo '<script>
                alert("Student information has been updated successfully.");
                window.location.href = "view_students.php";
              </script>';
        exit();
    } else {
        echo '<script>
                alert("Error updating student data.");
                window.history.back();
              </script>';
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
    <title>Update Student</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Student Management System</a>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4>Update Student Information</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($student)): ?>
                            <form id="updateForm" action="update_student.php?id=<?php echo $student['id']; ?>" method="POST">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($student['id']); ?>">

                                <div class="mb-3">
    <label for="first_name" class="form-label">First Name</label>
    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
</div>

<div class="mb-3">
    <label for="last_name" class="form-label">Last Name</label>
    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
</div>

<div class="mb-3">
    <label for="middle_name" class="form-label">Middle Name</label>
    <input type="text" class="form-control" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($student['middle_name']); ?>">
</div>

                                <div class="mb-3">
                                    <label for="age" class="form-label">Age</label>
                                    <input type="number" class="form-control" id="age" name="age" value="<?php echo htmlspecialchars($student['age']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="Male" <?php if ($student['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                                        <option value="Female" <?php if ($student['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="birthday" class="form-label">Birthday</label>
                                    <input type="date" class="form-control" id="birthday" name="birthday" value="<?php echo htmlspecialchars($student['birthday']); ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="contact" class="form-label">Contact</label>
                                    <input type="text" class="form-control" id="contact" name="contact" value="<?php echo htmlspecialchars($student['contact']); ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                                </div>

                                <button type="submit" class="btn btn-primary w-100" id="updateButton">Update Student</button>
                                <a href="view_students.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
                            </form>
                        <?php else: ?>
                            <p class="text-danger">No student found to update.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-primary text-white text-center py-3 mt-5">
        <p>&copy; <?php echo date('Y'); ?> Student Management System</p>
    </footer>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.min.js"></script>

    <script>



const form = document.getElementById("updateForm");
const firstName = document.getElementById("first_name");
const lastName = document.getElementById("last_name");
const middleName = document.getElementById("middle_name");

form.addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent the default submission

    let errors = [];

    // Validation: Names must only contain letters
    const nameFields = [firstName, lastName, middleName];
    const nameRegex = /^[a-zA-Z\s]+$/;

    nameFields.forEach(field => {
        if (field.value.trim() !== "" && !nameRegex.test(field.value.trim())) {
            errors.push(`${field.previousElementSibling.textContent} must only contain letters.`);
        }
    });

    // Validation: Names must be unique
    const nameValues = [firstName.value.trim(), lastName.value.trim(), middleName.value.trim()];
    const uniqueNames = new Set(nameValues.filter(name => name !== "")); // Ignore empty fields

    if (uniqueNames.size < nameValues.filter(name => name !== "").length) {
        errors.push("First Name, Last Name, and Middle Name must all be unique.");
    }

    // Handle validation errors
    if (errors.length > 0) {
        Swal.fire({
            title: "Error!",
            text: errors.join("\n"),
            icon: "error",
            confirmButtonText: "OK"
        });
        return; // Stop further execution
    }

    // Show SweetAlert confirmation
    Swal.fire({
        title: 'Updating...',
        text: 'Please wait while we process the update.',
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false,
        timer: 2000 // Simulate a delay for the update process
    }).then(() => {
        // After the delay, show a success message
        Swal.fire({
            title: 'Success!',
            text: 'The student information has been updated successfully.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            // Proceed to submit the form
            form.submit();
        });
    });
});

    </script>
</body>
</html>
