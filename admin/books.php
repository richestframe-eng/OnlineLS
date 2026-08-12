<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../includes/db.php';

// =========================================
// Search & Filters
// =========================================
$search    = trim($_GET['search'] ?? '');
$category  = $_GET['category'] ?? '';
$publisher = $_GET['publisher'] ?? '';

// =========================================
// Pagination
// =========================================
$limit = 10;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$start = ($page - 1) * $limit;

// =========================================
// Main Query
// =========================================
$sql = "
SELECT
    b.book_id,
    b.title,
    b.isbn,
    b.available,
    a.author_name,
    p.publisher_name,
    c.category_name
FROM book b
INNER JOIN author a
    ON b.author_id = a.author_id
INNER JOIN publisher p
    ON b.publisher_id = p.publisher_id
INNER JOIN category c
    ON b.category_id = c.category_id
WHERE 1
";

$params = [];
$types  = "";

// =========================================
// Search
// =========================================
if (!empty($search)) {

    $sql .= "
        AND
        (
            b.title LIKE ?
            OR b.isbn LIKE ?
            OR a.author_name LIKE ?
        )
    ";

    $keyword = "%$search%";

    $params[] = $keyword;
    $params[] = $keyword;
    $params[] = $keyword;

    $types .= "sss";
}

// =========================================
// Category
// =========================================
if (!empty($category)) {

    $sql .= " AND b.category_id = ?";

    $params[] = $category;

    $types .= "i";
}

// =========================================
// Publisher
// =========================================
if (!empty($publisher)) {

    $sql .= " AND b.publisher_id = ?";

    $params[] = $publisher;

    $types .= "i";
}

// =========================================
// Count Query
// =========================================
$countSql = "
SELECT COUNT(*) AS total
FROM book b
INNER JOIN author a
    ON b.author_id = a.author_id
INNER JOIN publisher p
    ON b.publisher_id = p.publisher_id
INNER JOIN category c
    ON b.category_id = c.category_id
WHERE 1
";

$countParams = [];
$countTypes  = "";

// Search
if (!empty($search)) {

    $countSql .= "
        AND
        (
            b.title LIKE ?
            OR b.isbn LIKE ?
            OR a.author_name LIKE ?
        )
    ";

    $keyword = "%$search%";

    $countParams[] = $keyword;
    $countParams[] = $keyword;
    $countParams[] = $keyword;

    $countTypes .= "sss";
}

// Category
if (!empty($category)) {

    $countSql .= " AND b.category_id = ?";

    $countParams[] = $category;

    $countTypes .= "i";
}

// Publisher
if (!empty($publisher)) {

    $countSql .= " AND b.publisher_id = ?";

    $countParams[] = $publisher;

    $countTypes .= "i";
}

$countStmt = $conn->prepare($countSql);

if (!empty($countParams)) {

    $countStmt->bind_param($countTypes, ...$countParams);

}

$countStmt->execute();

$totalBooks = $countStmt
    ->get_result()
    ->fetch_assoc()['total'];

$totalPages = ceil($totalBooks / $limit);

// =========================================
// Main Query Pagination
// =========================================
$sql .= " ORDER BY b.title ASC LIMIT ?, ?";

$params[] = $start;
$params[] = $limit;

$types .= "ii";

// =========================================
// Execute Main Query
// =========================================
$stmt = $conn->prepare($sql);

$stmt->bind_param($types, ...$params);

$stmt->execute();

$result = $stmt->get_result();

// =========================================
// Load Categories
// =========================================
$categoryResult = $conn->query("
    SELECT category_id, category_name
    FROM category
    ORDER BY category_name ASC
");

// =========================================
// Load Publishers
// =========================================
$publisherResult = $conn->query("
    SELECT publisher_id, publisher_name
    FROM publisher
    ORDER BY publisher_name ASC
");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Books | Online Library System</title>

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

            <!-- Books Page Content Starts Here -->
            <main class="main-content">
                <div class="container-fluid">

                    <!-- ===== Page Heading ===== -->
                    <div class="page-header d-flex align-item-center justify-content-between">
                        <div class="title">
                            <h3>Books Management</h3>
                            <span>
                                <a href="dashboard.php">Home</a> /
                                <a href="books.php">Books</a>
                            </span>
                        </div>
                        <div class="btn">

                            <button id="addBookBtn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBookModal">
                                <i class="bi bi-plus-lg"></i>
                                Add New Book
                            </button>

                        </div>
                    </div>

                    <!-- ===== Filter Card ===== -->
                    <form method="GET" action="books.php" class="filter-card">

                        <div class="row g-3 align-items-end">

                            <!-- Search -->
                            <div class="col-lg-4">

                                <div class="input-group">

                                    <span class="input-group-text">
                                        <i class="bi bi-search"></i>
                                    </span>

                                    <input
                                        type="text"
                                        class="form-control"
                                        name="search"
                                        placeholder="Search by title, ISBN or author"
                                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

                                </div>

                            </div>

                            <!-- Category -->
                            <div class="col-lg-3">
                                <select class="form-select col-lg-3" name="category">

                                    <option value="">All Categories</option>

                                    <?php while ($cat = $categoryResult->fetch_assoc()) : ?>

                                        <option
                                            value="<?= $cat['category_id']; ?>"
                                            <?= ($category == $cat['category_id']) ? 'selected' : ''; ?>>

                                            <?= htmlspecialchars($cat['category_name']); ?>

                                        </option>

                                    <?php endwhile; ?>

                                </select>
                            </div>

                            <!-- Publisher -->
                            <div class="col-lg-3">
                                <select class="form-select col-lg-3" name="publisher">

                                    <option value="">All Publishers</option>

                                    <?php while ($pub = $publisherResult->fetch_assoc()) : ?>

                                        <option
                                            value="<?= $pub['publisher_id']; ?>"
                                            <?= ($publisher == $pub['publisher_id']) ? 'selected' : ''; ?>>

                                            <?= htmlspecialchars($pub['publisher_name']); ?>

                                        </option>

                                    <?php endwhile; ?>

                                </select>
                            </div>

                            <div class="col-lg-1 d-grid">

                                <button class="btn btn-primary">
                                    <i class="bi bi-funnel-fill"></i>
                                </button>

                            </div>

                            <div class="col-lg-1 d-grid">
                                <a href="books.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </a>
                            </div>

                        </div>

                    </form>

                    <!-- ===== Books Table ===== -->
                    <div class="table-card">

                        <div class="table-header">

                            <div class="d-flex align-item-center justify-content-between">
                                <h5 class="mb-0">
                                    <i class="bi bi-book-half"></i>
                                    Books List
                                </h5>

                                <p class="text-dark">
                                    Total Books : <?= $result->num_rows ?>
                                </p>
                            </div>

                        </div>

                        <div class="table-responsive">

                            <table class="table table-hover table-bordered align-middle mb-0">

                                <thead class="table-dark">

                                    <tr>
                                        <th>S.N.</th>
                                        <th>Book Title</th>
                                        <th>ISBN</th>
                                        <th>Author</th>
                                        <th>Category</th>
                                        <th>Publisher</th>
                                        <th>Available</th>
                                        <th>Status</th>
                                        <th class="text-center">Action</th>
                                    </tr>

                                </thead>

                                <tbody>

                                    <?php

                                    $sn = 1;

                                    while ($book = $result->fetch_assoc()) :

                                    ?>

                                        <tr>

                                            <td><?= $sn++ ?></td>

                                            <td><?= htmlspecialchars($book['title']); ?></td>

                                            <td><?= htmlspecialchars($book['isbn']); ?></td>

                                            <td><?= htmlspecialchars($book['author_name']); ?></td>

                                            <td><?= htmlspecialchars($book['category_name']); ?></td>

                                            <td><?= htmlspecialchars($book['publisher_name']); ?></td>

                                            <td><?= $book['available']; ?></td>

                                            <td>

                                                <?php if ($book['available'] > 0) : ?>

                                                    <span class="badge bg-success">
                                                        Available
                                                    </span>

                                                <?php else : ?>

                                                    <span class="badge bg-danger">
                                                        Out of Stock
                                                    </span>

                                                <?php endif; ?>

                                            </td>

                                            <td class="text-center">

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-warning edit-book"
                                                    data-id="<?= $book['book_id']; ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#addBookModal">

                                                    <i class="bi bi-pencil-square"></i>

                                                </button>

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-danger delete-book"
                                                    data-id="<?= $book['book_id']; ?>"
                                                    data-title="<?= htmlspecialchars($book['title']); ?>">

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

                            <p class="text-dark">
                                Showing
                                <?= $totalBooks == 0 ? 0 : $start + 1; ?>
                                to
                                <?= min($start + $limit, $totalBooks); ?>
                                of
                                <?= $totalBooks; ?>
                                books
                            </p>

                            <nav>

                                <ul class="pagination mb-0">

                                    <!-- Previous -->
                                    <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                                        <a class="page-link"
                                        href="?page=<?= $page - 1; ?>&search=<?= urlencode($search); ?>&category=<?= $category; ?>&publisher=<?= $publisher; ?>">
                                            Previous
                                        </a>
                                    </li>

                                    <!-- Page Numbers -->
                                    <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                                        <li class="page-item <?= ($page == $i) ? 'active' : ''; ?>">
                                            <a class="page-link"
                                            href="?page=<?= $i; ?>&search=<?= urlencode($search); ?>&category=<?= $category; ?>&publisher=<?= $publisher; ?>">
                                                <?= $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <!-- Next -->
                                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                        <a class="page-link"
                                        href="?page=<?= $page + 1; ?>&search=<?= urlencode($search); ?>&category=<?= $category; ?>&publisher=<?= $publisher; ?>">
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


    <!-- ===== Add Book Modal ===== -->
    <div class="modal fade" id="addBookModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-centered">

            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h3 id="modalTitle" class="modal-title fw-bold">
                        Add New Book
                    </h3>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body">

                    <!-- Form goes here -->
                    <form id="bookForm" action="save_book.php" method="POST">

                        <input type="hidden" id="bookId" name="book_id">

                        <!-- ==== Book Information ==== -->
                        <div class="form-section">

                            <h5 class="section-title d-flex align-items-center">
                                Book Information
                                <span class="flex-grow-1"></span>
                            </h5>

                            <div class="row g-3">

                                <!-- Book Title -->
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Book Title <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="title"
                                        name="title"
                                        placeholder="Enter book title"
                                        required>
                                </div>

                                <!-- ISBN -->
                                <div class="col-md-6">
                                    <label class="form-label">
                                        ISBN <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="isbn"
                                        name="isbn"
                                        placeholder="Enter ISBN"
                                        required>
                                </div>

                                <!-- Author -->
                                <div class="col-md-6">
                                    <label for="authorSearch" class="form-label">
                                        Author <span class="text-danger">*</span>
                                    </label>

                                    <div class="position-relative">

                                        <input
                                            type="text"
                                            class="form-control smart-input"
                                            id="authorSearch"
                                            autocomplete="off"
                                            placeholder="Enter author"
                                            data-id="">

                                        <input type="hidden" name="author_id" id="authorId">

                                        <div
                                            class="smart-dropdown"
                                            id="authorDropdown">
                                        </div>

                                    </div>
                                </div>

                                <!-- Category -->
                                <div class="col-md-6">
                                    <label for="categorySearch" class="form-label">
                                        Category <span class="text-danger">*</span>
                                    </label>

                                    <div class="position-relative">

                                        <input
                                            type="text"
                                            class="form-control smart-input"
                                            id="categorySearch"
                                            autocomplete="off"
                                            placeholder="Enter category"
                                            data-id="">

                                        <input type="hidden" name="category_id" id="categoryId">

                                        <div
                                            class="smart-dropdown"
                                            id="categoryDropdown">
                                        </div>

                                    </div>
                                </div>

                                <!-- Publisher -->
                                <div class="col-md-6">
                                    <label for="publisherSearch" class="form-label">
                                        Publisher <span class="text-danger">*</span>
                                    </label>

                                    <div class="position-relative">

                                        <input
                                            type="text"
                                            class="form-control smart-input"
                                            id="publisherSearch"
                                            autocomplete="off"
                                            placeholder="Enter publisher"
                                            data-id="">

                                        <input type="hidden" name="publisher_id" id="publisherId">

                                        <div
                                            class="smart-dropdown"
                                            id="publisherDropdown">
                                        </div>

                                    </div>
                                </div>

                                <!-- Publication Year -->
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Publication Year
                                    </label>

                                    <input
                                        type="number"
                                        class="form-control"
                                        id="publicationYear"
                                        name="publication_year"
                                        placeholder="Enter publication year"
                                        min="1900"
                                        max="<?php echo date('Y'); ?>" />
                                </div>

                            </div>

                        </div>

                        <!-- ===== Stock Information ===== -->
                        <div class="form-section mt-4">

                            <h5 class="section-title d-flex align-items-center">
                                Stock Information
                                <span class="flex-grow-1"></span>
                            </h5>

                            <div class="row g-3">

                                <!-- Total Quantity -->
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Total Quantity <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="number"
                                        class="form-control"
                                        id="total"
                                        name="total_quantity"
                                        placeholder="Enter total quantity"
                                        min="1"
                                        required>
                                </div>

                                <!-- Available Quantity -->
                                <div class="col-md-6">
                                    <label class="form-label">
                                        Available Quantity <span class="text-danger">*</span>
                                    </label>

                                    <input
                                        type="number"
                                        class="form-control"
                                        id="available"
                                        name="available_quantity"
                                        placeholder="Enter available quantity"
                                        min="0"
                                        required>
                                </div>

                            </div>

                        </div>

                        <!-- ===== Additional Information ===== -->
                        <div class="form-section mt-4">

                            <h5 class="section-title d-flex align-items-center">
                                Additional Information
                                <span class="flex-grow-1"></span>
                            </h5>

                            <div class="mb-3">

                                <label class="form-label">
                                    Description
                                </label>

                                <textarea
                                    class="form-control"
                                    id="description"
                                    name="description"
                                    rows="4"
                                    placeholder="Enter book description..."></textarea>

                            </div>

                        </div>

                        <!-- ===== Buttons ===== -->
                        <div class="modal-footer border-0 p-0">

                            <button
                                type="button"
                                class="btn btn-danger px-4"
                                data-bs-dismiss="modal">

                                Cancel

                            </button>

                            <button
                                id="saveBookBtn"
                                type="submit"
                                name="save_book"
                                class="btn btn-primary px-3">

                                <i class="bi bi-floppy"></i>
                                Save Book

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
    <script src="../assets/js/books.js"></script>

</body>

</html>