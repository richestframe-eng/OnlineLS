<?php

// =========================================
// Start Session & Database Connection
// =========================================
session_start();
require_once "../includes/db.php";


// =========================================
// Allow Only POST Request
// =========================================
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: students.php");
    exit();
}


// =========================================
// Check if Photo is Selected
// =========================================
if (!isset($_FILES["photo"]) || $_FILES["photo"]["error"] != 0) {

    $_SESSION["error"] = "Please select a student photo.";

    header("Location: students.php");
    exit();
}


// =========================================
// Receive Form Data
// =========================================
$full_name        = trim($_POST["full_name"]);
$email            = trim($_POST["email"]);
$phone            = trim($_POST["phone"]);
$dob              = trim($_POST["dob"]);
$program           = $_POST["program"];
$semester         = intval($_POST["semester"]);
$address          = trim($_POST["address"]);
$password         = $_POST["password"];
$confirm_password = $_POST["confirm_password"];


// =========================================
// Check Empty Fields
// =========================================
if (
    empty($full_name) ||
    empty($email) ||
    empty($phone) ||
    empty($dob) ||
    empty($program) ||
    empty($semester) ||
    empty($address) ||
    empty($password) ||
    empty($confirm_password)
) {

    $_SESSION["error"] = "Please fill all required fields.";

    header("Location: students.php");
    exit();
}


// =========================================
// Validate Full Name
// =========================================
if (
    strlen($full_name) < 2 ||
    strlen($full_name) > 50 ||
    !preg_match("/^[a-zA-Z .'-]+$/", $full_name)
) {

    $_SESSION["error"] =
        "Full name must contain only letters and be 2-50 characters long.";

    header("Location: students.php");
    exit();
}


// =========================================
// Validate Email
// =========================================
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $_SESSION["error"] = "Please enter a valid email address.";

    header("Location: students.php");
    exit();
}


// =========================================
// Validate Phone
// =========================================
if (!preg_match("/^[0-9]{10}$/", $phone)) {

    $_SESSION["error"] =
        "Phone number must contain exactly 10 digits.";

    header("Location: students.php");
    exit();
}


// =========================================
// Validate Date of Birth
// =========================================
$dob_date = DateTime::createFromFormat("Y-m-d", $dob);
$dob_errors = DateTime::getLastErrors();

if (
    !$dob_date ||
    ($dob_errors !== false &&
        ($dob_errors["warning_count"] > 0 || $dob_errors["error_count"] > 0)) ||
    $dob_date->format("Y-m-d") !== $dob
) {

    $_SESSION["error"] = "Please enter a valid date of birth.";

    header("Location: students.php");
    exit();
}

if ($dob_date > new DateTime("today")) {

    $_SESSION["error"] = "Date of birth cannot be in the future.";

    header("Location: students.php");
    exit();
}


// =========================================
// Validate Semester
// =========================================
if ($semester < 1 || $semester > 8) {

    $_SESSION["error"] = "Please select a valid semester.";

    header("Location: students.php");
    exit();
}


// =========================================
// Check Password Match
// =========================================
if ($password !== $confirm_password) {

    $_SESSION["error"] = "Passwords do not match.";

    header("Location: students.php");
    exit();
}


// =========================================
// Validate Password Length
// =========================================
if (strlen($password) < 6) {

    $_SESSION["error"] = "Password must be at least 6 characters long.";

    header("Location: students.php");
    exit();
}


// =========================================
// Validate Image Extension
// =========================================
$allowed_extensions = ["jpg", "jpeg", "png"];

$file_name      = basename($_FILES["photo"]["name"]);
$file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

if (!in_array($file_extension, $allowed_extensions)) {

    $_SESSION["error"] = "Only JPG, JPEG and PNG images are allowed.";

    header("Location: students.php");
    exit();
}


// =========================================
// Validate Image Size and Content
// =========================================
if ($_FILES["photo"]["size"] > 2 * 1024 * 1024) {

    $_SESSION["error"] = "Student photo must be 2 MB or smaller.";

    header("Location: students.php");
    exit();
}

$image_info = @getimagesize($_FILES["photo"]["tmp_name"]);

if (
    $image_info === false ||
    !in_array($image_info[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG], true)
) {

    $_SESSION["error"] = "The uploaded file is not a valid JPG, JPEG or PNG image.";

    header("Location: students.php");
    exit();
}


// =========================================
// Check Duplicate Email
// =========================================
$email_check = $conn->prepare(
    "SELECT student_id FROM student WHERE email = ?"
);

$email_check->bind_param("s", $email);
$email_check->execute();

$email_result = $email_check->get_result();

if ($email_result->num_rows > 0) {

    $_SESSION["error"] = "Email already exists.";

    header("Location: students.php");
    exit();
}


// =========================================
// Check Duplicate Phone
// =========================================
$phone_check = $conn->prepare(
    "SELECT student_id FROM student WHERE phone = ?"
);

$phone_check->bind_param("s", $phone);
$phone_check->execute();

$phone_result = $phone_check->get_result();

if ($phone_result->num_rows > 0) {

    $_SESSION["error"] = "Phone number already exists.";

    header("Location: students.php");
    exit();
}


// =========================================
// Generate Unique Image Name
// =========================================
$new_file_name = "student_" . time() . "." . $file_extension;

$upload_path = "../assets/uploads/students/" . $new_file_name;


// =========================================
// Upload Image
// =========================================
if (!move_uploaded_file($_FILES["photo"]["tmp_name"], $upload_path)) {

    $_SESSION["error"] = "Failed to upload student photo.";

    header("Location: students.php");
    exit();
}


// =========================================
// Store Relative Image Path
// =========================================
$photo = "students/" . $new_file_name;


// =========================================
// Hash Password
// =========================================
$hashed_password = password_hash(
    $password,
    PASSWORD_DEFAULT
);


// =========================================
// Insert Student
// =========================================
$stmt = $conn->prepare("
    INSERT INTO student
    (
        full_name,
        address,
        phone,
        dob,
        program,
        semester,
        email,
        photo,
        password
    )
    VALUES
    (
        ?, ?, ?, ?, ?, ?, ?, ?, ?
    )
");

$stmt->bind_param(
    "sssssisss",
    $full_name,
    $address,
    $phone,
    $dob,
    $program,
    $semester,
    $email,
    $photo,
    $hashed_password
);


// =========================================
// Execute Query
// =========================================
if ($stmt->execute()) {

    $_SESSION["success"] = "Student registered successfully.";

} else {

    // Remove uploaded image if database insert fails
    unlink($upload_path);

    $_SESSION["error"] = "Failed to register student.";

}


// =========================================
// Close Database Resources
// =========================================
$stmt->close();
$email_check->close();
$phone_check->close();
$conn->close();


// =========================================
// Redirect Back
// =========================================
header("Location: students.php");
exit();

?>
