<?php

session_start();
require_once "../includes/db.php";

// =========================================
// Allow POST Request Only
// =========================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    
    header("Location: publishers.php");
    exit();

}

// =========================================
// Get Form Data
// =========================================

$publisher_id = intval($_POST["publisher_id"]);
$publisher_name = trim($_POST["publisher_name"]);

// =========================================
// Validation
// =========================================

if (empty($publisher_name)) {

    $_SESSION["error"] = "Publisher name is required.";

    header("Location: publishers.php");
    exit();

}

// =========================================
// Check Publisher Exists
// =========================================

$stmt = $conn->prepare("
    SELECT publisher_id
    FROM publisher
    WHERE publisher_id = ?
");

$stmt->bind_param("i", $publisher_id);
$stmt->execute();

if ($stmt->get_result()->num_rows == 0) {

    $_SESSION["error"] = "Publisher not found.";

    header("Location: publishers.php");
    exit();

}

$stmt->close();

// =========================================
// Duplicate Check
// =========================================

$check = $conn->prepare("
    SELECT publisher_id
    FROM publisher
    WHERE publisher_name = ?
    AND publisher_id != ?
");

$check->bind_param(
    "si",
    $publisher_name,
    $publisher_id
);

$check->execute();

if ($check->get_result()->num_rows > 0) {

    $_SESSION["error"] = "Publisher already exists.";

    header("Location: publishers.php");
    exit();

}

$check->close();

// =========================================
// Update Publisher
// =========================================

$update = $conn->prepare("
    UPDATE publisher
    SET publisher_name = ?
    WHERE publisher_id = ?
");

$update->bind_param(
    "si",
    $publisher_name,
    $publisher_id
);

if ($update->execute()) {

    $_SESSION["success"] = "Publisher updated successfully.";

} else {

    $_SESSION["error"] = "Failed to update publisher.";

}

$update->close();
$conn->close();

header("Location: publishers.php");
exit();

?>