<?php

require_once "../includes/db.php";

if (!isset($_GET["id"])) {
    exit();
}

$category_id = intval($_GET["id"]);

$stmt = $conn->prepare("
    SELECT
        category_id,
        category_name
    FROM category
    WHERE category_id = ?
");

$stmt->bind_param("i", $category_id);
$stmt->execute();

$result = $stmt->get_result();

echo json_encode($result->fetch_assoc());

$stmt->close();
$conn->close();

?>