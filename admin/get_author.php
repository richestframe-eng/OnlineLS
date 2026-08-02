<?php

require_once "../includes/db.php";

if (!isset($_GET["id"])) {
    exit();
}

$author_id = intval($_GET["id"]);

$stmt = $conn->prepare("
    SELECT
        author_id,
        author_name
    FROM author
    WHERE author_id = ?
");

$stmt->bind_param("i", $author_id);
$stmt->execute();

$result = $stmt->get_result();

echo json_encode($result->fetch_assoc());

$stmt->close();
$conn->close();

?>