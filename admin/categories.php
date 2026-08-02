<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

// =========================================
// Fetch Categories
// =========================================

$query = "
    SELECT
        c.category_id,
        c.category_name,
        COUNT(b.book_id) AS total_books
    FROM category c
    LEFT JOIN book b
        ON c.category_id = b.category_id
    GROUP BY
        c.category_id,
        c.category_name
    ORDER BY
        c.category_name ASC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Categories | Online Library System</title>

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

            <!-- Categories Page Content Starts Here -->
            <main class="main-content">

                <?php include "../includes/alert.php"; ?>

                <div class="container-fluid">

                    <!-- ===== Page Heading ===== -->
                    <div class="page-header d-flex align-item-center justify-content-between">
                        <div class="title">
                            <h3>Categories</h3>
                            <span>
                                <a href="dashboard.php">Home</a> /
                                <a href="categories.php">Categories</a>
                            </span>
                        </div>
                        <div class="btn">

                            <button id="addCategoryBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                                <i class="bi bi-plus-lg"></i>
                                Add New Category
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
                                        placeholder="Search Categories...">
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- ===== Categories Table ===== -->
                    <div class="table-card">

                        <div class="table-header">

                            <div class="d-flex align-item-center justify-content-between">
                                <h5 class="mb-0">
                                    <i class="bi bi-tags-fill"></i>
                                    Categories List
                                </h5>

                                <p class="text-dark">
                                    Total Categories : 150
                                </p>
                            </div>

                        </div>

                        <div class="table-responsive">

                            <table class="table table-hover table-bordered align-middle mb-0">

                                <thead class="table-dark">

                                    <tr>
                                        <th>ID</th>
                                        <th>Category Name</th>
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

                                            <td><?= htmlspecialchars($row["category_name"]) ?></td>

                                            <td><?= $row["total_books"] ?></td>

                                            <td class="text-center">

                                                <button
                                                    class="btn btn-warning btn-sm edit-category"
                                                    data-id="<?= $row["category_id"] ?>">

                                                    <i class="bi bi-pencil-square"></i>

                                                </button>

                                                <button
                                                    class="btn btn-danger btn-sm delete-category"
                                                    data-id="<?= $row["category_id"] ?>"
                                                    data-name="<?= htmlspecialchars($row["category_name"]) ?>">

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
                                Showing 1 to 10 of 150 Categories
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


    <!-- ===== Add Category Modal ===== -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h3 id="modalTitle" class="modal-title fw-bold">
                        Add New Category
                    </h3>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">

                    <!-- Form Here -->
                    <form id="categoryForm" action="save_category.php" method="POST">

                        <input
                            type="hidden"
                            id="categoryId"
                            name="category_id">

                        <div class="mb-3">

                            <label class="form-label">
                                Category Name
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="categoryName"
                                name="category_name"
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
                                id="saveCategoryBtn"
                                type="submit"
                                name="save_category"
                                class="btn btn-primary">

                                <i class="bi bi-floppy"></i>

                                Save Category

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
    <script src="../assets/js/categories.js"></script>

</body>

</html>