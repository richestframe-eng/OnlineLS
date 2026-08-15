<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

requireStudent();

$studentId = $_SESSION['student_id'];


// ==========================
// Fetch Student Profile
// ==========================

$stmt = $conn->prepare("
    SELECT
        student_id,
        full_name,
        address,
        phone,
        dob,
        program,
        semester,
        email,
        photo
    FROM student
    WHERE student_id = ?
");

$stmt->bind_param("i", $studentId);

$stmt->execute();

$result = $stmt->get_result();

$student = $result->fetch_assoc();


// Student should always exist if logged in
if (!$student) {
    session_destroy();

    header("Location: ../login.php");
    exit();
}


$pageTitle = 'My Profile';

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Profile - Online Library System</title>


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
        rel="stylesheet"
        href="../assets/css/style.css"
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

                    <h4>My Profile</h2>

                    <p>
                        View your personal information.
                    </p>

                </div>

            </div>


            <!-- ==========================
                 Profile Card
            ========================== -->

            <div class="card">

                <div class="card-body">


                    <div class="row g-4">


                        <!-- ==========================
                             Profile Photo
                        ========================== -->

                        <div class="col-md-4 text-center">

                            <?php

                            $photoFile = basename($student['photo'] ?? '');

                            $photoPath = '../assets/uploads/students/' . $photoFile;

                            ?>

                            <?php if (!empty($photoFile) && file_exists($photoPath)): ?>

                                <img
                                    src="<?= htmlspecialchars($photoPath); ?>"
                                    alt="Student Photo"
                                    width="150"
                                    height="180"
                                    style="object-fit: cover; border-radius: 8px;"
                                >

                            <?php else: ?>

                                <div
                                    class="d-flex align-items-center justify-content-center mx-auto bg-secondary text-white"
                                    style="
                                        width:150px;
                                        height:180px;
                                        border-radius:8px;
                                    "
                                >
                                    <i
                                        class="bi bi-person-fill"
                                        style="font-size:70px;"
                                    ></i>
                                </div>

                            <?php endif; ?>       
       
                            <h4 class="mt-3 mb-1">

                                <?= htmlspecialchars(
                                    $student['full_name']
                                ); ?>

                            </h4>


                            <p class="text-muted">

                                Student ID:
                                <?= htmlspecialchars(
                                    $student['student_id']
                                ); ?>

                            </p>


                        </div>


                        <!-- ==========================
                             Student Information
                        ========================== -->

                        <div class="col-md-8">


                            <div class="row g-3">


                                <!-- Full Name -->

                                <div class="col-md-6">

                                    <label class="form-label fw-bold">
                                        Full Name
                                    </label>

                                    <div class="form-control bg-light">

                                        <?= htmlspecialchars(
                                            $student['full_name']
                                        ); ?>

                                    </div>

                                </div>


                                <!-- Email -->

                                <div class="col-md-6">

                                    <label class="form-label fw-bold">
                                        Email
                                    </label>

                                    <div class="form-control bg-light">

                                        <?= htmlspecialchars(
                                            $student['email']
                                        ); ?>

                                    </div>

                                </div>


                                <!-- Phone -->

                                <div class="col-md-6">

                                    <label class="form-label fw-bold">
                                        Phone
                                    </label>

                                    <div class="form-control bg-light">

                                        <?= htmlspecialchars(
                                            $student['phone']
                                        ); ?>

                                    </div>

                                </div>


                                <!-- Date of Birth -->

                                <div class="col-md-6">

                                    <label class="form-label fw-bold">
                                        Date of Birth
                                    </label>

                                    <div class="form-control bg-light">

                                        <?php if (!empty($student['dob'])): ?>

                                            <?= date(
                                                'd M Y',
                                                strtotime($student['dob'])
                                            ); ?>

                                        <?php else: ?>

                                            -

                                        <?php endif; ?>

                                    </div>

                                </div>


                                <!-- Program -->

                                <div class="col-md-6">

                                    <label class="form-label fw-bold">
                                        Program
                                    </label>

                                    <div class="form-control bg-light">

                                        <?= !empty($student['program'])
                                            ? htmlspecialchars($student['program'])
                                            : '-'; ?>

                                    </div>

                                </div>


                                <!-- Semester -->

                                <div class="col-md-6">

                                    <label class="form-label fw-bold">
                                        Semester
                                    </label>

                                    <div class="form-control bg-light">

                                        <?= !empty($student['semester'])
                                            ? htmlspecialchars($student['semester'])
                                            : '-'; ?>

                                    </div>

                                </div>


                                <!-- Address -->

                                <div class="col-12">

                                    <label class="form-label fw-bold">
                                        Address
                                    </label>

                                    <div class="form-control bg-light">

                                        <?= !empty($student['address'])
                                            ? htmlspecialchars($student['address'])
                                            : '-'; ?>

                                    </div>

                                </div>


                            </div>

                        </div>

                    </div>

                </div>

            </div>


        </main>

    </div>

</div>


<!-- Bootstrap JS -->

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>


<!-- Main JS -->

<script src="../js/script.js"></script>


</body>

</html>