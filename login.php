<?php
// Start session
session_start();
require_once 'includes/db.php';

// Store error message
$error = "";

// Check if login form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Read and trim user input
    $loginID = trim($_POST['login_id'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validate empty fields
    if (empty($loginID) || empty($password)) {
        $error = "Please enter your username/student ID/email and password.";
    } else {
        // Search admin by username or email
        $sql = "SELECT admin_id, username, email, password FROM admin WHERE username = ? OR email = ? LIMIT 1";

        // Prepare SQL statement
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Prepare Failed: " . $conn->error);
        }

        // Bind login ID to both placeholders
        $stmt->bind_param("ss", $loginID, $loginID);

        // Execute query
        $stmt->execute();

        // Get query result
        $result = $stmt->get_result();

        // Check if admin exists
        if ($result->num_rows == 1) {
            // Fetch the admin record
            $admin = $result->fetch_assoc();

            // Verify the password using password_verify
            if (password_verify($password, $admin['password'])) {
                // Set session variables
                $_SESSION['admin_id'] = $admin['admin_id'];
                $_SESSION['username'] = $admin['username'];
                $_SESSION['email'] = $admin['email'];

                // Redirect to admin dashboard
                header("Location: admin/dashboard.php");
                exit();
            } else {
                $error = "Invalid Login ID or Password.";
            }
        } else {
            $error = "Invalid Login ID or Password.";
        }

        $stmt->close();

        // Search student by student ID or email
        $stmt = $conn->prepare("
            SELECT student_id, full_name, email, password
            FROM student
            WHERE student_id = ? OR email = ?
            LIMIT 1
        ");

        $stmt->bind_param("ss", $loginID, $loginID);
        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $student = $result->fetch_assoc();

            if (password_verify($password, $student['password'])) {

                $_SESSION['student_id'] = $student['student_id'];
                $_SESSION['student_name'] = $student['full_name'];
                $_SESSION['student_email'] = $student['email'];

                header("Location: student/dashboard.php");
                exit();
            }
        }

        $stmt->close();

        // Invalid Login
        $error = "Invalid login credentials.";

    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Online Library System</title>

    <!-- ----------- BOOTSTRAP 5 CDN ----------- -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- ------------ BOOTSTRAP ICONS ------------ -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- ---------- EXTERNAL CSS ---------- -->
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-5 col-md-7 col-sm-10">
                <div class="card login-card">
                    <div class="card-body px-5 pt-5 pb-4 text-center">
                        <i class="bi bi-book-half fs-2 text-white"></i>

                        <h2 class="fw-bold mt-3 text-light fs-4">
                            Online Library System
                        </h2>

                        <p class="text-color fw-semibold mb-2">
                            Sign in to your account
                        </p>

                        <div class="d-flex align-items-center justify-content-center">
                            <span class="divider"></span>
                            <i class="bi bi-book fs-4 mx-3"></i>
                            <span class="divider"></span>
                        </div>

                        <!-- ---- Form Start Here ---- -->
                        <form action="" method="POST">

                            <!-- ---- Login ID ---- -->
                            <div class="mb-3 text-start">
                                <label for="login_id" class="form-label fw-semibold text-light">
                                    Login ID
                                </label>

                                <div class="form-control py-2 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-person"></i>
                                    <input type="text" class="login-input" id="login_id" name="login_id"
                                        placeholder="Username, student ID or email" required>
                                </div>
                            </div>

                            <!-- ---- Password ---- -->
                            <div class="mb-4 text-start">
                                <label for="password" class="form-label fw-semibold text-light">
                                    Password
                                </label>

                                <div class="form-control py-2 d-flex align-items-center justify-content-center">
                                    <i class="bi bi-lock"></i>
                                    <input type="password" class="login-input" id="password"
                                        name="password" placeholder="Password" required>
                                </div>
                            </div>

                            <!-- ---- Submit Btn ---- -->
                            <div class="d-grid">
                                <button type="submit" class="btn btn-lg text-light login-btn">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                    Login
                                </button>
                            </div>

                            <div class="d-flex align-items-center justify-content-center wel-msg">
                                <span class="divider"></span>
                                <p class="text-color fw-semibold mx-3">
                                    Welcome!
                                </p>
                                <span class="divider"></span>
                            </div>

                            <p class="text-color mb-0">
                                <i class="bi bi-shield-check"></i>
                                Access your account to continue
                            </p>

                        </form>
                    </div>
                </div>

                <!-- ---- Message ---- -->
                <p class="text-color mt-4 mb-0 d-flex justify-content-center align-items-center text-light">
                    <i class="bi bi-book me-3"></i>
                    Read
                    <i class="bi bi-dot"></i>
                    Learn
                    <i class="bi bi-dot"></i>
                    Grow
                </p>
            </div>
        </div>
    </div>
</body>

</html>