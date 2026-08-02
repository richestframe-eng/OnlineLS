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

    $author_name = trim($_POST["author_name"]);

    // =========================================
    // Validate
    // =========================================

    if (empty($author_name)) {

        $_SESSION["error"] = "Author name is required.";

        header("Location: authors.php");
        exit();

    }

    // =========================================
    // Check Duplicate Author
    // =========================================

    $check = $conn->prepare("
        SELECT author_id
        FROM author
        WHERE author_name = ?
    ");

    $check->bind_param("s", $author_name);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {

        $_SESSION["error"] = "Author already exists.";

        header("Location: authors.php");
        exit();

    }

    $check->close();

    // =========================================
    // Insert Author
    // =========================================

    $stmt = $conn->prepare("
        INSERT INTO author
        (
            author_name
        )
        VALUES
        (
            ?
        )
    ");

    $stmt->bind_param("s", $author_name);

    if ($stmt->execute()) {

        $_SESSION["success"] = "Author added successfully.";

    } else {

        $_SESSION["error"] = "Failed to add author.";

    }

    $stmt->close();
    $conn->close();

    header("Location: authors.php");
    exit();

?>