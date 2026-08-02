<?php

session_start();
require_once "../includes/db.php";

// =========================================
// Allow POST Request Only
// =========================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    
    header("Location: authors.php");
    exit();

}

// =========================================
// Get Form Data
// =========================================

$author_id = intval($_POST["author_id"]);
$author_name = trim($_POST["author_name"]);

// =========================================
// Validation
// =========================================

if (empty($author_name)) {

    $_SESSION["error"] = "Author name is required.";

    header("Location: authors.php");
    exit();

}

// =========================================
// Check Author Exists
// =========================================

$stmt = $conn->prepare("
    SELECT author_id
    FROM author
    WHERE author_id = ?
");

$stmt->bind_param("i", $author_id);
$stmt->execute();

if ($stmt->get_result()->num_rows == 0) {

    $_SESSION["error"] = "Author not found.";

    header("Location: authors.php");
    exit();

}

$stmt->close();

// =========================================
// Duplicate Check
// =========================================

$check = $conn->prepare("
    SELECT author_id
    FROM author
    WHERE author_name = ?
    AND author_id != ?
");

$check->bind_param(
    "si",
    $author_name,
    $author_id
);

$check->execute();

if ($check->get_result()->num_rows > 0) {

    $_SESSION["error"] = "Author already exists.";

    header("Location: authors.php");
    exit();

}

$check->close();

// =========================================
// Update Author
// =========================================

$update = $conn->prepare("
    UPDATE author
    SET author_name = ?
    WHERE author_id = ?
");

$update->bind_param(
    "si",
    $author_name,
    $author_id
);

if ($update->execute()) {

    $_SESSION["success"] = "Author updated successfully.";

} else {

    $_SESSION["error"] = "Failed to update author.";

}

$update->close();
$conn->close();

header("Location: authors.php");
exit();

?>