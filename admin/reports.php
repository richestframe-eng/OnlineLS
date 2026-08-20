<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

$pageTitle = 'Reports';

// ==========================
// Summary Cards
// ==========================

// Total Books
$totalBooks = $conn->query("
    SELECT COUNT(*) AS total
    FROM book
")->fetch_assoc()['total'];

// Total Students
$totalStudents = $conn->query("
    SELECT COUNT(*) AS total
    FROM student
")->fetch_assoc()['total'];

// Issued Books
$totalIssued = $conn->query("
    SELECT COUNT(*) AS total
    FROM issue_return
    WHERE status = 'Issued'
")->fetch_assoc()['total'];

// Returned Books
$totalReturned = $conn->query("
    SELECT COUNT(*) AS total
    FROM issue_return
    WHERE status = 'Returned'
")->fetch_assoc()['total'];

// Overdue Books
$totalOverdue = $conn->query("
    SELECT COUNT(*) AS total
    FROM issue_return
    WHERE status = 'Issued'
      AND due_date < CURDATE()
")->fetch_assoc()['total'];

// ==========================
// Report Filters
// ==========================
$from    = $_GET['from'] ?? '';
$to      = $_GET['to'] ?? '';
$student = $_GET['student'] ?? '';
$status  = $_GET['status'] ?? '';

$sql = "
SELECT
    ir.transaction_id,
    s.full_name,
    b.title,
    ir.issue_date,
    ir.due_date,
    ir.return_date,
    ir.status,
    ir.fine
FROM issue_return ir
INNER JOIN student s
    ON ir.student_id = s.student_id
INNER JOIN book b
    ON ir.book_id = b.book_id
WHERE 1
";

$params = [];
$types = "";

// From Date
if (!empty($from)) {
    $sql .= " AND ir.issue_date >= ?";
    $params[] = $from;
    $types .= "s";
}

// To Date
if (!empty($to)) {
    $sql .= " AND ir.issue_date <= ?";
    $params[] = $to;
    $types .= "s";
}

// Student
if (!empty($student)) {
    $sql .= " AND ir.student_id = ?";
    $params[] = $student;
    $types .= "i";
}

// Status
if (!empty($status)) {

    if ($status == "Overdue") {

        $sql .= "
            AND ir.status = 'Issued'
            AND ir.due_date < CURDATE()
        ";

    } else {

        $sql .= " AND ir.status = ?";
        $params[] = $status;
        $types .= "s";

    }

}

$sql .= " ORDER BY ir.issue_date DESC";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$reportResult = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reports | Online Library System</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Admin CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="container-fluid m-0 p-0 d-flex">
        <!-- ===== Sidebar ===== -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- ==== Content ===== -->
        <div class="main-container flex-grow-1">

            <?php include '../includes/header.php'; ?>

            <!-- Categories Page Content Starts Here -->
            <main class="main-content">

                <?php include "../includes/alert.php"; ?>

                <div class="container-fluid">
                    <!-- Statistics Cards -->
                    <section class="stats-container">

                        <div class="stat-card">
                            <div class="details-card">
                                <i class="bi bi-book-half bg-primary"></i>
                                <div class="text-card">
                                    <h3>Total Books</h3>
                                    <p><?= $totalBooks; ?></p>
                                </div>
                            </div>
                            <div class="btn-card">
                                <a href="books.php">
                                    <span>View all books</span>
                                    <i class="bi bi-arrow-right-short"></i>
                                </a>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="details-card">
                                <i class="bi bi-people-fill bg-success"></i>
                                <div class="text-card">
                                    <h3>Issued</h3>
                                    <p><?= $totalIssued; ?></p>
                                </div>
                            </div>
                            <div class="btn-card">
                                <a href="issue.php">
                                    <span>View issued books</span>
                                    <i class="bi bi-arrow-right-short"></i>
                                </a>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="details-card">
                                <i class="bi bi-journal-bookmark-fill bg-danger"></i>
                                <div class="text-card">
                                    <h3>Returned</h3>
                                    <p><?= $totalReturned; ?></p>
                                </div>
                            </div>
                            <div class="btn-card">
                                <a href="return.php">
                                    <span>View returned books</span>
                                    <i class="bi bi-arrow-right-short"></i>
                                </a>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="details-card">
                                <i class="bi bi-clipboard-data-fill bg-dark"></i>
                                <div class="text-card">
                                    <h3>Overdue</h3>
                                    <p><?= $totalOverdue; ?></p>
                                </div>
                            </div>
                            <div class="btn-card">
                                <a href=".php">
                                    <span>View all overdues</span>
                                    <i class="bi bi-arrow-right-short"></i>
                                </a>
                            </div>
                        </div>

                    </section>

                    <!-- Student Summary -->
<section class="stats-container mt-3">

    <div class="stat-card" style="max-width:320px;">

        <div class="details-card">

            <i class="bi bi-people-fill bg-success"></i>

            <div class="text-card">

                <h3>Total Students</h3>

                <p><?= $totalStudents; ?></p>

            </div>

        </div>

        <div class="btn-card">

            <a href="students.php">

                <span>View all students</span>

                <i class="bi bi-arrow-right-short"></i>

            </a>

        </div>

    </div>

</section>

<div class="content-card mt-4">

    <div class="card-header">
        <h5>
            <i class="bi bi-funnel-fill"></i>
            Report Filters
        </h5>
    </div>

    <form method="GET" action="reports.php">

        <div class="row g-3">

            <div class="col-md-2">

                <label class="form-label">From</label>

                <input
                    type="date"
                    class="form-control"
                    name="from"
                    value="<?= htmlspecialchars($from); ?>"
                >

            </div>

            <div class="col-md-2">

                <label class="form-label">To</label>

                <input
                    type="date"
                    class="form-control"
                    name="to"
                    value="<?= htmlspecialchars($to); ?>"
                >

            </div>

            <div class="col-md-3">

                <label class="form-label">Student</label>

                <select
                    class="form-select"
                    name="student">

                    <option value="">All Students</option>

                    <?php

                    $students = $conn->query("
                        SELECT student_id, full_name
                        FROM student
                        ORDER BY full_name
                    ");

                    while($student = $students->fetch_assoc()):

                    ?>

                        <option
                            value="<?= $student['student_id']; ?>"
                            <?= ($student['student_id'] == ($_GET['student'] ?? '')) ? 'selected' : ''; ?>
                        ></option>

                    <?php endwhile; ?>

                </select>

            </div>

            <div class="col-md-2">

                <label class="form-label">Status</label>

                <select
                    class="form-select"
                    name="status">

                    <option value="">All</option>
                    <option value="Issued"
                        <?= (($_GET['status'] ?? '') == 'Issued') ? 'selected' : ''; ?>>
                        Issued
                    </option>

                    <option value="Issued"
                        <?= (($_GET['status'] ?? '') == 'Issued') ? 'selected' : ''; ?>>
                        Returned
                    </option>
                    
                    <option value="Issued"
                        <?= (($_GET['status'] ?? '') == 'Issued') ? 'selected' : ''; ?>>
                        Overdue
                    </option>

                </select>

            </div>

            <div class="col-md-3 d-flex align-items-end gap-2">

                <button class="btn btn-primary">

                    <i class="bi bi-search"></i>

                    Generate

                </button>

                <a
                    href="reports.php"
                    class="btn btn-secondary">

                    <i class="bi bi-arrow-clockwise"></i>

                    Reset

                </a>

            </div>

        </div>

    </form>

</div>

<div class="content-card mt-4">

    <div class="card-header">
        <h5>
            <i class="bi bi-table"></i>
            Transaction Report
        </h5>
    </div>

    <div class="table-responsive">

        <table class="table table-hover align-middle table-bordered mb-0">

            <thead class="table-light">

                <tr>

                    <th>#</th>
                    <th>Student</th>
                    <th>Book</th>
                    <th>Issue Date</th>
                    <th>Due Date</th>
                    <th>Return Date</th>
                    <th>Status</th>
                    <th>Fine</th>

                </tr>

            </thead>

            <tbody>

                <?php
                $sn = 1;

                if($reportResult->num_rows > 0):

                    while($row = $reportResult->fetch_assoc()):
                ?>

                <tr>

                    <td><?= $sn++; ?></td>

                    <td><?= htmlspecialchars($row['full_name']); ?></td>

                    <td><?= htmlspecialchars($row['title']); ?></td>

                    <td><?= $row['issue_date']; ?></td>

                    <td><?= $row['due_date']; ?></td>

                    <td>
                        <?= $row['return_date'] ?: '-'; ?>
                    </td>

                    <td>

                        <?php

                        if($row['status'] == "Issued"){

                            echo '<span class="badge bg-danger">Issued</span>';

                        }
                        elseif($row['status'] == "Returned"){

                            echo '<span class="badge bg-success">Returned</span>';

                        }

                        ?>

                    </td>

                    <td>
                        Rs. <?= number_format($row['fine'],2); ?>
                    </td>

                </tr>

                <?php
                    endwhile;

                else:
                ?>

                <tr>

                    <td colspan="8" class="text-center text-muted">

                        No records found.

                    </td>

                </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<div class="d-flex justify-content-end gap-2 mb-3">

    <a 
        href="reports_pdf.php?<?= http_build_query($_GET); ?>"
        class="btn btn-danger">
        <i class="bi bi-file-earmark-pdf-fill"></i>
        Export PDF
    </a>

    <a
        href="export_report.php?<?= http_build_query($_GET); ?>"
        class="btn btn-primary">

        <i class="bi bi-download"></i>
        Export CSV

    </a>

</div>
                    
                </div>
            </main>

        </div>
    </div>

    <!-- ==== Bootstrap JS ==== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ==== JavaScript ==== -->
     <script src="../assets/js/script.js"></script>
    <script src="../assets/js/report.js"></script>

</body>

</html>