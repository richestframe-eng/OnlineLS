<?php

session_start();
require_once "../includes/db.php";

// =========================================
// Allow POST Request Only
// =========================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    
    header("Location: categories.php");
    exit();

}

// =========================================
// Get Form Data
// =========================================

$category_id = intval($_POST["category_id"]);
$category_name = trim($_POST["category_name"]);

// =========================================
// Validation
// =========================================

if (empty($category_name)) {

    $_SESSION["error"] = "Category name is required.";

    header("Location: categories.php");
    exit();

}

// =========================================
// Check Category Exists
// =========================================

$stmt = $conn->prepare("
    SELECT category_id
    FROM category
    WHERE category_id = ?
");

$stmt->bind_param("i", $category_id);
$stmt->execute();

if ($stmt->get_result()->num_rows == 0) {

    $_SESSION["error"] = "Category not found.";

    header("Location: categories.php");
    exit();

}

$stmt->close();

// =========================================
// Duplicate Check
// =========================================

$check = $conn->prepare("
    SELECT category_id
    FROM category
    WHERE category_name = ?
    AND category_id != ?
");

$check->bind_param(
    "si",
    $category_name,
    $category_id
);

$check->execute();

if ($check->get_result()->num_rows > 0) {

    $_SESSION["error"] = "Category already exists.";

    header("Location: categories.php");
    exit();

}

$check->close();

// =========================================
// Update Category
// =========================================

$update = $conn->prepare("
    UPDATE category
    SET category_name = ?
    WHERE category_id = ?
");

$update->bind_param(
    "si",
    $category_name,
    $category_id
);

if ($update->execute()) {

    $_SESSION["success"] = "Category updated successfully.";

} else {

    $_SESSION["error"] = "Failed to update category.";

}

$update->close();
$conn->close();

header("Location: categories.php");
exit();

?>