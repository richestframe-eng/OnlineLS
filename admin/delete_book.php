<?php

require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

if (!isset($_GET['id'])) {

    header("Location: books.php");
    exit;

}

$book_id = (int) $_GET['id'];

$check = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM issue_return
    WHERE
        book_id = ?
        AND status = 'Issued'
");

$check->bind_param("i", $book_id);
$check->execute();

$row = $check->get_result()->fetch_assoc();

if ($row['total'] > 0) {

    $_SESSION["error"] =
        "Book is currently issued and cannot be deleted.";

    header("Location: books.php");
    exit();
}

$conn->begin_transaction();

try {

    // Delete only returned history
    $history = $conn->prepare("
        DELETE
        FROM issue_return
        WHERE
            book_id = ?
            AND status = 'Returned'
    ");

    $history->bind_param("i", $book_id);
    $history->execute();


    // Delete book
    $book = $conn->prepare("
        DELETE
        FROM book
        WHERE book_id = ?
    ");

    $book->bind_param("i", $book_id);
    $book->execute();


    $conn->commit();

    $_SESSION["success"] =
        "Book deleted successfully.";

} catch (Exception $e) {

    $conn->rollback();

    $_SESSION["error"] =
        "Delete failed.";
}

?>