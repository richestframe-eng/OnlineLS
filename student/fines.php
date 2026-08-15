<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

requireStudent();

$studentId = $_SESSION['student_id'];


// ==========================
// Fetch Student's Fines
// ==========================

$stmt = $conn->prepare("
    SELECT
        ir.transaction_id,
        b.title,
        ir.issue_date,
        ir.due_date,
        ir.return_date,
        ir.status,
        ir.fine
    FROM issue_return ir

    INNER JOIN book b
        ON ir.book_id = b.book_id

    WHERE ir.student_id = ?
      AND ir.fine > 0

    ORDER BY ir.due_date DESC
");

$stmt->bind_param("i", $studentId);

$stmt->execute();

$result = $stmt->get_result();


// ==========================
// Calculate Total Fine
// ==========================

$totalFine = 0;

while ($row = $result->fetch_assoc()) {
    $totalFine += (float) $row['fine'];
}


// Run the query again for the table
$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Fines - Online Library System</title>


    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >


    <!-- Main CSS -->
    <link
        rel="stylesheet" href="../assets/css/style.css"
    >

</head>


<body>


<div class="d-flex">


    <!-- ==========================
         Sidebar
    =========================== -->

    <?php include '../includes/sidebar.php'; ?>


    <!-- ==========================
         Main Container
    =========================== -->

    <div class="main-container flex-grow-1">


        <!-- Header -->

        <?php include '../includes/header.php'; ?>


        <!-- ==========================
             Page Content
        =========================== -->

        <main class="content p-4">


            <!-- Page Heading -->

            <div class="page-heading">

                <div>

                    <h4>My Fines</h4>

                    <p>
                        View your outstanding library fines.
                    </p>

                </div>

            </div>


            <!-- ==========================
                 Total Fine
            =========================== -->

            <div class="card mb-4">

                <div class="card-body">

                    <div class="d-flex align-items-center">


                        <div
                            class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center me-3"
                            style="width: 55px; height: 55px;"
                        >

                            <i class="bi bi-cash-stack fs-4"></i>

                        </div>


                        <div>

                            <h6 class="mb-1">
                                Total Outstanding Fine
                            </h6>

                            <h3 class="mb-0">
                                Rs. <?= number_format($totalFine, 2); ?>
                            </h3>

                        </div>


                    </div>

                </div>

            </div>

            <!-- ==========================
                 Fine List
            =========================== -->
            <div class="card">

                <div class="card-body">

                    <h5 class="mb-3">
                        <i class="bi bi-receipt me-2"></i>
                        Fine Details
                    </h5>

                    <div class="table-responsive">

                        <table class="table table-hover align-middle table-bordered mb-0">

                            <thead>

                                <tr>

                                    <th>S.N.</th>
                                    <th>Book</th>
                                    <th>Issue Date</th>
                                    <th>Due Date</th>
                                    <th>Return Date</th>
                                    <th>Status</th>
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

                                                <?= htmlspecialchars(
                                                    $row['title']
                                                ); ?>

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


                                        <td>

                                            <?php if ($row['status'] === 'Issued'): ?>

                                                <span class="badge bg-primary">
                                                    Issued
                                                </span>

                                            <?php else: ?>

                                                <span class="badge bg-success">
                                                    Returned
                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <td>

                                            <strong class="text-danger">

                                                Rs.
                                                <?= number_format(
                                                    (float) $row['fine'],
                                                    2
                                                ); ?>

                                            </strong>

                                        </td>


                                    </tr>


                                <?php endwhile; ?>


                            <?php else: ?>


                                <tr>

                                    <td
                                        colspan="7"
                                        class="text-center py-5"
                                    >

                                        <i
                                            class="bi bi-check-circle text-success"
                                            style="font-size: 45px;"
                                        ></i>


                                        <p class="mt-3 mb-0">

                                            You have no outstanding fines.

                                        </p>

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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Main JS -->
<script src="../js/script.js"></script>

</body>

</html>