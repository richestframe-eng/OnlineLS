<?php

require_once '../includes/notification.php';

if (isset($_SESSION['admin_id'])) {
    $userType = 'admin';

    $adminNotificationCount =
        getUnreadNotificationCount(
            $conn,
            'Admin',
            $_SESSION['admin_id']
        );
} elseif (isset($_SESSION['student_id'])) {
    $userType = 'student';
} else {
    $userType = '';
}

?>

<aside class="sidebar">

    <!-- ===== Logo ===== -->
    <div class="sidebar-logo">

        <i class="bi bi-book"></i>

        <div class="logo-text">
            <h4>Online Library</h4>
            <p>System</p>
        </div>

    </div>

    <!-- ===== Navigation ===== -->
    <nav class="sidebar-menu">

        <ul>

            <?php if ($userType === 'admin'): ?>

                <!-- ===== Admin Menu ===== -->
                <li>
                    <a href="../admin/dashboard.php">
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="../admin/books.php">
                        <i class="bi bi-book-half"></i>
                        <span>Books</span>
                    </a>
                </li>

                <li>
                    <a href="../admin/students.php">
                        <i class="bi bi-people-fill"></i>
                        <span>Students</span>
                    </a>
                </li>

                <li>
                    <a href="../admin/authors.php">
                        <i class="bi bi-pen-fill"></i>
                        <span>Authors</span>
                    </a>
                </li>

                <li>
                    <a href="../admin/publishers.php">
                        <i class="bi bi-buildings-fill"></i>
                        <span>Publishers</span>
                    </a>
                </li>

                <li>
                    <a href="../admin/categories.php">
                        <i class="bi bi-folder-fill"></i>
                        <span>Categories</span>
                    </a>
                </li>

                <li>
                    <a href="../admin/issue.php">
                        <i class="bi bi-upload"></i>
                        <span>Issue Book</span>
                    </a>
                </li>

                <li>
                    <a href="../admin/requests.php">
                        <i class="bi bi-bookmark-check-fill"></i>
                        <span>Book Requests</span>
                        <?php if ($adminNotificationCount > 0): ?>
                            <span id="sidebar-notification-badge" class="badge bg-danger ms-auto">
                                <?= $adminNotificationCount; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>

                <li>
                    <a href="../admin/reports.php">
                        <i class="bi bi-bar-chart-fill"></i>
                        <span>Reports</span>
                    </a>
                </li>


            <?php elseif ($userType === 'student'): ?>

                <!-- ===== Student Menu ===== -->

                <li>
                    <a href="../student/dashboard.php">
                        <i class="bi bi-grid-fill"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="../student/search.php">
                        <i class="bi bi-book-half"></i>
                        <span>Search Books</span>
                    </a>
                </li>

                <li>
                    <a href="../student/issued_books.php">
                        <i class="bi bi-journal-bookmark-fill"></i>
                        <span>My Books</span>
                    </a>
                </li>

                <li>
                    <a href="../student/fines.php">
                        <i class="bi bi-cash-coin"></i>
                        <span>My Fines</span>
                    </a>
                </li>

            <?php endif; ?>

        </ul>

    </nav>

</aside>