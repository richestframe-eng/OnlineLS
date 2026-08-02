<?php

require_once "../includes/db.php";

if (!isset($_GET["id"])) {
    exit();
}

$publisher_id = intval($_GET["id"]);

$stmt = $conn->prepare("
    SELECT
        publisher_id,
        publisher_name
    FROM publisher
    WHERE publisher_id = ?
");

$stmt->bind_param("i", $publisher_id);
$stmt->execute();

$result = $stmt->get_result();

echo json_encode($result->fetch_assoc());

$stmt->close();
$conn->close();

?>