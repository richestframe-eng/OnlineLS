<?php

        session_start();
        require_once "../includes/db.php";

        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            header("Location: students.php");
            exit();
        }

        $student_id = intval($_POST["student_id"]);

        $full_name = trim($_POST["full_name"]);
        $email = trim($_POST["email"]);
        $phone = trim($_POST["phone"]);
        $dob = trim($_POST["dob"]);
        $program = $_POST["program"];
        $semester = intval($_POST["semester"]);
        $address = trim($_POST["address"]);

        $password = $_POST["password"];
        $confirm_password = $_POST["confirm_password"];

        if (empty($full_name) || empty($email) || empty($phone) || empty($dob) || empty($program) || empty($semester) || empty($address)) 
        {
            $_SESSION["error"] = "Please fill all required fields.";
            header("Location: students.php");
            exit();
        }

        if (!empty($password)) {
            if ($password !== $confirm_password) {
                    $_SESSION["error"] = "Passwords do not match.";
                    header("Location: students.php");
                    exit();
            }
        }

        $stmt = $conn->prepare("
            SELECT photo, password
            FROM student
            WHERE student_id = ?
        ");

        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $current = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$current) {
            $_SESSION["error"] = "Student not found.";
            header("Location: students.php");
            exit();
        }

        $email_check = $conn->prepare("
            SELECT student_id
            FROM student
            WHERE email = ?
            AND student_id != ?
        ");

        $email_check->bind_param(
            "si",
            $email,
            $student_id
        );

        $email_check->execute();

        if ($email_check->get_result()->num_rows > 0) {
            $_SESSION["error"] = "Email already exists.";
            header("Location: students.php");
            exit();
        }

        $phone_check = $conn->prepare("
            SELECT student_id
            FROM student
            WHERE phone = ?
            AND student_id != ?
        ");

        $phone_check->bind_param(
            "si",
            $phone,
            $student_id
        );

        $phone_check->execute();

        if ($phone_check->get_result()->num_rows > 0) {
            $_SESSION["error"] = "Phone number already exists.";
            header("Location: students.php");
            exit();
        }

        // =========================================
    // Password Handling
    // =========================================

    if (empty($password)) {

        // Keep existing password
        $hashed_password = $current["password"];

    } else {

        // Hash new password
        $hashed_password = password_hash(
            $password,
            PASSWORD_DEFAULT
        );

    }

    // =========================================
    // Photo Handling
    // =========================================

    $photo = $current["photo"];

    if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {

        $allowed_extensions = ["jpg", "jpeg", "png"];
        $file_name = basename($_FILES["photo"]["name"]);

        $file_extension = strtolower(
            pathinfo($file_name, PATHINFO_EXTENSION)
        );

        if (!in_array($file_extension, $allowed_extensions)) {

            $_SESSION["error"] = "Only JPG, JPEG and PNG images are allowed.";

            header("Location: students.php");
            exit();

        }

        $new_file_name = "student_" . time() . "." . $file_extension;

        $upload_path = "../assets/uploads/students/" . $new_file_name;

        if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $upload_path)) {

            $_SESSION["error"] = "Failed to upload new photo.";

            header("Location: students.php");
            exit();

        }

        // Delete old photo
        if (!empty($current["photo"])) {

            $old_photo = "../assets/uploads/" . $current["photo"];

            if (file_exists($old_photo)) {

                unlink($old_photo);

            }

        }

        $photo = "students/" . $new_file_name;

    }

    // =========================================
    // Update Student
    // =========================================

    $stmt = $conn->prepare("
        UPDATE student
        SET
            full_name = ?,
            address = ?,
            phone = ?,
            dob = ?,
            program = ?,
            semester = ?,
            email = ?,
            photo = ?,
            password = ?
        WHERE student_id = ?
    ");

    $stmt->bind_param(
        "sssssisssi",
        $full_name,
        $address,
        $phone,
        $dob,
        $program,
        $semester,
        $email,
        $photo,
        $hashed_password,
        $student_id
    );

    // =========================================
    // Execute Update
    // =========================================

    if ($stmt->execute()) {

        // Remove previous photo after successful database update
        if (isset($new_file_name) && !empty($current["photo"])) {
            $old_photo = "../assets/uploads/" . $current["photo"];
            if (file_exists($old_photo)) {
                unlink($old_photo);
            }
        }

        $_SESSION["success"] = "Student updated successfully.";

    } else {

        // Delete newly uploaded image if database update failed
        if (isset($upload_path) && file_exists($upload_path)) {
            unlink($upload_path);
        }

        $_SESSION["error"] = "Failed to update student.";

    }

    $stmt->close();
    $email_check->close();
    $phone_check->close();
    $conn->close();

    header("Location: students.php");
    exit();

?>