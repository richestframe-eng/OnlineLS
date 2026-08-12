<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

// =========================================
// Dashboard Statistics
// =========================================

// Total Books
$totalBooks = $conn->query("
        SELECT COUNT(*) AS total
        FROM book
    ")->fetch_assoc()["total"];

// Total Students
$totalStudents = $conn->query("
        SELECT COUNT(*) AS total
        FROM student
    ")->fetch_assoc()["total"];

// Issued Books
$totalIssued = $conn->query("
        SELECT COUNT(*) AS total
        FROM issue_return
        WHERE status = 'Issued'
    ")->fetch_assoc()["total"];

// Available Books
$totalAvailable = $conn->query("
        SELECT SUM(available) AS total
        FROM book
    ")->fetch_assoc()["total"];

// Total Authors
$totalAuthors = $conn->query("
        SELECT COUNT(*) AS total
        FROM author
    ")->fetch_assoc()["total"];

// Total Publishers
$totalPublishers = $conn->query("
        SELECT COUNT(*) AS total
        FROM publisher
    ")->fetch_assoc()["total"];

// Total Categories
$totalCategories = $conn->query("
        SELECT COUNT(*) AS total
        FROM category
    ")->fetch_assoc()["total"];

// =========================================
// Recently Issued Books
// =========================================
$recentBooks = $conn->query("
        SELECT
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
        LIMIT 5
    ");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | Online Library System</title>

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

        <!-- ==== Container ==== -->
        <div class="main-container flex-grow-1">
            <!-- ===== Header ===== -->
            <?php include '../includes/header.php'; ?>

            <!-- ==== Content ===== -->
            <main class="main-content">

                <!-- Welcome Card -->
                <section class="welcome-card">

                    <div class="welcome-content">
                        <h2>
                            Welcome back, Admin! 👋
                        </h2>

                        <p>
                            Here's what's happening in your library today.
                        </p>
                    </div>

                    <div class="welcome-decora"></div>

                </section>

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

                    <div class="stat-card">
                        <div class="details-card">
                            <i class="bi bi-journal-bookmark-fill bg-danger"></i>
                            <div class="text-card">
                                <h3>Issued Books</h3>
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
                            <i class="bi bi-clipboard-data-fill bg-dark"></i>
                            <div class="text-card">
                                <h3>Available Books</h3>
                                <p><?= $totalAvailable; ?></p>
                            </div>
                        </div>
                        <div class="btn-card">
                            <a href="books.php">
                                <span>View available books</span>
                                <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </div>
                    </div>

                </section>

                <h5 class="section-title mt-4 mb-3">
                    <i class="bi bi-collection-fill"></i>
                    Library Information
                </h5>

                <section class="stats-container info-stats">

                    <div class="stat-card">
                        <div class="details-card">
                            <i class="bi bi-pencil-fill bg-warning"></i>
                            <div class="text-card">
                                <h3>Total Authors</h3>
                                <p><?= $totalAuthors; ?></p>
                            </div>
                        </div>
                        <div class="btn-card">
                            <a href="authors.php">
                                <span>View all authors</span>
                                <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="details-card">
                            <i class="bi bi-building-fill bg-info"></i>
                            <div class="text-card">
                                <h3>Total Publishers</h3>
                                <p><?= $totalPublishers; ?></p>
                            </div>
                        </div>
                        <div class="btn-card">
                            <a href="publishers.php">
                                <span>View all publishers</span>
                                <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="details-card">
                            <i class="bi bi-folder-fill bg-secondary"></i>
                            <div class="text-card">
                                <h3>Total Categories</h3>
                                <p><?= $totalCategories; ?></p>
                            </div>
                        </div>
                        <div class="btn-card">
                            <a href="categories.php">
                                <span>View all categories</span>
                                <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </div>
                    </div>

                </section>

                <!-- Recently Issued Books -->
                <section class="recent-books">

                    <div class="recent-books-header">

                        <div>
                            <i class="bi bi-clock"></i>
                            <h4>Recently Issued Books</h4>
                        </div>

                        <a href="issue.php" class="view-all-btn">
                            View All
                            <i class="bi bi-chevron-right ms-1"></i>
                        </a>

                    </div>

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle mb-0">

                            <thead>
                                <tr class="table-dark">
                                    <th>S.N.</th>
                                    <th>Student</th>
                                    <th>Book Title</th>
                                    <th>Issue Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php
                                $sn = 1;

                                while ($row = $recentBooks->fetch_assoc()) :
                                ?>

                                    <tr>

                                        <td><?= $sn++; ?></td>

                                        <td><?= htmlspecialchars($row["full_name"]); ?></td>

                                        <td><?= htmlspecialchars($row["title"]); ?></td>

                                        <td><?= htmlspecialchars($row["issue_date"]); ?></td>

                                        <td><?= htmlspecialchars($row["due_date"]); ?></td>

                                        <td>

                                            <span class="status-badge issued">
                                                <?= htmlspecialchars($row["status"]); ?>
                                            </span>

                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                                <?php if ($sn == 1) : ?>

                                    <tr>

                                        <td colspan="6" class="text-center text-muted py-4">
                                            No issued books found.
                                        </td>

                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </section>
            </main>
        </div>

    </div>

    <!-- ==== Bootstrap JS ==== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ==== External JS ====  -->
    <script src="../assets/js/script.js"></script>
</body>

</html>