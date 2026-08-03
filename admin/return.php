<?php

session_start();
require_once "../includes/db.php";

if (!isset($_GET["id"])) {

    $_SESSION["error"] = "Invalid request.";
    header("Location: issue.php");
    exit();
}

$transaction_id = intval($_GET["id"]);

$conn->begin_transaction();

try {

    // Get issue record
    $check = $conn->prepare("
        SELECT book_id, due_date
        FROM issue_return
        WHERE transaction_id = ?
        AND status = 'Issued'
    ");

    $check->bind_param("i", $transaction_id);
    $check->execute();

    $result = $check->get_result();

    if ($result->num_rows == 0) {

        throw new Exception("Book already returned.");
    }

    $issue = $result->fetch_assoc();

    $book_id = $issue["book_id"];
    $due_date = $issue["due_date"];

    // Today's date
    $return_date = date("Y-m-d");

    // -------------------------
    // Fine Calculation
    // -------------------------

    $fine = 0;

    if ($return_date > $due_date) {

        $late_days = (strtotime($return_date) - strtotime($due_date)) / 86400;

        $fine = $late_days * 10;   // Rs.10 per day
    }

    // -------------------------
    // Update transaction
    // -------------------------

    $update = $conn->prepare("
        UPDATE issue_return
        SET
            return_date = ?,
            status = 'Returned',
            fine = ?
        WHERE transaction_id = ?
    ");

    $update->bind_param(
        "sdi",
        $return_date,
        $fine,
        $transaction_id
    );

    $update->execute();

    // -------------------------
    // Increase available copies
    // -------------------------

    $book = $conn->prepare("
        UPDATE book
        SET available = available + 1
        WHERE book_id = ?
    ");

    $book->bind_param("i", $book_id);
    $book->execute();

    $conn->commit();

    $_SESSION["success"] = "Book returned successfully.";

} catch (Exception $e) {

    $conn->rollback();

    $_SESSION["error"] = $e->getMessage();
}

header("Location: issue.php");
exit();

?>