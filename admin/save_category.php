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

    $category_name = trim($_POST["category_name"]);

    // =========================================
    // Validate
    // =========================================

    if (empty($category_name)) {

        $_SESSION["error"] = "Category name is required.";

        header("Location: categories.php");
        exit();

    }

    // =========================================
    // Check Duplicate Category
    // =========================================

    $check = $conn->prepare("
        SELECT category_id
        FROM category
        WHERE category_name = ?
    ");

    $check->bind_param("s", $category_name);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {

        $_SESSION["error"] = "Category already exists.";

        header("Location: categories.php");
        exit();

    }

    $check->close();

    // =========================================
    // Insert Category
    // =========================================

    $stmt = $conn->prepare("
        INSERT INTO category
        (
            category_name
        )
        VALUES
        (
            ?
        )
    ");

    $stmt->bind_param("s", $category_name);

    if ($stmt->execute()) {

        $_SESSION["success"] = "Category added successfully.";

    } else {

        $_SESSION["error"] = "Failed to add category.";

    }

    $stmt->close();
    $conn->close();

    header("Location: categories.php");
    exit();

?>