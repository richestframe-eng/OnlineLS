<?php

session_start();
require_once "../includes/db.php";

// =========================================
// Check ID
// =========================================

if (!isset($_GET["id"])) {

    header("Location: categories.php");
    exit();

}

$category_id = intval($_GET["id"]);

// =========================================
// Check if Category has Books
// =========================================

$check = $conn->prepare("
    SELECT book_id
    FROM book
    WHERE category_id = ?
");

$check->bind_param("i", $category_id);
$check->execute();

if ($check->get_result()->num_rows > 0) {

    $_SESSION["error"] = "Cannot delete category. Books are assigned to this category.";

    header("Location: categories.php");
    exit();

}

$check->close();

// =========================================
// Delete Category  
// =========================================

$stmt = $conn->prepare("
    DELETE FROM category
    WHERE category_id = ?
");

$stmt->bind_param("i", $category_id);

if ($stmt->execute()) {

    $_SESSION["success"] = "Category deleted successfully.";

} else {

    $_SESSION["error"] = "Failed to delete category.";

}

$stmt->close();
$conn->close();

header("Location: categories.php");
exit();

?>