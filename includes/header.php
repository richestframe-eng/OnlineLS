<?php

require_once '../includes/notification.php';

if (isset($_SESSION['admin_id'])) {

    $userType = 'Admin';
    $userId = $_SESSION['admin_id'];
    $userName = 'Admin';

} elseif (isset($_SESSION['student_id'])) {

    $userType = 'Student';
    $userId = $_SESSION['student_id'];
    $userName = $_SESSION['student_name'] ?? 'Student';

    $stmt = $conn->prepare("
        SELECT photo
        FROM student
        WHERE student_id = ?
        LIMIT 1
    ");

    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $studentData = $stmt->get_result()->fetch_assoc();
    $studentPhoto = basename($studentData['photo'] ?? '');

} else {

    $userType = '';
    $userName = '';
    $studentPhoto = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['mark_notifications_read'])) {

    markNotificationsRead(
        $conn,
        $userType,
        $userId
    );

    echo json_encode(['success' => true]);
    exit;
}

$notificationCount = 0;
$notifications = null;

if ($userType !== '') {

    $notificationCount = getUnreadNotificationCount(
        $conn,
        $userType,
        $userId
    );

    $notifications = getNotifications(
        $conn,
        $userType,
        $userId
    );

}


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
        <div class="dropdown">
            
            <button
                class="btn notification-btn position-relative"
                type="button"
                data-bs-toggle="dropdown"
                aria-expanded="false">

                <i class="bi bi-bell"></i>

                <?php if ($notificationCount > 0): ?>
                    <span id="notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?= $notificationCount; ?>
                    </span>
                <?php endif; ?>
            </button> 

            <ul class="dropdown-menu dropdown-menu-end notification-menu">

                <?php if ($notifications->num_rows > 0): ?>

                    <?php while ($notification = $notifications->fetch_assoc()): ?>

                        <li>

                            <a class="dropdown-item"
                                href="#">

                                <i class="bi bi-bell me-2"></i>

                                <?= htmlspecialchars(
                                    $notification['message']
                                ); ?>

                                <small class="d-block text-muted">

                                    <?= date(
                                        'd M Y, h:i A',
                                        strtotime(
                                            $notification['created_at']
                                        )
                                    ); ?>

                                </small>

                            </a>

                        </li>

                    <?php endwhile; ?>

                <?php else: ?>

                    <li>
                        <span class="dropdown-item text-muted">
                            No notifications
                        </span>
                    </li>

                <?php endif; ?>

            </ul>

        </div>

        <!-- Vertical Line -->
        <span class="vertical-line"></span>

        <!-- User Dropdown -->
        <div class="dropdown">

            <button
                class="btn dropdown-toggle admin-btn"
                type="button"
                data-bs-toggle="dropdown"
            >

                <div>

                    <?php if ($userType === 'Admin'): ?>

                        <div style="padding-top: 3px;">
                            <img
                            src="../assets/images/admin.svg"
                            alt="Admin"
                            style="
                                width:35px;
                                height:35px;
                                object-fit:contain;
                            "
                        >
                        </div>

                    <?php elseif ($userType === 'Student'): ?>

                        <?php
                        $studentPhotoPath =
                            '../assets/uploads/students/' . $studentPhoto;
                        ?>

                        <?php if (
                            !empty($studentPhoto) &&
                            file_exists($studentPhotoPath)
                        ): ?>

                            <img
                                src="<?= htmlspecialchars($studentPhotoPath); ?>"
                                alt="Student Photo"
                                class="rounded-circle"
                                style="
                                    width:35px;
                                    height:35px;
                                    object-fit:cover;
                                "
                            >

                        <?php else: ?>

                            <i class="admin-logo"></i>

                        <?php endif; ?>

                    <?php else: ?>

                        <i class="admin-logo"></i>

                    <?php endif; ?>

                </div>

                <span>
                    <?= htmlspecialchars($userName); ?>
                </span>

            </button>

            <ul class="dropdown-menu dropdown-menu-end">

                <li>
                    <a
                        class="dropdown-item"
                        href="<?= $userType === 'Student'
                            ? '../student/profile.php'
                            : '../admin/profile.php'; ?>"
                    >
                        <i class="bi bi-person"></i>
                        Profile
                    </a>
                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>
                    <a
                        class="dropdown-item text-danger"
                        href="../logout.php"
                    >
                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </a>
                </li>

            </ul>

        </div>

    </div>

</header>

<script>
document.querySelector('.notification-btn')?.addEventListener('click', function () {

    fetch(window.location.href, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'mark_notifications_read=1'
    });

    const headerBadge =
        document.getElementById('notification-badge');

    const sidebarBadge =
        document.getElementById('sidebar-notification-badge');

    if (headerBadge) {
        headerBadge.remove();
    }

    if (sidebarBadge) {
        sidebarBadge.remove();
    }

});
</script>