
<?php

require_once "../../includes/db.php";

$name = trim($_POST['publisher_name'] ?? '');

if ($name == "") {

    echo json_encode([
        "success" => false,
        "message" => "Publisher name required."
    ]);
    exit;
}

$check = $conn->prepare("
    SELECT publisher_id
    FROM publisher
    WHERE publisher_name = ?
");

$check->bind_param("s", $name);
$check->execute();

$result = $check->get_result();

if ($row = $result->fetch_assoc()) {

    echo json_encode([
        "success" => true,
        "id" => $row['publisher_id'],
        "name" => $name
    ]);

    exit;
}

$stmt = $conn->prepare("
    INSERT INTO publisher(publisher_name)
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
        "message" => "Unable to add publisher."
    ]);

}
