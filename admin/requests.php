<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

requireAdmin();


// ==========================
// Handle Request Status
// ==========================

if (
    isset($_GET['action']) &&
    isset($_GET['request_id']) &&
    is_numeric($_GET['request_id'])
) {

    $requestId = (int) $_GET['request_id'];
    $action = $_GET['action'];

    if ($action === 'approve') {

        $status = 'Approved';

    } elseif ($action === 'reject') {

        $status = 'Rejected';

    } else {

        header("Location: requests.php");
        exit();

    }


    $stmt = $conn->prepare("
        UPDATE request
        SET status = ?
        WHERE request_id = ?
          AND status = 'Pending'
    ");

    $stmt->bind_param(
        "si",
        $status,
        $requestId
    );

    $stmt->execute();

    header("Location: requests.php");
    exit();
}


// ==========================
// Fetch Requests
// ==========================

$stmt = $conn->prepare("
    SELECT
        r.request_id,
        r.request_date,
        r.status,

        s.student_id,
        s.full_name,

        b.book_id,
        b.title,
        b.isbn

    FROM request r

    INNER JOIN student s
        ON r.student_id = s.student_id

    INNER JOIN book b
        ON r.book_id = b.book_id

    ORDER BY r.request_date DESC
");

$stmt->execute();

$requests = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Book Requests - Online Library System</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>


<body>

<div class="d-flex">

    <?php include '../includes/sidebar.php'; ?>


    <div class="main-container flex-grow-1">

        <?php include '../includes/header.php'; ?>


        <main class="content px-4 py-3">

            <div class="page-heading">

                <div>

                    <h2>Book Requests</h2>

                    <p>
                        Manage student book requests.
                    </p>

                </div>

            </div>


            <div class="card">

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover table-bordered align-middle mb-0">

                            <thead>

                                <tr>

                                    <th>S.N.</th>
                                    <th>Student</th>
                                    <th>Book</th>
                                    <th>ISBN</th>
                                    <th>Request Date</th>
                                    <th>Status</th>
                                    <th>Action</th>

                                </tr>

                            </thead>


                            <tbody>

                            <?php if ($requests->num_rows > 0): ?>

                                <?php $sn = 1; ?>

                                <?php while ($row = $requests->fetch_assoc()): ?>

                                    <tr>

                                        <td>
                                            <?= $sn++; ?>
                                        </td>


                                        <td>

                                            <strong>
                                                <?= htmlspecialchars(
                                                    $row['full_name']
                                                ); ?>
                                            </strong>

                                            <br>

                                            <small class="text-muted">

                                                ID:
                                                <?= htmlspecialchars(
                                                    $row['student_id']
                                                ); ?>

                                            </small>

                                        </td>


                                        <td>

                                            <?= htmlspecialchars(
                                                $row['title']
                                            ); ?>

                                        </td>


                                        <td>

                                            <?= htmlspecialchars(
                                                $row['isbn']
                                            ); ?>

                                        </td>


                                        <td>

                                            <?= date(
                                                'd M Y, h:i A',
                                                strtotime($row['request_date'])
                                            ); ?>

                                        </td>


                                        <td>

                                            <?php if ($row['status'] === 'Pending'): ?>

                                                <span class="badge bg-warning text-dark">
                                                    Pending
                                                </span>

                                            <?php elseif ($row['status'] === 'Approved'): ?>

                                                <span class="badge bg-success">
                                                    Approved
                                                </span>

                                            <?php else: ?>

                                                <span class="badge bg-danger">
                                                    Rejected
                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <td>

                                            <?php if ($row['status'] === 'Pending'): ?>

                                                <a
                                                    href="requests.php?action=approve&request_id=<?= $row['request_id']; ?>"
                                                    class="btn btn-success btn-sm"
                                                    onclick="return confirm('Approve this book request?');"
                                                >
                                                    <i class="bi bi-check-lg"></i>
                                                    Approve
                                                </a>


                                                <a
                                                    href="requests.php?action=reject&request_id=<?= $row['request_id']; ?>"
                                                    class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Reject this book request?');"
                                                >
                                                    <i class="bi bi-x-lg"></i>
                                                    Reject
                                                </a>

                                            <?php else: ?>

                                                <span class="text-muted">
                                                    No action
                                                </span>

                                            <?php endif; ?>

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
                                            class="bi bi-inbox"
                                            style="font-size:45px;"
                                        ></i>

                                        <p class="mt-3 mb-0">
                                            No book requests found.
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

<script src="../assets/js/script.js"></script>

</body>

</html>