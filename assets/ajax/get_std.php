<?php

    require_once "../../includes/db.php";

    // Check ID
    if (!isset($_GET["id"])) {

        echo json_encode([
            "error" => "Invalid request"
        ]);

        exit();

    }

    $id = intval($_GET["id"]);

    // Fetch student
    $stmt = $conn->prepare("
        SELECT *
        FROM student
        WHERE student_id = ?
    ");

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        echo json_encode(
            $result->fetch_assoc()
        );

    } else {

        echo json_encode([
            "error" => "Student not found"
        ]);

    }

    $stmt->close();
    $conn->close();

?>