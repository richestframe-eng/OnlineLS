<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/notification.php';

requireStudent();

$studentId = $_SESSION['student_id'];
$studentName = $_SESSION['student_name'];

sendDueDateReminders($conn, $studentId);

// ==========================
// Student Statistics
// ==========================

// Currently Issued Books
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM issue_return
    WHERE student_id = ?
      AND status = 'Issued'
");

$stmt->bind_param("i", $studentId);
$stmt->execute();

$currentIssued = $stmt->get_result()->fetch_assoc()['total'];


// Total Borrowing History
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM issue_return
    WHERE student_id = ?
");

$stmt->bind_param("i", $studentId);
$stmt->execute();

$totalTransactions = $stmt->get_result()->fetch_assoc()['total'];


// Overdue Books
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM issue_return
    WHERE student_id = ?
      AND status = 'Issued'
      AND due_date < CURDATE()
");

$stmt->bind_param("i", $studentId);
$stmt->execute();

$totalOverdue = $stmt->get_result()->fetch_assoc()['total'];


// Total Fine
$stmt = $conn->prepare("
    SELECT IFNULL(SUM(fine), 0) AS total
    FROM issue_return
    WHERE student_id = ?
");

$stmt->bind_param("i", $studentId);
$stmt->execute();

$totalFine = $stmt->get_result()->fetch_assoc()['total'];


// ==========================
// Recent Transactions
// ==========================

$stmt = $conn->prepare("
    SELECT
        ir.issue_date,
        ir.due_date,
        ir.return_date,
        ir.status,
        ir.fine,
        b.title
    FROM issue_return ir
    INNER JOIN book b
        ON ir.book_id = b.book_id
    WHERE ir.student_id = ?
    ORDER BY ir.issue_date DESC
    LIMIT 5
");

$stmt->bind_param("i", $studentId);
$stmt->execute();

$recentTransactions = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Student Dashboard | Online Library System</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Student CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">

</head>

<body>

    <div class="container-fluid m-0 p-0 d-flex">

        <!-- ==========================
         Sidebar
        =========================== -->

        <?php include '../includes/sidebar.php'; ?>


        <!-- ==========================
         Main Container
        =========================== -->

        <div class="main-container flex-grow-1">

            <?php include '../includes/header.php'; ?>


            <!-- ==========================
             Main Content
            =========================== -->

            <main class="main-content">

                <?php include "../includes/alert.php"; ?>


                <div class="container-fluid">


                    <!-- ==========================
                     Welcome Section
                =========================== -->

                    <section class="welcome-card">

                        <div class="welcome-content">

                            <h2>
                                Welcome, <?= htmlspecialchars($studentName); ?>!
                            </h2>

                            <p>
                                You can search books, view your issued books
                                and check your borrowing history here.
                            </p>

                        </div>

                        <div class="welcome-decora"></div>

                    </section>


                    <!-- ==========================
                     Statistics Cards
                    =========================== -->

                    <section class="stats-container">

                        <!-- Currently Issued -->
                        <div class="stat-card">
                            <div class="details-card">
                                <i class="bi bi-book-half bg-primary"></i>
                                <div class="text-card">
                                    <h3>Issued Books</h3>
                                    <p><?= $currentIssued; ?></p>
                                </div>
                            </div>
                            <div class="btn-card">
                                <a href="issued_books.php">
                                    <span>View issued books</span>
                                    <i class="bi bi-arrow-right-short"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Borrowing History -->
                        <div class="stat-card">
                            <div class="details-card">
                                <i class="bi bi-journal-bookmark-fill bg-success"></i>
                                <div class="text-card">
                                    <h3>Transactions</h3>
                                    <p><?= $totalTransactions; ?></p>
                                </div>
                            </div>
                            <div class="btn-card">
                                <a href="issued_books.php">
                                    <span>View history</span>
                                    <i class="bi bi-arrow-right-short"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Overdue -->
                        <div class="stat-card">
                            <div class="details-card">
                                <i class="bi bi-exclamation-triangle-fill bg-danger"></i>
                                <div class="text-card">
                                    <h3>Overdue</h3>
                                    <p><?= $totalOverdue; ?></p>
                                </div>
                            </div>
                            <div class="btn-card">
                                <a href="issued_books.php">
                                    <span>View overdue</span>
                                    <i class="bi bi-arrow-right-short"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Fine -->
                        <div class="stat-card">
                            <div class="details-card">
                                <i class="bi bi-cash-stack bg-warning"></i>
                                <div class="text-card">
                                    <h3>Total Fine</h3>
                                    <p>Rs. <?= number_format($totalFine, 2); ?></p>
                                </div>
                            </div>
                            <div class="btn-card">
                                <a href="fines.php">
                                    <span>View fines</span>
                                    <i class="bi bi-arrow-right-short"></i>
                                </a>
                            </div>
                        </div>

                    </section>


                    <!-- ==========================
                     Quick Actions
                    =========================== -->

                    <div class="content-card mt-4">

                            <h5 class="section-title mb-0">
                                <i class="bi bi-lightning-fill"></i>
                                Quick Actions
                            </h5>

                        <div class="mt-3">

                            <div class="d-flex flex-wrap gap-2">

                                <a
                                    href="search.php"
                                    class="btn btn-primary">

                                    <i class="bi bi-search me-1"></i>

                                    Search Books

                                </a>


                                <a
                                    href="issued_books.php"
                                    class="btn btn-success">

                                    <i class="bi bi-book me-1"></i>

                                    My Books

                                </a>


                                <a
                                    href="profile.php"
                                    class="btn btn-secondary">

                                    <i class="bi bi-person me-1"></i>

                                    My Profile

                                </a>

                            </div>

                        </div>

                    </div>


                    <!-- ==========================
                     Recent Transactions
                    =========================== -->

                    <div class="content-card mt-4">

                        <div class="card-header mb-3">

                            <h5>

                                <i class="bi bi-clock-history"></i>

                                Recent Transactions

                            </h5>

                        </div>


                        <div class="table-responsive">

                            <table class="table table-bordered table-hover align-middle mb-0">

                                <thead class="table-light">

                                    <tr>

                                        <th>#</th>

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

                                    if ($recentTransactions->num_rows > 0):

                                        while ($row = $recentTransactions->fetch_assoc()):

                                    ?>

                                            <tr>

                                                <td>
                                                    <?= $sn++; ?>
                                                </td>


                                                <td>
                                                    <?= htmlspecialchars($row['title']); ?>
                                                </td>


                                                <td>
                                                    <?= htmlspecialchars($row['issue_date']); ?>
                                                </td>


                                                <td>
                                                    <?= htmlspecialchars($row['due_date']); ?>
                                                </td>


                                                <td>

                                                    <?= $row['return_date']
                                                        ? htmlspecialchars($row['return_date'])
                                                        : '-'; ?>

                                                </td>


                                                <td>

                                                    <?php

                                                    if (
                                                        $row['status'] === 'Issued'
                                                        && $row['due_date'] < date('Y-m-d')
                                                    ) {

                                                        echo '<span class="badge bg-warning text-dark">
                                                    Overdue
                                                  </span>';
                                                    } elseif ($row['status'] === 'Issued') {

                                                        echo '<span class="badge bg-primary">
                                                    Issued
                                                  </span>';
                                                    } elseif ($row['status'] === 'Returned') {

                                                        echo '<span class="badge bg-success">
                                                    Returned
                                                  </span>';
                                                    }

                                                    ?>

                                                </td>


                                                <td>

                                                    Rs.
                                                    <?= number_format($row['fine'], 2); ?>

                                                </td>

                                            </tr>

                                        <?php

                                        endwhile;

                                    else:

                                        ?>

                                        <tr>

                                            <td
                                                colspan="7"
                                                class="text-center text-muted py-4">

                                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                                                No transactions found.

                                            </td>

                                        </tr>

                                    <?php endif; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>


                </div>

            </main>

        </div>

    </div>


    <!-- Bootstrap JS -->

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js">
    </script>

    <!-- External JS -->
    <script src="../assets/js/script.js"></script>

</body>

</html>