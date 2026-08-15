<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

requireAdmin();


// ==========================
// Get Transaction ID
// ==========================

if (!isset($_GET['transaction_id']) || !is_numeric($_GET['transaction_id'])) {
    header("Location: issue.php");
    exit();
}

$transactionId = (int) $_GET['transaction_id'];


// ==========================
// Fetch Issue Details
// ==========================

$stmt = $conn->prepare("
    SELECT
        ir.transaction_id,
        ir.issue_date,
        ir.due_date,
        ir.status,

        s.student_id,
        s.full_name,
        s.phone,
        s.program,
        s.semester,
        s.photo,

        b.book_id,
        b.title,
        b.isbn

    FROM issue_return ir

    INNER JOIN student s
        ON ir.student_id = s.student_id

    INNER JOIN book b
        ON ir.book_id = b.book_id

    WHERE ir.transaction_id = ?
");

$stmt->bind_param("i", $transactionId);

$stmt->execute();

$result = $stmt->get_result();

$issue = $result->fetch_assoc();


// ==========================
// Check Transaction
// ==========================

if (!$issue) {
    echo "Issue transaction not found.";
    exit();
}


// ==========================
// Student Photo
// ==========================

$photoPath = '';

if (!empty($issue['photo'])) {

    $photoPath = '../' . ltrim($issue['photo'], '/\\');

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Issue Slip #<?= $issue['transaction_id']; ?>
        - Online Library System
    </title>


    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >


    <style>

        body {
            background: #f5f7fb;
            font-family: Arial, sans-serif;
        }


        .slip-container {
            max-width: 850px;
            margin: 40px auto;
        }


        .slip {
            background: #ffffff;
            border: 1px solid #dcdcdc;
            border-radius: 8px;
            padding: 35px;
        }


        .slip-header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }


        .slip-header h2 {
            margin: 0;
            font-weight: 700;
        }


        .slip-header p {
            margin: 5px 0 0;
            color: #666;
        }


        .slip-title {
            text-align: center;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 25px;
            text-transform: uppercase;
        }


        .student-photo {
            width: 120px;
            height: 145px;
            object-fit: cover;
            border: 1px solid #ccc;
            border-radius: 5px;
        }


        .photo-placeholder {
            width: 120px;
            height: 145px;
            border: 1px solid #ccc;
            border-radius: 5px;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #f1f1f1;
            color: #777;
        }


        .info-label {
            font-weight: 600;
            color: #555;
        }


        .info-value {
            font-weight: 500;
        }


        .book-section {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 20px;
            margin-top: 25px;
        }


        .status {
            display: inline-block;
            background: #198754;
            color: white;
            padding: 5px 14px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: 600;
        }


        .signature-section {
            margin-top: 55px;
        }


        .signature-line {
            border-top: 1px solid #333;
            width: 220px;
            margin-top: 45px;
            padding-top: 8px;
            text-align: center;
        }


        .print-btn {
            margin-bottom: 20px;
        }


        @media print {

            body {
                background: #ffffff;
            }

            .print-btn {
                display: none;
            }

            .slip-container {
                margin: 0;
                max-width: none;
            }

            .slip {
                border: none;
                border-radius: 0;
            }

        }

    </style>

</head>


<body>


<div class="slip-container">


    <!-- Print Button -->

    <div class="text-end print-btn">

        <button
            type="button"
            class="btn btn-primary"
            onclick="window.print()"
        >

            <i class="bi bi-printer"></i>

            Print Issue Slip

        </button>

        <a
            href="search.php"
            class="btn btn-secondary"
        >

            <i class="bi bi-arrow-left"></i>

            Back

        </a>

    </div>


    <!-- ==========================
         Issue Slip
    =========================== -->

    <div class="slip">


        <!-- Header -->

        <div class="slip-header">

            <h2>Online Library System</h2>

            <p>
                Book Issue Confirmation
            </p>

        </div>


        <div class="slip-title">

            Book Issue Slip

        </div>


        <!-- ==========================
             Student Information
        =========================== -->

        <div class="row">


            <!-- Photo -->

            <div class="col-md-3 text-center">


                <?php if (
                    !empty($photoPath) &&
                    file_exists($photoPath)
                ): ?>

                    <img
                        src="<?= htmlspecialchars($photoPath); ?>"
                        alt="Student Photo"
                        class="student-photo"
                    >

                <?php else: ?>

                    <div class="photo-placeholder">

                        <i
                            class="bi bi-person-fill"
                            style="font-size: 55px;"
                        ></i>

                    </div>

                <?php endif; ?>


                <div class="mt-2">

                    <strong>
                        Student Photo
                    </strong>

                </div>

            </div>


            <!-- Student Details -->

            <div class="col-md-9">

                <div class="row g-3">


                    <div class="col-md-6">

                        <span class="info-label">
                            Transaction ID:
                        </span>

                        <span class="info-value">
                            #<?= $issue['transaction_id']; ?>
                        </span>

                    </div>


                    <div class="col-md-6">

                        <span class="info-label">
                            Student ID:
                        </span>

                        <span class="info-value">
                            <?= htmlspecialchars(
                                $issue['student_id']
                            ); ?>
                        </span>

                    </div>


                    <div class="col-md-6">

                        <span class="info-label">
                            Student Name:
                        </span>

                        <span class="info-value">
                            <?= htmlspecialchars(
                                $issue['full_name']
                            ); ?>
                        </span>

                    </div>


                    <div class="col-md-6">

                        <span class="info-label">
                            Phone:
                        </span>

                        <span class="info-value">
                            <?= htmlspecialchars(
                                $issue['phone']
                            ); ?>
                        </span>

                    </div>


                    <div class="col-md-6">

                        <span class="info-label">
                            Program:
                        </span>

                        <span class="info-value">
                            <?= !empty($issue['program'])
                                ? htmlspecialchars($issue['program'])
                                : '-'; ?>
                        </span>

                    </div>


                    <div class="col-md-6">

                        <span class="info-label">
                            Semester:
                        </span>

                        <span class="info-value">
                            <?= !empty($issue['semester'])
                                ? htmlspecialchars($issue['semester'])
                                : '-'; ?>
                        </span>

                    </div>


                </div>

            </div>

        </div>


        <!-- ==========================
             Book Information
        =========================== -->

        <div class="book-section">


            <h5 class="mb-3">

                <i class="bi bi-book me-2"></i>

                Book Information

            </h5>


            <div class="row g-3">


                <div class="col-md-8">

                    <span class="info-label">
                        Book Title:
                    </span>

                    <span class="info-value">
                        <?= htmlspecialchars(
                            $issue['title']
                        ); ?>
                    </span>

                </div>


                <div class="col-md-4">

                    <span class="info-label">
                        Book ID:
                    </span>

                    <span class="info-value">
                        <?= htmlspecialchars(
                            $issue['book_id']
                        ); ?>
                    </span>

                </div>


                <div class="col-md-6">

                    <span class="info-label">
                        ISBN:
                    </span>

                    <span class="info-value">
                        <?= htmlspecialchars(
                            $issue['isbn']
                        ); ?>
                    </span>

                </div>


                <div class="col-md-6">

                    <span class="info-label">
                        Status:
                    </span>

                    <span class="status">
                        <?= htmlspecialchars(
                            $issue['status']
                        ); ?>
                    </span>

                </div>


                <div class="col-md-6">

                    <span class="info-label">
                        Issue Date:
                    </span>

                    <span class="info-value">
                        <?= date(
                            'd M Y',
                            strtotime($issue['issue_date'])
                        ); ?>
                    </span>

                </div>


                <div class="col-md-6">

                    <span class="info-label">
                        Due Date:
                    </span>

                    <span class="info-value">
                        <?= date(
                            'd M Y',
                            strtotime($issue['due_date'])
                        ); ?>
                    </span>

                </div>


            </div>

        </div>


        <!-- ==========================
             Signatures
        =========================== -->

        <div class="row signature-section">


            <div class="col-md-6">

                <div class="signature-line">

                    Student Signature

                </div>

            </div>


            <div class="col-md-6 text-end">

                <div
                    class="signature-line ms-auto"
                >

                    Librarian / Admin

                </div>

            </div>


        </div>


        <!-- Footer -->

        <div class="text-center mt-5 text-muted">

            <small>

                This slip confirms the issue of the above-mentioned
                book to the student.

            </small>

        </div>


    </div>

</div>


</body>

</html>