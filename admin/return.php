<?php

require_once "../includes/auth.php";
require_once "../includes/db.php";

requireAdmin();

session_start();

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    $_SESSION["error"] = "Invalid request.";
    header("Location: issue.php");
    exit();

}

$transaction_id = intval($_GET["id"]);

$conn->begin_transaction();

try {

    // ==========================
    // Get Issue Record
    // ==========================

    $check = $conn->prepare("
        SELECT
            book_id,
            due_date
        FROM issue_return
        WHERE transaction_id = ?
          AND status = 'Issued'
    ");

    $check->bind_param(
        "i",
        $transaction_id
    );

    $check->execute();

    $result = $check->get_result();


    if ($result->num_rows === 0) {

        throw new Exception(
            "Book already returned or transaction not found."
        );

    }


    $issue = $result->fetch_assoc();

    $book_id = (int) $issue["book_id"];

    $due_date = $issue["due_date"];


    // ==========================
    // Return Date
    // ==========================

    $return_date = date("Y-m-d");


    // ==========================
    // Fine Calculation
    // Rs. 10 per late day
    // ==========================

    $fine = 0;

    if ($return_date > $due_date) {

        $late_days = floor(
            (strtotime($return_date) - strtotime($due_date))
            / 86400
        );

        $fine = $late_days * 10;

    }


    // ==========================
    // Update Issue Record
    // ==========================

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


    // ==========================
    // Increase Available Copies
    // ==========================

    $book = $conn->prepare("
        UPDATE book
        SET available = available + 1
        WHERE book_id = ?
    ");

    $book->bind_param(
        "i",
        $book_id
    );

    $book->execute();


    // ==========================
    // Commit
    // ==========================

    $conn->commit();


    if ($fine > 0) {

        $_SESSION["success"] =
            "Book returned successfully. Fine: Rs. "
            . number_format($fine, 2);

    } else {

        $_SESSION["success"] =
            "Book returned successfully.";

    }


} catch (Exception $e) {

    $conn->rollback();

    $_SESSION["error"] = $e->getMessage();

}


header("Location: issue.php");

exit();

?>