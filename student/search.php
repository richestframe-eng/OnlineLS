<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

requireStudent();

$search = trim($_GET['search'] ?? '');


// ==========================
// Fetch Books
// ==========================

if ($search !== '') {

    $searchTerm = "%{$search}%";

    $stmt = $conn->prepare("
        SELECT
            b.book_id,
            b.title,
            b.isbn,
            b.publication_year,
            b.available,
            b.total,
            a.author_name,
            c.category_name
        FROM book b

        LEFT JOIN author a
            ON b.author_id = a.author_id

        LEFT JOIN category c
            ON b.category_id = c.category_id

        WHERE
            b.title LIKE ?
            OR a.author_name LIKE ?
            OR c.category_name LIKE ?
            OR b.isbn LIKE ?

        ORDER BY b.title ASC
    ");

    $stmt->bind_param(
        "ssss",
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm
    );

    $stmt->execute();

    $books = $stmt->get_result();
} else {

    // Show all books when search box is empty

    $books = $conn->query("
        SELECT
            b.book_id,
            b.title,
            b.isbn,
            b.publication_year,
            b.available,
            b.total,
            a.author_name,
            c.category_name
        FROM book b

        LEFT JOIN author a
            ON b.author_id = a.author_id

        LEFT JOIN category c
            ON b.category_id = c.category_id

        ORDER BY b.title ASC
    ");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Search Books | Online Library System</title>


    <!-- Bootstrap CSS -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">


    <!-- Bootstrap Icons -->

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">


    <!-- Common CSS -->

    <link
        rel="stylesheet"
        href="../assets/css/style.css">

</head>


<body>


    <div class="container-fluid m-0 p-0 d-flex">


        <!-- ==========================
         Sidebar
    =========================== -->

        <?php include '../includes/sidebar.php'; ?>


        <!-- ==========================
         Main Container
    =========================== -->

        <div class="main-container flex-grow-1">


            <?php
            $pageTitle = 'Search Books';
            include '../includes/header.php';
            ?>


            <!-- ==========================
             Main Content
        =========================== -->

            <main class="main-content">

                <?php include "../includes/alert.php"; ?>


                <div class="container-fluid">

                    <!-- ==========================
                     Search Form
                =========================== -->

                    <div class="content-card mb-4">

                        <div class="card-header">

                            <h5>

                                <i class="bi bi-search"></i>
                                Search a Book
                            </h5>

                        </div>


                        <div class="pt-2">

                            <form
                                method="GET"
                                action="search.php">

                                <div class="row g-3">


                                    <div class="col-md-10">

                                        <input
                                            type="text"
                                            name="search"
                                            class="form-control"
                                            placeholder="Enter book title, author, category or ISBN"
                                            value="<?= htmlspecialchars($search); ?>">

                                    </div>


                                    <div class="col-md-2 d-flex align-items-end gap-2">

                                        <button
                                            type="submit"
                                            class="btn btn-primary flex-grow-1">
                                            <i class="bi bi-search me-1"></i>
                                            Search
                                        </button>

                                        <?php if ($search !== ''): ?>

                                            <a
                                                href="search.php"
                                                class="btn btn-secondary"
                                                title="Reset Search">
                                                <i class="bi bi-arrow-clockwise"></i>
                                            </a>

                                        <?php endif; ?>

                                    </div>

                                </div>

                            </form>

                        </div>

                    </div>

                    <?php if (isset($_GET['success']) && $_GET['success'] === 'requested'): ?>

                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-1"></i>
                            Book request submitted successfully.
                        </div>

                    <?php elseif (isset($_GET['error'])): ?>

                        <?php if ($_GET['error'] === 'already_requested'): ?>

                            <div class="alert alert-warning">
                                You have already requested this book.
                            </div>

                        <?php elseif ($_GET['error'] === 'unavailable'): ?>

                            <div class="alert alert-danger">
                                This book is currently unavailable.
                            </div>

                        <?php elseif ($_GET['error'] === 'request_failed'): ?>

                            <div class="alert alert-danger">
                                Failed to submit book request.
                            </div>

                        <?php endif; ?>

                    <?php endif; ?>


                    <!-- ==========================
                     Search Results
                =========================== -->

                    <div class="content-card">

                        <div class="card-header">

                            <h5>

                                <i class="bi bi-book-half"></i>

                                Book List

                                <span class="text-muted">
                                    (<?= $books->num_rows; ?>)
                                </span>

                            </h5>

                        </div>


                        <div class="table-responsive">

                            <table class="table table-hover table-bordered align-middle mb-0">

                                <thead class="table-light">

                                    <tr>
                                        <th>#</th>
                                        <th>Book Title</th>
                                        <th>Author</th>
                                        <th>Category</th>
                                        <th>ISBN</th>
                                        <th>Publication Year</th>
                                        <th>Availability</th>
                                        <th class="text-center">Action</th>
                                    </tr>

                                </thead>


                                <tbody>

                                    <?php

                                    $sn = 1;

                                    if ($books->num_rows > 0):

                                        while ($row = $books->fetch_assoc()):

                                    ?>

                                    <?php
                                        $requestStmt = $conn->prepare("
                                            SELECT request_id
                                            FROM request
                                            WHERE student_id = ?
                                            AND book_id = ?
                                            AND status = 'Pending'
                                            LIMIT 1
                                        ");

                                        $requestStmt->bind_param(
                                            "ii",
                                            $_SESSION['student_id'],
                                            $row['book_id']
                                        );

                                        $requestStmt->execute();

                                        $requestResult = $requestStmt->get_result();

                                        $pendingRequest = $requestResult->num_rows > 0;
                                    ?>

                                            <tr>


                                                <!-- Serial Number -->

                                                <td>
                                                    <?= $sn++; ?>
                                                </td>


                                                <!-- Book Title -->

                                                <td>

                                                    <strong>
                                                        <?= htmlspecialchars($row['title']); ?>
                                                    </strong>

                                                </td>


                                                <!-- Author -->

                                                <td>

                                                    <?= htmlspecialchars(
                                                        $row['author_name'] ?? 'Unknown'
                                                    ); ?>

                                                </td>


                                                <!-- Category -->

                                                <td>

                                                    <?= htmlspecialchars(
                                                        $row['category_name'] ?? 'Uncategorized'
                                                    ); ?>

                                                </td>


                                                <!-- ISBN -->

                                                <td>

                                                    <?= htmlspecialchars(
                                                        $row['isbn']
                                                    ); ?>

                                                </td>


                                                <!-- Publication Year -->

                                                <td>

                                                    <?= htmlspecialchars(
                                                        $row['publication_year']
                                                    ); ?>

                                                </td>


                                                <!-- Availability -->

                                                <td>

                                                    <?php

                                                    if ($row['available'] > 0):

                                                    ?>

                                                        <span class="badge bg-success">

                                                            <i class="bi bi-check-circle me-1"></i>

                                                            Available
                                                            (<?= $row['available']; ?>)

                                                        </span>

                                                    <?php else: ?>

                                                        <span class="badge bg-danger">

                                                            <i class="bi bi-x-circle me-1"></i>

                                                            Not Available

                                                        </span>

                                                    <?php endif; ?>

                                                </td>

                                                <!-- Request Book -->

                                                <td>
                                                    <?php if ($row['available'] > 0): ?>

                                                        <?php if ($pendingRequest): ?>

                                                            <button
                                                                type="button"
                                                                class="btn btn-warning btn-sm"
                                                                disabled
                                                            >
                                                                <i class="bi bi-clock me-1"></i>
                                                                Requested
                                                            </button>

                                                        <?php else: ?>

                                                            <a
                                                                href="request_book.php?book_id=<?= $row['book_id']; ?>"
                                                                class="btn btn-primary btn-sm"
                                                                onclick="return confirm('Are you sure you want to request this book?');"
                                                            >
                                                                <i class="bi bi-bookmark-plus me-1"></i>
                                                                Request Book
                                                            </a>

                                                        <?php endif; ?>

                                                    <?php else: ?>

                                                        <button
                                                            type="button"
                                                            class="btn btn-secondary btn-sm"
                                                            disabled
                                                        >
                                                            Unavailable
                                                        </button>

                                                    <?php endif; ?>
                                                </td>
                                            </tr>

                                        <?php

                                        endwhile;

                                    else:

                                        ?>

                                        <tr>

                                            <td
                                                colspan="7"
                                                class="text-center text-muted py-4">

                                                <i class="bi bi-search fs-3 d-block mb-2"></i>

                                                <?php if (!empty($search)): ?>

                                                    No books found for
                                                    "<strong>
                                                        <?= htmlspecialchars($search); ?>
                                                    </strong>".

                                                <?php else: ?>

                                                    No books available.

                                                <?php endif; ?>

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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Common JavaScript -->
    <script src="../assets/js/script.js"></script>

</body>

</html>