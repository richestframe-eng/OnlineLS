<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

if (!isset($_GET['id'])) {

    header("Location: books.php");
    exit;

}

$book_id = (int) $_GET['id'];

$check = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM issue_return
    WHERE book_id = ?
    AND status = 'Issued'
");

$check->bind_param("i", $book_id);
$check->execute();

$result = $check->get_result();
$row = $result->fetch_assoc();

if ($row['total'] > 0) {

    // Later this will become a notification
    die("This book is currently issued and cannot be deleted.");

}

$stmt = $conn->prepare("
    DELETE FROM book
    WHERE book_id = ?
");

$stmt->bind_param("i", $book_id);

if ($stmt->execute()) {

    header("Location: books.php");
    exit;

} else {

    echo "Delete failed.";

}

$stmt->close();
$conn->close();