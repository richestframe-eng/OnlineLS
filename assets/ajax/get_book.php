<?php

require_once '../../includes/db.php';

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode([]);
    exit;
}

$book_id = (int) $_GET['id'];

$stmt = $conn->prepare("
    SELECT
    b.*,
    a.author_name,
    p.publisher_name,
    c.category_name
    FROM book b
    INNER JOIN author a
    ON b.author_id = a.author_id

    INNER JOIN publisher p
    ON b.publisher_id = p.publisher_id

    INNER JOIN category c
    ON b.category_id = c.category_id

    WHERE b.book_id = ?
");

$stmt->bind_param("i", $book_id);

$stmt->execute();

$result = $stmt->get_result();

echo json_encode($result->fetch_assoc());

?>