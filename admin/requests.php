<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/notification.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['mark_request_notifications_read'])) {

    markNotificationsRead(
        $conn,
        'Admin',
        $_SESSION['admin_id']
    );

    exit;
}

$pageTitle = 'Book Requests';

if (
    isset($_GET['action']) &&
    isset($_GET['request_id']) &&
    is_numeric($_GET['request_id'])
) {

    $requestId = (int) $_GET['request_id'];
    $action = $_GET['action'];

    // ==========================
    // APPROVE
    // ==========================

    if ($action === 'approve') {

        $conn->begin_transaction();

        try {

            // Get request details
            $stmt = $conn->prepare("
                SELECT student_id, book_id
                FROM request
                WHERE request_id = ?
                AND status = 'Pending'
            ");

            $stmt->bind_param("i", $requestId);
            $stmt->execute();

            $request = $stmt->get_result()->fetch_assoc();

            if (!$request) {
                throw new Exception("Request not found.");
            }

            $studentId = $request['student_id'];
            $bookId = $request['book_id'];


            // Check availability
            $stmt = $conn->prepare("
                SELECT available
                FROM book
                WHERE book_id = ?
            ");

            $stmt->bind_param("i", $bookId);
            $stmt->execute();

            $book = $stmt->get_result()->fetch_assoc();

            if (!$book || $book['available'] <= 0) {
                throw new Exception("Book is not available.");
            }


            // Approve request
            $stmt = $conn->prepare("
                UPDATE request
                SET status = 'Approved'
                WHERE request_id = ?
            ");

            $stmt->bind_param("i", $requestId);
            $stmt->execute();


            // Create issue transaction
            $stmt = $conn->prepare("
                INSERT INTO issue_return
                (
                    student_id,
                    book_id,
                    issue_date,
                    due_date,
                    status
                )
                VALUES
                (
                    ?,
                    ?,
                    CURDATE(),
                    DATE_ADD(CURDATE(), INTERVAL 14 DAY),
                    'Issued'
                )
            ");

            $stmt->bind_param("ii", $studentId, $bookId);
            $stmt->execute();


            // Decrease available copies ONCE
            $stmt = $conn->prepare("
                UPDATE book
                SET available = available - 1
                WHERE book_id = ?
            ");

            $stmt->bind_param("i", $bookId);
            $stmt->execute();


            // Student notification
            addNotification(
                $conn,
                'Student',
                $studentId,
                'Your book request has been approved.'
            );


            $conn->commit();

            header("Location: requests.php?success=approved");
            exit();

        } catch (Exception $e) {

            $conn->rollback();

            header(
                "Location: requests.php?error="
                . urlencode($e->getMessage())
            );

            exit();
        }


    // ==========================
    // REJECT
    // ==========================

    } elseif ($action === 'reject') {

        // Get student ID first
        $stmt = $conn->prepare("
            SELECT student_id
            FROM request
            WHERE request_id = ?
            AND status = 'Pending'
        ");

        $stmt->bind_param("i", $requestId);
        $stmt->execute();

        $request = $stmt->get_result()->fetch_assoc();

        if (!$request) {
            header("Location: requests.php?error=Request+not+found");
            exit();
        }

        $studentId = $request['student_id'];


        // Reject request
        $stmt = $conn->prepare("
            UPDATE request
            SET status = 'Rejected'
            WHERE request_id = ?
        ");

        $stmt->bind_param("i", $requestId);
        $stmt->execute();


        // Student notification
        addNotification(
            $conn,
            'Student',
            $studentId,
            'Your book request has been rejected.'
        );

        addNotification(
            $conn,
            'Admin',
            1,
            'New book request received.'
        );

        header("Location: requests.php?success=rejected");
        exit();
    }
}

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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        markRequestNotificationsRead();
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    fetch('requests.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'mark_request_notifications_read=1'
    });

    const sidebarBadge =
        document.getElementById('sidebar-notification-badge');

    if (sidebarBadge) {
        sidebarBadge.remove();
    }

    const headerBadge =
        document.getElementById('notification-badge');

    if (headerBadge) {
        headerBadge.remove();
    }

});
</script>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

<script src="../assets/js/script.js"></script>

</body>

</html>