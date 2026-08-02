<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

$sql = "SELECT * FROM student ORDER BY full_name ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Students | Online Library System</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Admin CSS -->
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

    <div class="container-fluid m-0 p-0 d-flex">
        <!-- ===== Sidebar ===== -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- ==== Content ===== -->
        <div class="main-container flex-grow-1">

            <?php include '../includes/header.php'; ?>

            <!-- Books Page Content Starts Here -->
            <main class="main-content">
                <div class="container-fluid">

                    <!-- ===== Page Heading ===== -->
                    <div class="page-header d-flex align-item-center justify-content-between">
                        <div class="title">
                            <h3>Students Management</h3>
                            <span>
                                <a href="dashboard.php">Home</a> /
                                <a href="students.php">Students</a>
                            </span>
                        </div>
                        <div class="btn">

                            <button id="addStdBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStdModal">
                                <i class="bi bi-plus-lg"></i>
                                Add New Student
                            </button>

                        </div>
                    </div>

                    <!-- ===== Filter Card ===== -->
                    <div class="filter-card">

                        <div class="row g-3 align-items-end">

                            <div class="col-lg-5">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="form-control"
                                        placeholder="Search Students...">
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <select class="form-select">
                                    <option selected>All Categories</option>
                                    <option>Programming</option>
                                    <option>Database</option>
                                    <option>Networking</option>
                                </select>
                            </div>

                            <div class="col-lg-3">
                                <select class="form-select">
                                    <option selected>All Publishers</option>
                                    <option>McGraw Hill</option>
                                    <option>Pearson</option>
                                    <option>Oxford</option>
                                </select>
                            </div>

                            <div class="col-lg-1 d-grid">
                                <button class="btn btn-primary">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>
                            </div>

                        </div>

                    </div>

                    <!-- ===== Books Table ===== -->
                    <div class="table-card">

                        <div class="table-header">

                            <div class="d-flex align-item-center justify-content-between">
                                <h5 class="mb-0">
                                    <i class="bi bi-book-half"></i>
                                    Students List
                                </h5>

                                <p class="text-dark">
                                    Total Students : 150
                                </p>
                            </div>

                        </div>

                        <div class="table-responsive">

                            <table class="table table-hover table-bordered align-middle mb-0">

                                <thead class="table-dark">

                                    <tr>
                                        <th>S.N.</th>
                                        <th>Photo</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th class="text-center">Program</th>
                                        <th class="text-center">Action</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    <?php

                                    $query = "
    SELECT *
    FROM student
    ORDER BY full_name ASC
";

                                    $result = $conn->query($query);

                                    $sn = 1;

                                    while ($row = $result->fetch_assoc()) :

                                    ?>

                                        <tr>

                                            <td><?= $sn++ ?></td>

                                            <td>

                                                <img
                                                    src="../assets/uploads/<?= htmlspecialchars($row['photo']); ?>"
                                                    alt="Student Photo"
                                                    width="50"
                                                    height="60"
                                                    style="object-fit: cover; border-radius: 5px;">

                                            </td>

                                            <td><?= htmlspecialchars($row['full_name']); ?></td>

                                            <td><?= htmlspecialchars($row['email']); ?></td>

                                            <td><?= htmlspecialchars($row['phone']); ?></td>

                                            <td class="text-center">
                                                <?= htmlspecialchars($row['program']); ?>
                                            </td>

                                            <td class="text-center">

                                                <a
                                                    href="view_std.php?id=<?= $row['student_id']; ?>"
                                                    class="btn btn-info btn-sm">

                                                    <i class="bi bi-eye"></i>

                                                </a>

                                                <button
                                                    class="btn btn-warning btn-sm edit-student"
                                                    data-id="<?= $row['student_id']; ?>">

                                                    <i class="bi bi-pencil-square"></i>

                                                </button>

                                                <button
                                                    class="btn btn-danger btn-sm delete-std"
                                                    data-id="<?= $row['student_id']; ?>">

                                                    <i class="bi bi-trash"></i>

                                                </button>

                                            </td>

                                        </tr>

                                    <?php endwhile; ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                    <!-- ===== Pagination ===== -->
                    <div class="pagination-card mt-3">

                        <div class="d-flex justify-content-between align-items-center flex-wrap">

                            <small class="text-muted">
                                Showing 1 to 10 of 150 Students
                            </small>

                            <nav>

                                <ul class="pagination mb-0">

                                    <li class="page-item disabled">
                                        <a class="page-link" href="#">
                                            Previous
                                        </a>
                                    </li>

                                    <li class="page-item active">
                                        <a class="page-link" href="#">
                                            1
                                        </a>
                                    </li>

                                    <li class="page-item">
                                        <a class="page-link" href="#">
                                            2
                                        </a>
                                    </li>

                                    <li class="page-item">
                                        <a class="page-link" href="#">
                                            3
                                        </a>
                                    </li>

                                    <li class="page-item">
                                        <a class="page-link" href="#">
                                            Next
                                        </a>
                                    </li>

                                </ul>

                            </nav>

                        </div>

                    </div>
                </div>
            </main>

        </div>
    </div>


    <!-- ===== Add Student Modal ===== -->
    <div class="modal fade" id="addStdModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h3 id="modalTitle" class="modal-title fw-bold">
                        Add New Student
                    </h3>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">

                    <!-- Form goes here -->
                    <form id="studentForm" action="save_std.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="studentId" name="student_id">

                        <div class="row">

                            <!-- ===== Left Side ===== -->
                            <div class="col-md-3 text-center">

                                <label class="form-label fw-semibold mb-2">
                                    Passport Size Photo
                                </label>

                                <div
                                    id="photoContainer"
                                    class="border rounded d-flex justify-content-center align-items-center mx-auto"
                                    style=" width:150px; height:193px; cursor:pointer; overflow:hidden; ">

                                    <i
                                        id="photoIcon"
                                        class="bi bi-person-bounding-box text-secondary"
                                        style="font-size: 50px;">
                                    </i>

                                    <img
                                        id="photoPreview"
                                        src=""
                                        class="w-100 h-100 d-none"
                                        style="object-fit:cover;">

                                </div>

                                <p class="text-muted small mb-2">
                                    Click the box to upload
                                </p>

                                <!-- Hidden File Input -->
                                <input
                                    type="file"
                                    id="studentPhoto"
                                    name="photo"
                                    accept=".jpg,.jpeg,.png"
                                    hidden>

                            </div>

                            <!-- ===== Right Side ===== -->
                            <div class="col-md-9">

                                <div class="row">

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">
                                            Full Name
                                        </label>

                                        <input
                                            type="text"
                                            class="form-control"
                                            id="fullName"
                                            name="full_name"
                                            required>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">
                                            Email
                                        </label>

                                        <input
                                            type="email"
                                            class="form-control"
                                            id="email"
                                            name="email"
                                            required>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label">
                                            Phone
                                        </label>

                                        <input
                                            type="text"
                                            class="form-control"
                                            id="phone"
                                            name="phone"
                                            required>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label for="dob" class="form-label">
                                            Date of Birth
                                        </label>

                                        <input
                                            type="date"
                                            class="form-control"
                                            id="dob"
                                            name="dob"
                                            required>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label for="program" class="form-label">
                                            Program
                                        </label>

                                        <select
                                            class="form-select"
                                            id="program"
                                            name="program"
                                            required>

                                            <option value="" selected disabled>
                                                Select Program
                                            </option>

                                            <option value="BA">BA</option>
                                            <option value="B.Com">B.Com</option>
                                            <option value="BCA">BCA</option>
                                            <option value="BBS">BBS</option>
                                            <option value="BBA">BBA</option>
                                            <option value="BIM">BIM</option>
                                            <option value="BIT">BIT</option>
                                            <option value="BHM">BHM</option>
                                            <option value="BSc.CSIT">BSc.CSIT</option>
                                            <option value="B.Ed">B.Ed</option>
                                            <option value="BSW">BSW</option>
                                            <option value="LLB">LLB</option>

                                        </select>

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label for="semester" class="form-label">
                                            Semester
                                        </label>

                                        <select
                                            class="form-select"
                                            id="semester"
                                            name="semester"
                                            required>

                                            <option value="" selected disabled>
                                                Select Semester
                                            </option>

                                            <option value="1">1st Semester</option>
                                            <option value="2">2nd Semester</option>
                                            <option value="3">3rd Semester</option>
                                            <option value="4">4th Semester</option>
                                            <option value="5">5th Semester</option>
                                            <option value="6">6th Semester</option>
                                            <option value="7">7th Semester</option>
                                            <option value="8">8th Semester</option>

                                        </select>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <!-- ===== Address ===== -->

                        <div class="mb-3">

                            <label class="form-label">
                                Address
                            </label>

                            <textarea
                                class="form-control"
                                id="address"
                                name="address"
                                rows="3"
                                required></textarea>

                        </div>

                        <!-- ===== Password Row ===== -->
                        <div class="row">

                            <div class="col-md-6 mb-0">

                                <label class="form-label">
                                    Password
                                </label>

                                <input
                                    type="password"
                                    class="form-control"
                                    id="password"
                                    name="password"
                                    required>

                            </div>

                            <div class="col-md-6 mb-0">

                                <label class="form-label">
                                    Confirm Password
                                </label>

                                <input
                                    type="password"
                                    class="form-control"
                                    id="confirmPassword"
                                    name="confirm_password"
                                    required>

                            </div>

                        </div>

                </div>

                <!-- ===== Buttons ===== -->
                <div class="modal-footer border-0 pb-4">

                    <button
                        type="button"
                        class="btn btn-danger px-4"
                        data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button
                        id="saveStdBtn"
                        type="submit"
                        name="save_std"
                        class="btn btn-primary px-3">

                        <i class="bi bi-floppy"></i>
                        Save Student

                    </button>

                </div>

                </form>

            </div>

        </div>

    </div>

    <!-- ==== Bootstrap JS ==== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ==== JavaScript ==== -->
    <script src="../assets/js/students.js"></script>

</body>

</html>