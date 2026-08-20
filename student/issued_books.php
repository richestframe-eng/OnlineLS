<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

requireStudent();

$pageTitle = 'My Books';


// ==========================
// Fetch Student's Books
// ==========================

$studentId = $_SESSION['student_id'];

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

     ORDER BY ir.issue_date DESC
");

 $stmt->bind_param("i", $studentId);

 $stmt->execute();

 $result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>My Books - Online Library System</title>

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


        <!-- ==========================
         Sidebar
    =========================== -->

        <?php include '../includes/sidebar.php'; ?>


        <!-- ==========================
         Main Content
    =========================== -->

        <div class="main-container flex-grow-1">


            <!-- Header -->

            <?php include '../includes/header.php'; ?>


            <main class="content px-4 py-3">


                <!-- Page Heading -->

                <div class="page-heading">

                    <div>
                        <h4>My Books</h4>
                        <p>
                            View your issued and returned books.
                        </p>
                    </div>

                </div>


                <!-- ==========================
                 Books Table
            =========================== -->

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


                                                <td>

                                                    <?php if ($row['status'] === 'Issued'): ?>

                                                        <span class="badge bg-primary">
                                                            Issued
                                                        </span>

                                                        <a
                                                            href="issue_slip.php?transaction_id=<?= $row['transaction_id']; ?>"
                                                            class="btn btn-sm btn-outline-primary"
                                                        >
                                                            <i class="bi bi-receipt"></i>
                                                            Issue Slip
                                                        </a>

                                                    <?php elseif ($row['status'] === 'Returned'): ?>

                                                        <span class="badge bg-success">
                                                            Returned
                                                        </span>

                                                    <?php endif; ?>

                                                </td>

                                                <td>
                                                    Rs.<?= number_format((float)$row['fine'], 2); ?>
                                                </td>

                                            </tr>

                                        <?php endwhile; ?>

                                    <?php else: ?>

                                        <tr>

                                            <td
                                                colspan="7"
                                                class="text-center py-4">

                                                <i
                                                    class="bi bi-journal-x"
                                                    style="font-size:40px;"></i>

                                                <p class="mt-2 mb-0">
                                                    You have no book transactions.
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


    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>

    <script src="../js/script.js"></script>


</body>

</html>