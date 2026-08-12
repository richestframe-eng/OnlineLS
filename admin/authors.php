<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

// =========================================
// Fetch Authors
// =========================================

$query = "
    SELECT
        a.author_id,
        a.author_name,
        COUNT(b.book_id) AS total_books
    FROM author a
    LEFT JOIN book b
        ON a.author_id = b.author_id
    GROUP BY
        a.author_id,
        a.author_name
    ORDER BY
        a.author_name ASC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Authors | Online Library System</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Admin CSS -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="container-fluid m-0 p-0 d-flex">
        <!-- ===== Sidebar ===== -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- ==== Content ===== -->
        <div class="main-container flex-grow-1">

            <?php include '../includes/header.php'; ?>

            <!-- Authors Page Content Starts Here -->
            <main class="main-content">

                <?php include "../includes/alert.php"; ?>

                <div class="container-fluid">

                    <!-- ===== Page Heading ===== -->
                    <div class="page-header d-flex align-item-center justify-content-between">
                        <div class="title">
                            <h3>Authors</h3>
                            <span>
                                <a href="dashboard.php">Home</a> /
                                <a href="authors.php">Authors</a>
                            </span>
                        </div>
                        <div class="btn">

                            <button id="addAuthorBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAuthorModal">
                                <i class="bi bi-plus-lg"></i>
                                Add New Author
                            </button>

                        </div>
                    </div>

                    <!-- ===== Filter Card ===== -->
                    <div class="filter-card">

                        <div class="row g-3 align-items-end">

                            <div class="col-lg-12">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input
                                        type="text"
                                        class="form-control"
                                        placeholder="Search Authors...">
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- ===== Authors Table ===== -->
                    <div class="table-card">

                        <div class="table-header">

                            <div class="d-flex align-item-center justify-content-between">
                                <h5 class="mb-0">
                                    <i class="bi bi-pen-fill"></i>
                                    Authors List
                                </h5>

                                <p class="text-dark">
                                    Total Authors : 150
                                </p>
                            </div>

                        </div>

                        <div class="table-responsive">

                            <table class="table table-hover table-bordered align-middle mb-0">

                                <thead class="table-dark">

                                    <tr>
                                        <th>ID</th>
                                        <th>Author Name</th>
                                        <th>Total Books</th>
                                        <th class="text-center">Action</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    <?php

                                    $sn = 1;

                                    while ($row = $result->fetch_assoc()) :

                                    ?>

                                        <tr>

                                            <td><?= $sn++ ?></td>

                                            <td><?= htmlspecialchars($row["author_name"]) ?></td>

                                            <td><?= $row["total_books"] ?></td>

                                            <td class="text-center">

                                                <button
                                                    class="btn btn-warning btn-sm edit-author"
                                                    data-id="<?= $row["author_id"] ?>">

                                                    <i class="bi bi-pencil-square"></i>

                                                </button>

                                                <button
                                                    class="btn btn-danger btn-sm delete-author"
                                                    data-id="<?= $row["author_id"] ?>"
                                                    data-name="<?= htmlspecialchars($row["author_name"]) ?>">

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
                                Showing 1 to 10 of 150 Authors
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


    <!-- ===== Add Author Modal ===== -->
    <div class="modal fade" id="addAuthorModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h3 id="modalTitle" class="modal-title fw-bold">
                        Add New Author
                    </h3>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">

                    <!-- Form Here -->
                    <form id="authorForm" action="save_author.php" method="POST">

                        <input
                            type="hidden"
                            id="authorId"
                            name="author_id">

                        <div class="mb-3">

                            <label class="form-label">
                                Author Name
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="authorName"
                                name="author_name"
                                required>

                        </div>

                        <div class="modal-footer">

                            <button
                                type="button"
                                class="btn btn-danger"
                                data-bs-dismiss="modal">

                                Cancel

                            </button>

                            <button
                                id="saveAuthorBtn"
                                type="submit"
                                name="save_author"
                                class="btn btn-primary">

                                <i class="bi bi-floppy"></i>

                                Save Author

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

    <!-- ==== Bootstrap JS ==== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ==== JavaScript ==== -->
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/authors.js"></script>

</body>

</html>