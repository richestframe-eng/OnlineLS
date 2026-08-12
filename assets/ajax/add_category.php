
<?php

require_once "../../includes/db.php";

$name = trim($_POST['category_name'] ?? '');

if ($name == "") {

    echo json_encode([
        "success" => false,
        "message" => "Category name required."
    ]);
    exit;
}

$check = $conn->prepare("
    SELECT category_id
    FROM category
    WHERE category_name = ?
");

$check->bind_param("s", $name);
$check->execute();

$result = $check->get_result();

if ($row = $result->fetch_assoc()) {

    echo json_encode([
        "success" => true,
        "id" => $row['category_id'],
        "name" => $name
    ]);

    exit;
}

$stmt = $conn->prepare("
    INSERT INTO category(category_name)
    VALUES(?)
");

$stmt->bind_param("s", $name);

if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "id" => $stmt->insert_id,
        "name" => $name
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Unable to add category."
    ]);

}
