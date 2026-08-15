<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

requireStudent();


// ==========================
// Validate Book ID
// ==========================

if (
    !isset($_GET['book_id']) ||
    !is_numeric($_GET['book_id'])
) {
    header("Location: search.php");
    exit();
}

$studentId = $_SESSION['student_id'];
$bookId = (int) $_GET['book_id'];


// ==========================
// Check Book
// ==========================

$stmt = $conn->prepare("
    SELECT
        book_id,
        title,
        available
    FROM book
    WHERE book_id = ?
");

$stmt->bind_param("i", $bookId);
$stmt->execute();

$result = $stmt->get_result();

$book = $result->fetch_assoc();


// Book doesn't exist

if (!$book) {
    header("Location: search.php");
    exit();
}


// ==========================
// Check Availability
// ==========================

if ((int) $book['available'] <= 0) {
    header("Location: search.php?error=unavailable");
    exit();
}


// ==========================
// Check Existing Pending Request
// ==========================

$stmt = $conn->prepare("
    SELECT request_id
    FROM request
    WHERE student_id = ?
      AND book_id = ?
      AND status = 'Pending'
    LIMIT 1
");

$stmt->bind_param(
    "ii",
    $studentId,
    $bookId
);

$stmt->execute();

$result = $stmt->get_result();


// Already requested

if ($result->num_rows > 0) {
    header("Location: search.php?error=already_requested");
    exit();
}


// ==========================
// Create Request
// ==========================

$stmt = $conn->prepare("
    INSERT INTO request (
        student_id,
        book_id,
        status
    )
    VALUES (?, ?, 'Pending')
");

$stmt->bind_param(
    "ii",
    $studentId,
    $bookId
);


if ($stmt->execute()) {

    header("Location: search.php?success=requested");
    exit();

}


// Something went wrong

header("Location: search.php?error=request_failed");
exit();

?>