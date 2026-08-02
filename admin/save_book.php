<?php

    require_once '../includes/auth.php';
    require_once '../includes/db.php';

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        header("Location: books.php");
        exit;
    }

    $title = trim($_POST['title']);
    $isbn = trim($_POST['isbn']);

    $author_id = (int)$_POST['author_id'];
    $publisher_id = (int)$_POST['publisher_id'];
    $category_id = (int)$_POST['category_id'];

    $publication_year = $_POST['publication_year'];

    $total_quantity = (int)$_POST['total_quantity'];
    $available_quantity = (int)$_POST['available_quantity'];

    $description = trim($_POST['description']);

    if (empty($title) || empty($isbn) || $author_id == 0 || $publisher_id == 0 || $category_id == 0) 
    {
        header("Location: books.php");
        exit();
    }

    $stmt = $conn->prepare("
        INSERT INTO book
        (
            title,
            isbn,
            publication_year,
            total,
            available,
            author_id,
            publisher_id,
            category_id,
            description
        )VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "ssiiiiiis",
        $title,
        $isbn,
        $publication_year,
        $total_quantity,
        $available_quantity,
        $author_id,
        $publisher_id,
        $category_id,
        $description
    );

    if ($stmt->execute()) 
    {
        header("Location: books.php");
        exit();
    } else {

        echo "Error: " . $stmt->error;

    }

    $stmt->close();
    $conn->close();

?>