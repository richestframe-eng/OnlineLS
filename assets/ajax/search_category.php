<?php

require_once '../../includes/db.php';

header('Content-Type: application/json');

if (!isset($_GET['q'])) {
    echo json_encode([]);
    exit;
}

$search = trim($_GET['q']);

$stmt = $conn->prepare("
    SELECT category_id, category_name
    FROM category
    WHERE category_name LIKE CONCAT('%', ?, '%')
    ORDER BY category_name ASC
    LIMIT 10
");

$stmt->bind_param("s", $search);
$stmt->execute();

$result = $stmt->get_result();

$authors = [];

while ($row = $result->fetch_assoc()) {
    $authors[] = $row;
}

echo json_encode($authors);