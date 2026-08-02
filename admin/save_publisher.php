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

    $publisher_name = trim($_POST["publisher_name"]);

    // =========================================
    // Validate
    // =========================================

    if (empty($publisher_name)) {

        $_SESSION["error"] = "Publisher name is required.";

        header("Location: publishers.php");
        exit();

    }

    // =========================================
    // Check Duplicate Publisher
    // =========================================

    $check = $conn->prepare("
        SELECT publisher_id
        FROM publisher
        WHERE publisher_name = ?
    ");

    $check->bind_param("s", $publisher_name);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {

        $_SESSION["error"] = "Publisher already exists.";

        header("Location: publishers.php");
        exit();

    }

    $check->close();

    // =========================================
    // Insert Publisher
    // =========================================

    $stmt = $conn->prepare("
        INSERT INTO publisher
        (
            publisher_name
        )
        VALUES
        (
            ?
        )
    ");

    $stmt->bind_param("s", $publisher_name);

    if ($stmt->execute()) {

        $_SESSION["success"] = "Publisher added successfully.";

    } else {

        $_SESSION["error"] = "Failed to add publisher.";

    }

    $stmt->close();
    $conn->close();

    header("Location: publishers.php");
    exit();

?>