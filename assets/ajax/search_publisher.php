<?php

require_once '../../includes/db.php';

header('Content-Type: application/json');

if (!isset($_GET['q'])) {
    echo json_encode([]);
    exit;
}

$search = trim($_GET['q']);

$stmt = $conn->prepare("
    SELECT publisher_id, publisher_name
    FROM publisher
    WHERE publisher_name LIKE CONCAT('%', ?, '%')
    ORDER BY publisher_name ASC
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