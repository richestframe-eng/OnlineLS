
<?php

require_once "../../includes/db.php";

$name = trim($_POST['author_name'] ?? '');

if ($name == "") {

    echo json_encode([
        "success" => false,
        "message" => "Author name required."
    ]);
    exit;
}

$check = $conn->prepare("
    SELECT author_id
    FROM author
    WHERE author_name = ?
");

$check->bind_param("s", $name);
$check->execute();

$result = $check->get_result();

if ($row = $result->fetch_assoc()) {

    echo json_encode([
        "success" => true,
        "id" => $row['author_id'],
        "name" => $name
    ]);

    exit;
}

$stmt = $conn->prepare("
    INSERT INTO author(author_name)
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
        "message" => "Unable to add author."
    ]);

}
