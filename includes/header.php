<?php

if (isset($_SESSION['admin_id'])) {

    $userType = 'admin';
    $userName = 'Admin';

} elseif (isset($_SESSION['student_id'])) {

    $userType = 'student';

    // Student name should already be available from the page/session.
    $userName = $_SESSION['student_name'] ?? 'Student';

} else {

    $userType = '';
    $userName = '';

}

// Page title
$pageTitle = $pageTitle ?? 'Dashboard';

?>

<header class="admin-header">

    <!-- Left Side -->
    <div class="header-left">

        <button type="button" class="menu-btn">
            <i class="bi bi-list"></i>
        </button>

        <h3 class="page-title">
            <?= htmlspecialchars($pageTitle); ?>
        </h3>

    </div>

    <!-- Right Side -->
    <div class="header-right">

        <!-- Notification -->
        <button type="button" class="notification-btn">
            <i class="bi bi-bell"></i>
        </button>

        <!-- Vertical Line -->
        <span class="vertical-line"></span>

        <!-- User Dropdown -->
        <div class="dropdown">

            <button class="btn dropdown-toggle admin-btn"
                    type="button"
                    data-bs-toggle="dropdown">

                <div>
                    <i class="admin-logo"></i>
                </div>

                <span>
                    <?= htmlspecialchars($userName); ?>
                </span>

            </button>

            <ul class="dropdown-menu dropdown-menu-end">

                <li>
                    <a class="dropdown-item"
                       href="<?= $userType === 'student'
                           ? '../student/profile.php'
                           : '../admin/profile.php'; ?>">

                        <i class="bi bi-person"></i>
                        Profile

                    </a>
                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>
                    <a class="dropdown-item text-danger"
                       href="../logout.php">

                        <i class="bi bi-box-arrow-right"></i>
                        Logout

                    </a>
                </li>

            </ul>

        </div>

    </div>

</header>