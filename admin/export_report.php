<?php

require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="library_report.csv"');

$output = fopen("php://output", "w");

fputcsv($output, [
    "Student",
    "Book",
    "Issue Date",
    "Due Date",
    "Return Date",
    "Status",
    "Fine"
]);

$from    = $_GET['from'] ?? '';
$to      = $_GET['to'] ?? '';
$student = $_GET['student'] ?? '';
$status  = $_GET['status'] ?? '';

$sql = "
SELECT
    s.full_name,
    b.title,
    ir.issue_date,
    ir.due_date,
    ir.return_date,
    ir.status,
    ir.fine
FROM issue_return ir
JOIN student s
    ON ir.student_id = s.student_id
JOIN book b
    ON ir.book_id = b.book_id
WHERE 1
";

$params = [];
$types = "";

// Filters
if (!empty($from)) {
    $sql .= " AND ir.issue_date >= ?";
    $params[] = $from;
    $types .= "s";
}

if (!empty($to)) {
    $sql .= " AND ir.issue_date <= ?";
    $params[] = $to;
    $types .= "s";
}

if (!empty($student)) {
    $sql .= " AND ir.student_id = ?";
    $params[] = $student;
    $types .= "i";
}

if (!empty($status)) {

    if ($status == "Overdue") {

        $sql .= "
            AND ir.status='Issued'
            AND ir.due_date < CURDATE()
        ";

    } else {

        $sql .= " AND ir.status=?";
        $params[] = $status;
        $types .= "s";

    }

}

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {

    fputcsv($output, [
        $row['full_name'],
        $row['title'],
        $row['issue_date'],
        $row['due_date'],
        $row['return_date'],
        $row['status'],
        $row['fine']
    ]);

}

fclose($output);
exit;