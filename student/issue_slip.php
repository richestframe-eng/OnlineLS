<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

requireStudent();

$studentId = $_SESSION['student_id'];

if (
    !isset($_GET['transaction_id']) ||
    !is_numeric($_GET['transaction_id'])
) {
    header("Location: issued_books.php");
    exit();
}

$transactionId = (int) $_GET['transaction_id'];

$stmt = $conn->prepare("
    SELECT
        ir.transaction_id,
        ir.issue_date,
        ir.due_date,
        ir.return_date,
        ir.status,
        ir.fine,

        s.student_id,
        s.full_name,
        s.email,
        s.photo,

        b.title,
        b.isbn

    FROM issue_return ir

    INNER JOIN student s
        ON ir.student_id = s.student_id

    INNER JOIN book b
        ON ir.book_id = b.book_id

    WHERE ir.transaction_id = ?
      AND ir.student_id = ?

    LIMIT 1
");

$stmt->bind_param("ii", $transactionId, $studentId);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = 'Issue record not found.';
    header("Location: issued_books.php");
    exit();
}

$issue = $result->fetch_assoc();

$pageTitle = 'Issue Slip';

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Issue Slip - Online Library System</title>

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

    <style>
        .issue-slip-card {
            max-width: 900px;
        }

        .student-photo {
            width: 95px;
            height: 115px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .student-photo-placeholder {
            width: 95px;
            height: 115px;
            border-radius: 8px;
        }

        .info-panel {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 16px;
            height: 100%;
        }

        .info-panel h6,
        .section-title {
            margin-bottom: 10px;
            font-weight: 700;
        }

        .info-line {
            margin-bottom: 6px;
        }

        .book-table {
            margin-bottom: 16px;
        }

        .slip-actions {
            border-top: 1px solid #dee2e6;
            padding-top: 16px;
        }

        @media print {
            .admin-header,
            .sidebar,
            .slip-actions {
                display: none !important;
            }

            .main-container {
                width: 100% !important;
                margin: 0 !important;
            }

            .content {
                padding: 0 !important;
            }

            .issue-slip-card {
                max-width: 100% !important;
                border: 0 !important;
                box-shadow: none !important;
            }

            .card-body {
                padding: 20px !important;
            }
        }
    </style>

</head>

<body>

<div class="d-flex">

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-container flex-grow-1">

        <?php include '../includes/header.php'; ?>

        <main class="content p-4">

            <div class="card shadow-sm mx-auto issue-slip-card">

                <div class="card-body px-4 py-2">

                    <!-- Slip Header -->
                    <div class="text-center">

                        <h5>Online Library System</h5>

                        <h6 class="text-muted">
                            Book Issue Slip
                        </h6>

                        <hr class="mb-3">

                    </div>


                    <!-- Student + Transaction Information -->
                    <div class="row g-3 mb-3">

                        <!-- Student Information -->
                        <div class="col-md-7">

                            <div class="info-panel">

                                <h6 class="section-title">
                                    Student Information
                                </h6>

                                <div class="d-flex align-items-center gap-3">

                                    <?php

                                    $photoFile = basename($issue['photo'] ?? '');

                                    $photoPath =
                                        '../assets/uploads/students/' . $photoFile;

                                    ?>

                                    <?php if (
                                        !empty($photoFile) &&
                                        file_exists($photoPath)
                                    ): ?>

                                        <img
                                            src="<?= htmlspecialchars($photoPath); ?>"
                                            alt="Student Photo"
                                            class="student-photo flex-shrink-0"
                                        >

                                    <?php else: ?>

                                        <div
                                            class="student-photo-placeholder bg-secondary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                                        >
                                            <i
                                                class="bi bi-person-fill"
                                                style="font-size:52px;"
                                            ></i>
                                        </div>

                                    <?php endif; ?>

                                    <div>

                                        <div class="info-line">
                                            <strong>ID:</strong>
                                            <?= htmlspecialchars(
                                                $issue['student_id']
                                            ); ?>
                                        </div>

                                        <div class="info-line">
                                            <strong>Name:</strong>
                                            <?= htmlspecialchars(
                                                $issue['full_name']
                                            ); ?>
                                        </div>

                                        <div class="info-line">
                                            <strong>Email:</strong>
                                            <?= htmlspecialchars(
                                                $issue['email']
                                            ); ?>
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <!-- Transaction Information -->
                        <div class="col-md-5">

                            <div class="info-panel">

                                <h6 class="section-title">
                                    Transaction Information
                                </h6>

                                <div class="info-line">
                                    <strong>Transaction ID:</strong>
                                    #<?= htmlspecialchars(
                                        $issue['transaction_id']
                                    ); ?>
                                </div>

                                <div class="info-line">
                                    <strong>Issue Date:</strong>
                                    <?= date(
                                        'd M Y',
                                        strtotime($issue['issue_date'])
                                    ); ?>
                                </div>

                                <div class="info-line">
                                    <strong>Due Date:</strong>
                                    <?= date(
                                        'd M Y',
                                        strtotime($issue['due_date'])
                                    ); ?>
                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- Book Information -->
                    <h6 class="section-title">
                        Book Information
                    </h6>

                    <div class="table-responsive">

                        <table class="table table-bordered book-table align-middle mb-0">

                            <tr>
                                <th width="30%">Book Title</th>
                                <td>
                                    <?= htmlspecialchars(
                                        $issue['title']
                                    ); ?>
                                </td>
                            </tr>

                            <tr>
                                <th>ISBN</th>
                                <td>
                                    <?= htmlspecialchars(
                                        $issue['isbn']
                                    ); ?>
                                </td>
                            </tr>

                            <tr>
                                <th>Status</th>
                                <td>

                                    <?php if (
                                        $issue['status'] === 'Issued'
                                    ): ?>

                                        <span class="badge bg-success">
                                            Issued
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-secondary">
                                            <?= htmlspecialchars(
                                                $issue['status']
                                            ); ?>
                                        </span>

                                    <?php endif; ?>

                                </td>
                            </tr>

                            <tr>
                                <th>Fine</th>
                                <td>
                                    Rs.
                                    <?= number_format(
                                        (float)$issue['fine'],
                                        2
                                    ); ?>
                                </td>
                            </tr>

                        </table>

                    </div>


                    <!-- Actions -->
                    <div class="slip-actions d-flex justify-content-center gap-2">

                        <button
                            type="button"
                            class="btn btn-primary"
                            onclick="window.print();"
                        >
                            <i class="bi bi-printer me-1"></i>
                            Print Issue Slip
                        </button>

                        <a
                            href="issued_books.php"
                            class="btn btn-secondary"
                        >
                            <i class="bi bi-arrow-left me-1"></i>
                            Back
                        </a>

                    </div>

                </div>

            </div>

        </main>

    </div>

</div>

</body>

</html>
