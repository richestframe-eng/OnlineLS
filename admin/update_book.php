<?php

    require_once '../includes/auth.php';
    requireAdmin();
    require_once '../includes/db.php';

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: books.php");
        exit;
    }

    $book_id = (int)$_POST['book_id'];

    $title = trim($_POST['title']);
    $isbn = trim($_POST['isbn']);

    $author_id = (int)$_POST['author_id'];
    $publisher_id = (int)$_POST['publisher_id'];
    $category_id = (int)$_POST['category_id'];

    $publication_year = $_POST['publication_year'];

    $total = (int)$_POST['total_quantity'];
    $available = (int)$_POST['available_quantity'];

    $description = trim($_POST['description']);

    $stmt = $conn->prepare("
        UPDATE book
        SET
        title = ?,
        isbn = ?,
        publication_year = ?,
        total = ?,
        available = ?,
        author_id = ?,
        publisher_id = ?,
        category_id = ?,
        description = ?
        WHERE book_id = ?
    ");

    $stmt->bind_param(
        "ssiiiiiisi",
        $title,
        $isbn,
        $publication_year,
        $total,
        $available,
        $author_id,
        $publisher_id,
        $category_id,
        $description,
        $book_id
    );

    if ($stmt->execute()) 
    {
        header("Location: books.php");
        exit();
    } else {

        echo $stmt->error;

    }

    $stmt->close();
    $conn->close();

?>