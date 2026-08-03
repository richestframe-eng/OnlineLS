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

    header("Location: issue.php");
    exit();
}


// =========================================
// Receive Form Data
// =========================================
$student_id = intval($_POST["student_id"]);
$book_id    = intval($_POST["book_id"]);
$issue_date = $_POST["issue_date"];
$due_date   = $_POST["due_date"];


// =========================================
// Validate Input
// =========================================
if (
    empty($student_id) ||
    empty($book_id) ||
    empty($issue_date) ||
    empty($due_date)
) {

    $_SESSION["error"] = "Please fill all required fields.";

    header("Location: issue.php");
    exit();
}

// =========================================
// Check Student Exists
// =========================================
$student_check = $conn->prepare("
    SELECT student_id
    FROM student
    WHERE student_id = ?
");

$student_check->bind_param("i", $student_id);
$student_check->execute();

if ($student_check->get_result()->num_rows == 0) {

    $_SESSION["error"] = "Student not found.";

    header("Location: issue.php");
    exit();
}

// =========================================
// Check Book Availability
// =========================================
$book_check = $conn->prepare("
    SELECT available
    FROM book
    WHERE book_id = ?
");

$book_check->bind_param("i", $book_id);
$book_check->execute();

$book_result = $book_check->get_result();

if ($book_result->num_rows == 0) {

    $_SESSION["error"] = "Book not found.";

    header("Location: issue.php");
    exit();
}

$book = $book_result->fetch_assoc();

if ($book["available"] <= 0) {

    $_SESSION["error"] = "Book is currently unavailable.";

    header("Location: issue.php");
    exit();
}


// =========================================
// Prevent Duplicate Issue
// =========================================
$duplicate = $conn->prepare("
    SELECT transaction_id
    FROM issue_return
    WHERE
        student_id = ?
        AND book_id = ?
        AND status = 'Issued'
");

$duplicate->bind_param(
    "ii",
    $student_id,
    $book_id
);

$duplicate->execute();

$result = $duplicate->get_result();

if ($result->num_rows > 0) {
    $_SESSION["error"] = "This student already has this book issued.";

    header("Location: issue.php");
    exit();
}


// =========================================
// Start Transaction
// =========================================
$conn->begin_transaction();

try {

    // =====================================
    // Insert Issue Record
    // =====================================
    $issue = $conn->prepare("
        INSERT INTO issue_return
        (
            student_id,
            book_id,
            issue_date,
            due_date,
            status,
            fine
        )
        VALUES
        (
            ?, ?, ?, ?, 'Issued', 0
        )
    ");

    $issue->bind_param(
        "iiss",
        $student_id,
        $book_id,
        $issue_date,
        $due_date
    );

    $issue->execute();


    // =====================================
    // Reduce Available Quantity
    // =====================================
    $update = $conn->prepare("
        UPDATE book
        SET available = available - 1
        WHERE book_id = ?
    ");

    $update->bind_param("i", $book_id);
    $update->execute();


    // =====================================
    // Commit
    // =====================================
    $conn->commit();

    $_SESSION["success"] = "Book issued successfully.";
}
catch (Exception $e) {

    $conn->rollback();

    $_SESSION["error"] = "Failed to issue book.";
}


// =========================================
// Close Resources
// =========================================
$duplicate->close();
$book_check->close();

if (isset($issue)) $issue->close();
if (isset($update)) $update->close();

$conn->close();
$student_check->close();

// =========================================
// Redirect
// =========================================
header("Location: issue.php");
exit();

?>