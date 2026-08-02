<?php

session_start();
require_once "../includes/db.php";

// =========================================
// Check ID
// =========================================

if (!isset($_GET["id"])) {

    header("Location: authors.php");
    exit();

}

$author_id = intval($_GET["id"]);

// =========================================
// Check if Author has Books
// =========================================

$check = $conn->prepare("
    SELECT book_id
    FROM book
    WHERE author_id = ?
");

$check->bind_param("i", $author_id);
$check->execute();

if ($check->get_result()->num_rows > 0) {

    $_SESSION["error"] = "Cannot delete author. Books are assigned to this author.";

    header("Location: authors.php");
    exit();

}

$check->close();

// =========================================
// Delete Author
// =========================================

$stmt = $conn->prepare("
    DELETE FROM author
    WHERE author_id = ?
");

$stmt->bind_param("i", $author_id);

if ($stmt->execute()) {

    $_SESSION["success"] = "Author deleted successfully.";

} else {

    $_SESSION["error"] = "Failed to delete author.";

}

$stmt->close();
$conn->close();

header("Location: authors.php");
exit();

?>