<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

requireStudent();

$pageTitle = 'My Fines';

$studentId = $_SESSION['student_id'];

$stmt = $conn->prepare("
    SELECT
        ir.transaction_id,
        b.title,
        ir.issue_date,
        ir.due_date,
        ir.return_date,
        ir.fine
    FROM issue_return ir
    INNER JOIN book b
        ON ir.book_id = b.book_id
    WHERE ir.student_id = ?
    AND ir.fine > 0
    ORDER BY ir.transaction_id DESC
");

$stmt->bind_param("i", $studentId);
$stmt->execute();

$result = $stmt->get_result();

$totalFine = 0;

while ($row = $result->fetch_assoc()) {
    $totalFine += $row['fine'];
}

$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>My Fines - Online Library System</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="../assets/css/style.css">

</head>

<body>

<div class="d-flex">

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-container flex-grow-1">

        <?php include '../includes/header.php'; ?>

        <main class="content px-4 py-3">

            <div class="page-heading">

                <div>

                    <h4>My Fines</h4>

                    <p>
                        View your library fines.
                    </p>

                </div>

            </div>


            <!-- Total Fine -->

            <div class="card mb-4">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h6 class="text-muted mb-1">
                                Total Fine
                            </h6>

                            <h3 class="mb-0 text-danger">
                                Rs. <?= number_format($totalFine, 2); ?>
                            </h3>

                        </div>

                        <i class="bi bi-cash-stack fs-1 text-danger"></i>

                    </div>

                </div>

            </div>


            <!-- Fine Table -->

            <div class="card">

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover table-bordered align-middle mb-0">

                            <thead>

                                <tr>

                                    <th>S.N.</th>

                                    <th>Book</th>

                                    <th>Issue Date</th>

                                    <th>Due Date</th>

                                    <th>Return Date</th>

                                    <th>Fine</th>

                                </tr>

                            </thead>


                            <tbody>

                            <?php if ($result->num_rows > 0): ?>

                                <?php

                                $sn = 1;

                                while ($row = $result->fetch_assoc()):

                                ?>

                                    <tr>

                                        <td>
                                            <?= $sn++; ?>
                                        </td>

                                        <td>
                                            <strong>
                                                <?= htmlspecialchars($row['title']); ?>
                                            </strong>
                                        </td>

                                        <td>
                                            <?= date(
                                                'd M Y',
                                                strtotime($row['issue_date'])
                                            ); ?>
                                        </td>

                                        <td>
                                            <?= date(
                                                'd M Y',
                                                strtotime($row['due_date'])
                                            ); ?>
                                        </td>

                                        <td>

                                            <?php if (!empty($row['return_date'])): ?>

                                                <?= date(
                                                    'd M Y',
                                                    strtotime($row['return_date'])
                                                ); ?>

                                            <?php else: ?>

                                                -

                                            <?php endif; ?>

                                        </td>

                                        <td class="text-danger fw-bold">

                                            Rs.
                                            <?= number_format(
                                                $row['fine'],
                                                2
                                            ); ?>

                                        </td>

                                    </tr>

                                <?php endwhile; ?>

                            <?php else: ?>

                                <tr>

                                    <td
                                        colspan="6"
                                        class="text-center text-muted py-4">

                                        <i class="bi bi-check-circle fs-3 d-block mb-2"></i>

                                        No fines found.

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

</body>

</html>