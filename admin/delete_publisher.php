<?php

session_start();
require_once "../includes/db.php";

// =========================================
// Check ID
// =========================================

if (!isset($_GET["id"])) {

    header("Location: publishers.php");
    exit();

}

$publisher_id = intval($_GET["id"]);

// =========================================
// Check if Publisher has Books
// =========================================

$check = $conn->prepare("
    SELECT book_id
    FROM book
    WHERE publisher_id = ?
");

$check->bind_param("i", $publisher_id);
$check->execute();

if ($check->get_result()->num_rows > 0) {

    $_SESSION["error"] = "Cannot delete publisher. Books are assigned to this publisher.";

    header("Location: publishers.php");
    exit();

}

$check->close();

// =========================================
// Delete Publisher  
// =========================================

$stmt = $conn->prepare("
    DELETE FROM publisher
    WHERE publisher_id = ?
");

$stmt->bind_param("i", $publisher_id);

if ($stmt->execute()) {

    $_SESSION["success"] = "Publisher deleted successfully.";

} else {

    $_SESSION["error"] = "Failed to delete publisher.";

}

$stmt->close();
$conn->close();

header("Location: publishers.php");
exit();

?>