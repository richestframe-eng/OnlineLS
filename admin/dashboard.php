<?php
    require_once '../includes/auth.php';
    require_once '../includes/db.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | Online Library System</title>

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

        <!-- ==== Container ==== -->
        <div class="main-container flex-grow-1">
            <!-- ===== Header ===== -->
            <?php include '../includes/header.php'; ?>

            <!-- ==== Content ===== -->
            <main class="main-content">

                <!-- Welcome Card -->
                <section class="welcome-card">

                    <div class="welcome-content">
                        <h2>
                            Welcome back, Admin! 👋
                        </h2>

                        <p>
                            Here's what's happening in your library today.
                        </p>
                    </div>

                    <div class="welcome-decora"></div>

                </section>

                <!-- Statistics Cards -->
                <section class="stats-container">

                    <div class="stat-card">
                        <div class="details-card">
                            <i class="bi bi-book-half bg-primary"></i>
                            <div class="text-card">
                                <h3>Total Books</h3>
                                <p>150</p>
                            </div>
                        </div>
                        <div class="btn-card">
                            <a href="books.php">
                                <span>View all books</span>
                                <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="details-card">
                            <i class="bi bi-people-fill bg-success"></i>
                            <div class="text-card">
                                <h3>Total Students</h3>
                                <p>80</p>
                            </div>
                        </div>
                        <div class="btn-card">
                            <a href="students.php">
                                <span>View all students</span>
                                <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="details-card">
                            <i class="bi bi-journal-bookmark-fill bg-danger"></i>
                            <div class="text-card">
                                <h3>Issued Books</h3>
                                <p>30</p>
                            </div>
                        </div>
                        <div class="btn-card">
                            <a href="issue.php">
                                <span>View issued books</span>
                                <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="details-card">
                            <i class="bi bi-clipboard-data-fill bg-dark"></i>
                            <div class="text-card">
                                <h3>Available Books</h3>
                                <p>120</p>
                            </div>
                        </div>
                        <div class="btn-card">
                            <a href="books.php">
                                <span>View available books</span>
                                <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </div>
                    </div>

                </section>

                <!-- Recently Issued Books -->
                <section class="recent-books">

                    <div class="recent-books-header">

                        <div>
                            <i class="bi bi-clock"></i>
                            <h4>Recently Issued Books</h4>
                        </div>

                        <a href="issue.php" class="view-all-btn">
                            View All
                            <i class="bi bi-chevron-right ms-1"></i>
                        </a>

                    </div>

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle mb-0">

                            <thead>
                                <tr class="table-dark">
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Book Title</th>
                                    <th>Issue Date</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>

                                <tr>
                                    <td>1</td>
                                    <td>Ram Sharma</td>
                                    <td>Database Management System</td>
                                    <td>15 May 2025</td>
                                    <td>29 May 2025</td>
                                    <td>
                                        <span class="status-badge issued">
                                            Issued
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <td>2</td>
                                    <td>Sita Thapa</td>
                                    <td>PHP Programming</td>
                                    <td>14 May 2025</td>
                                    <td>28 May 2025</td>
                                    <td>
                                        <span class="status-badge issued">
                                            Issued
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <td>3</td>
                                    <td>Hari Poudel</td>
                                    <td>Operating System Concepts</td>
                                    <td>13 May 2025</td>
                                    <td>27 May 2025</td>
                                    <td>
                                        <span class="status-badge issued">
                                            Issued
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <td>4</td>
                                    <td>Anita Karki</td>
                                    <td>Web Technologies</td>
                                    <td>12 May 2025</td>
                                    <td>26 May 2025</td>
                                    <td>
                                        <span class="status-badge issued">
                                            Issued
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <td>5</td>
                                    <td>Rohit Adhikari</td>
                                    <td>Data Structures</td>
                                    <td>11 May 2025</td>
                                    <td>25 May 2025</td>
                                    <td>
                                        <span class="status-badge issued">
                                            Issued
                                        </span>
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </section>
            </main>
        </div>

    </div>

    <!-- ==== Bootstrap JS ==== -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>    
</body>

</html>