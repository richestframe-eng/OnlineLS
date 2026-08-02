
<?php

session_start();
require_once "../includes/db.php";

// =========================================
// Check Request
// =========================================

if (!isset($_GET["id"])) {

    header("Location: students.php");
    exit();

}

$student_id = intval($_GET["id"]);

// =========================================
// Check Active Issued Books
// =========================================

$check_issue = $conn->prepare("
    SELECT transaction_id
    FROM issue_return
    WHERE student_id = ?
    AND status = 'Issued'
");

$check_issue->bind_param("i", $student_id);
$check_issue->execute();

if ($check_issue->get_result()->num_rows > 0) {

    $check_issue->close();
    $conn->close();

    $_SESSION["error"] =
        "Cannot delete student. Student still has issued book(s).";

    header("Location: students.php");
    exit();

}

$check_issue->close();

// =========================================
// Get Student Photo
// =========================================

$get_photo = $conn->prepare("
    SELECT photo
    FROM student
    WHERE student_id = ?
");

$get_photo->bind_param("i", $student_id);
$get_photo->execute();

$result = $get_photo->get_result();

if ($result->num_rows == 0) {

    $get_photo->close();
    $conn->close();

    $_SESSION["error"] = "Student not found.";

    header("Location: students.php");
    exit();

}

$student = $result->fetch_assoc();

$get_photo->close();

// =========================================
// Delete Student
// =========================================

$delete = $conn->prepare("
    DELETE FROM student
    WHERE student_id = ?
");

$delete->bind_param("i", $student_id);

if ($delete->execute()) {

    // Delete photo from uploads folder
    if (!empty($student["photo"])) {

        $photo_path =
            "../assets/uploads/" . $student["photo"];

        if (file_exists($photo_path)) {
            unlink($photo_path);
        }

    }

    $_SESSION["success"] =
        "Student deleted successfully.";

} else {

    $_SESSION["error"] =
        "Failed to delete student.";

}

$delete->close();
$conn->close();

header("Location: students.php");
exit();

?>