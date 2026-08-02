<?php

session_start();
require_once "../includes/db.php";

if (!isset($_GET["id"])) {

    header("Location: students.php");
    exit();
}

$student_id = intval($_GET["id"]);

$stmt = $conn->prepare("
    SELECT *
    FROM student
    WHERE student_id = ?
");

$stmt->bind_param("i", $student_id);
$stmt->execute();

$student = $stmt->get_result()->fetch_assoc();

$stmt->close();

if (!$student) {

    $_SESSION["error"] = "Student not found.";

    header("Location: students.php");
    exit();
}

$dob = new DateTime($student["dob"]);
$today = new DateTime();

$age = $today->diff($dob)->y;

$sem = $student["semester"];

switch ($sem) {

    case 1:
        $semesterText = "1st Semester";
        break;
    case 2:
        $semesterText = "2nd Semester";
        break;
    case 3:
        $semesterText = "3rd Semester";
        break;
    default:
        $semesterText = $sem . "th Semester";
}

// Total Issued

$totalIssued = $conn->prepare("
SELECT COUNT(*)
FROM issue_return
WHERE student_id=?
");

$totalIssued->bind_param("i", $student_id);
$totalIssued->execute();
$totalIssued->bind_result($issuedCount);
$totalIssued->fetch();
$totalIssued->close();

// Total Returned

$totalReturned = $conn->prepare("
SELECT COUNT(*)
FROM issue_return
WHERE student_id=?
AND status='Returned'
");

$totalReturned->bind_param("i", $student_id);
$totalReturned->execute();
$totalReturned->bind_result($returnedCount);
$totalReturned->fetch();
$totalReturned->close();

// Current Issued

$currentIssued = $conn->prepare("
SELECT COUNT(*)
FROM issue_return
WHERE student_id=?
AND status='Issued'
");

$currentIssued->bind_param("i", $student_id);
$currentIssued->execute();
$currentIssued->bind_result($currentBook);
$currentIssued->fetch();
$currentIssued->close();

// Total Fine

$fineQuery = $conn->prepare("
SELECT SUM(fine)
FROM issue_return
WHERE student_id=?
");

$fineQuery->bind_param("i", $student_id);
$fineQuery->execute();
$fineQuery->bind_result($fine);

$fineQuery->fetch();

$fineQuery->close();

$fine = $fine ?? 0;

$history = $conn->prepare("
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

$history->bind_param("i", $student_id);
$history->execute();

$historyResult = $history->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student's Details</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Internal CSS -->
    <style>
        .table th {
            width: 180px;
        }
    </style>
</head>

<body>

    <div class="container">

        <div class="d-flex align-items-center my-4">
            <a href="students.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
                Back
            </a>

            <h3 class="fw-bold mx-auto mb-0">
                <i class="bi bi-person-badge-fill text-primary"></i>
                Student Profile
            </h3>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 d-flex flex-column justify-content-center align-items-center">
                                <img
                                    src="../assets/uploads/<?= htmlspecialchars($student["photo"]); ?>"
                                    onerror="this.src='../assets/images/default-user.png';"
                                    class="img-thumbnail rounded-3"
                                    style="
                                        width:140px;
                                        height:210px;
                                        object-fit:cover;
                                ">

                                <h5 class="mt-3 text-center fw-bold">
                                    <?= htmlspecialchars($student["full_name"]); ?>
                                </h5>
                            </div>

                            <div class="col-md-9">
                                <table class="table table-borderless">
                                    <tr>
                                        <th>Full Name</th>
                                        <td><?= htmlspecialchars($student["full_name"]); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Program</th>
                                        <td><?= htmlspecialchars($student["program"]); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Semester</th>
                                        <td><?= $semesterText ?></td>
                                    </tr>

                                    <tr>
                                        <th>DOB</th>
                                        <td><?= date("d M Y", strtotime($student["dob"])) ?></td>
                                    </tr>

                                    <tr>
                                        <th>Age</th>
                                        <td>
                                            <span class="badge bg-primary fs-6">
                                                <?= $age ?> Years
                                            </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Email</th>
                                        <td><?= htmlspecialchars($student["email"]); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Phone</th>
                                        <td><?= htmlspecialchars($student["phone"]); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Address</th>
                                        <td><?= htmlspecialchars($student["address"]); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">

            <!-- Total Issued -->

            <div class="col-md-3">

                <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <h6>Books Issued</h6>

                                <h2><?= $issuedCount ?></h2>

                            </div>

                            <i class="bi bi-journal-bookmark-fill fs-1"></i>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Total Returned -->

            <div class="col-md-3">

                <div class="card border-0 shadow-sm rounded-4 bg-success text-white">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <h6>Books Returned</h6>

                                <h2><?= $returnedCount ?></h2>

                            </div>

                            <i class="bi bi-check-circle-fill fs-1"></i>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Current Issued -->

            <div class="col-md-3">

                <div class="card border-0 shadow-sm rounded-4 bg-warning text-dark">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <h6>Current Borrowed</h6>

                                <h2><?= $currentBook ?></h2>

                            </div>

                            <i class="bi bi-book-half fs-1"></i>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Total Fine -->

            <div class="col-md-3">

                <div class="card border-0 shadow-sm rounded-4 bg-danger text-white">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <h6>Outstanding Fine</h6>

                                <h2>
                                    <?php
                                    if ($fine == 0) {
                                        echo "No Fine";
                                    } else {
                                        echo "Rs. " . number_format($fine, 2);
                                    }

                                    ?>
                                </h2>

                            </div>

                            <i class="bi bi-cash-stack fs-1"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="card shadow-sm border-0 rounded-4 my-4">

            <div class="card-header bg-white">

                <h5 class="mb-0">
                    <i class="bi bi-clock-history text-primary"></i>
                    Borrowing History
                </h5>

            </div>

            <div class="card-body">

                <table class="table table-hover align-middle">

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

                        <!-- PHP -->
                        <?php

                        if ($historyResult->num_rows > 0) {
                            $sn = 1;
                            while ($row = $historyResult->fetch_assoc()) {

                        ?>

                                <tr>

                                    <td><?= $sn++ ?></td>

                                    <td><?= htmlspecialchars($row["title"]) ?></td>
                                    <td><?= date("d M Y", strtotime($row["issue_date"])) ?></td>
                                    <td><?= date("d M Y", strtotime($row["due_date"])) ?></td>
                                    <td>
                                        <?php
                                        if (!empty($row["return_date"])) {
                                            echo date("d M Y", strtotime($row["return_date"]));
                                        } else {
                                            echo "-";
                                        }
                                        ?>
                                    </td>

                                    <td>
                                        <?php
                                        if ($row["status"] == "Issued") {
                                            echo '<span class="badge bg-warning text-dark">Issued</span>';
                                        } else {
                                            echo '<span class="badge bg-success">Returned</span>';
                                        }
                                        ?>
                                    </td>

                                    <td>
                                        Rs. <?= number_format($row["fine"], 2) ?>
                                    </td>

                                </tr>
                            <?php
                            }
                        } else {
                            ?>

                            <tr>

                                <td colspan="7" class="text-center py-5">
                                    <i class="bi bi-journal-x display-6 text-secondary"></i>
                                    <br>
                                    <span class="text-muted">
                                        No borrowing history found.
                                    </span>
                                </td>

                            </tr>

                        <?php
                        }
                        ?>

                    </tbody>

                </table>

            </div>

        </div>
    </div>

    <!-- ==== Bootstrap JS ==== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>