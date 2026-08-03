<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

// Students
$students = $conn->query("
    SELECT
        student_id,
        full_name
    FROM student
    ORDER BY full_name ASC
");

// Available Books
$books = $conn->query("
    SELECT
        book_id,
        title,
        available
    FROM book
    WHERE available > 0
    ORDER BY title ASC
");

// =========================================
// Current Issued Books
// =========================================
$issued_books = $conn->query("
    SELECT
        ir.transaction_id,
        s.full_name,
        b.title,
        ir.issue_date,
        ir.due_date,
        ir.status
    FROM issue_return ir

    INNER JOIN student s
        ON ir.student_id = s.student_id

    INNER JOIN book b
        ON ir.book_id = b.book_id

    WHERE ir.status = 'Issued'

    ORDER BY ir.issue_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Issued Book | Online Library System</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Admin CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

    <div class="container-fluid m-0 p-0 d-flex">
        <!-- ===== Sidebar ===== -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- ==== Content ===== -->
        <div class="main-container flex-grow-1">

            <?php include '../includes/header.php'; ?>

            <!-- Books Page Content Starts Here -->
            <main class="main-content">
                <?php include "../includes/alert.php"; ?>
                <div class="container-fluid">

                    <!-- ===== Page Heading ===== -->
                    <div class="page-header d-flex align-item-center justify-content-between">
                        <div class="title">
                            <h3>Issue Book</h3>
                            <span>
                                <a href="dashboard.php">Home</a> /
                                <a href="issue_book.php">Issue Book</a>
                            </span>
                        </div>
                    </div>

                    <!-- ===== Filter Card ===== -->
                    <div class="filter-card">

                        <form action="save_issue.php" method="POST">

                            <div class="row g-3">

                                <!-- Student -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Student
                                    </label>

                                    <select
                                        class="form-select"
                                        name="student_id"
                                        required>

                                        <option value="" selected disabled>
                                            Select Student
                                        </option>

                                        <?php while ($student = $students->fetch_assoc()) : ?>

                                            <option value="<?= $student['student_id']; ?>">

                                                <?= htmlspecialchars($student['full_name']); ?>

                                            </option>

                                        <?php endwhile; ?>

                                    </select>

                                </div>

                                <!-- Book -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Book
                                    </label>

                                    <select
                                        class="form-select"
                                        name="book_id"
                                        required>

                                        <option value="" selected disabled>
                                            Select Book
                                        </option>

                                        <?php while ($book = $books->fetch_assoc()) : ?>

                                            <option value="<?= $book['book_id']; ?>">
                                                <?= htmlspecialchars($book['title']); ?>
                                                (Available: <?= $book['available']; ?>)
                                            </option>

                                        <?php endwhile; ?>

                                    </select>

                                </div>

                                <!-- Issue Date -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Issue Date
                                    </label>

                                    <input
                                        type="date"
                                        class="form-control"
                                        name="issue_date"
                                        value="<?= date('Y-m-d'); ?>"
                                        readonly>

                                </div>

                                <!-- Due Date -->
                                <div class="col-md-6">

                                    <label class="form-label">
                                        Due Date
                                    </label>

                                    <input
                                        type="date"
                                        class="form-control"
                                        name="due_date"
                                        value="<?= date('Y-m-d', strtotime('+7 days')); ?>"
                                        readonly>

                                </div>

                                <div class="col-12 text-end">

                                    <button
                                        type="submit"
                                        class="btn btn-primary">

                                        <i class="bi bi-journal-plus"></i>

                                        Issue Book

                                    </button>

                                </div>

                            </div>

                        </form>

                    </div>

                    <!-- ===== Books Table ===== -->
                    <div class="table-card">

                        <div class="table-header">

                            <div class="d-flex align-item-center justify-content-between">
                                <h5 class="mb-0">
                                    <i class="bi bi-book-half"></i>
                                    Issued Books List
                                </h5>

                                <p class="text-dark">
                                    <?php
                                        $total_issued = $issued_books->num_rows;
                                    ?>
                                    Current Issued Books : <?= $total_issued; ?>
                                </p>
                            </div>

                        </div>

                        <div class="table-responsive">

                            <table class="table table-hover table-bordered align-middle mb-0">

                                <thead class="table-dark">

                                    <tr>
                                        <th>S.N.</th>
                                        <th>Student</th>
                                        <th>Book</th>
                                        <th>Issue Date</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    <?php

                                    $sn = 1;

                                    while ($row = $issued_books->fetch_assoc()) :

                                    ?>

                                        <tr>

                                            <td><?= $sn++; ?></td>

                                            <td><?= htmlspecialchars($row["full_name"]); ?></td>

                                            <td><?= htmlspecialchars($row["title"]); ?></td>

                                            <td><?= $row["issue_date"]; ?></td>

                                            <td><?= $row["due_date"]; ?></td>

                                            <td>

                                                <?php if ($row["status"] == "Issued") : ?>

                                                    <span class="badge bg-success">
                                                        Issued
                                                    </span>

                                                <?php else : ?>

                                                    <span class="badge bg-secondary">
                                                        Returned
                                                    </span>

                                                <?php endif; ?>

                                            </td>

                                            <td class="text-center">

                                                <button
                                                    class="btn btn-sm btn-primary return-book"
                                                    data-id="<?= $row["transaction_id"]; ?>">

                                                    <i class="bi bi-arrow-return-left"></i>

                                                    Return

                                                </button>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                    <!-- ===== Pagination ===== -->
                    <div class="pagination-card mt-3">

                        <div class="d-flex justify-content-between align-items-center flex-wrap">

                            <small class="text-muted">
                                <?php
                                    $totalIssued = $issued_books->num_rows;
                                ?>
                                Showing 1 to <?= $totalIssued; ?> of <?= $totalIssued; ?> issued books
                            </small>

                            <nav>

                                <ul class="pagination mb-0">

                                    <li class="page-item disabled">
                                        <a class="page-link" href="#">
                                            Previous
                                        </a>
                                    </li>

                                    <li class="page-item active">
                                        <a class="page-link" href="#">
                                            1
                                        </a>
                                    </li>

                                    <li class="page-item">
                                        <a class="page-link" href="#">
                                            2
                                        </a>
                                    </li>

                                    <li class="page-item">
                                        <a class="page-link" href="#">
                                            3
                                        </a>
                                    </li>

                                    <li class="page-item">
                                        <a class="page-link" href="#">
                                            Next
                                        </a>
                                    </li>

                                </ul>

                            </nav>

                        </div>

                    </div>
                </div>
            </main>

        </div>
    </div>

    <!-- ==== Bootstrap JS ==== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ==== JavaScript ==== -->
    <script src="../assets/js/issue.js"></script>

</body>

</html>