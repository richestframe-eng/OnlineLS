<?php

require_once '../includes/auth.php';
require_once '../includes/db.php';

requireAdmin();

$pageTitle = 'Admin Profile';

$adminId = $_SESSION['admin_id'];

$stmt = $conn->prepare("
    SELECT
        admin_id,
        username,
        email
    FROM admin
    WHERE admin_id = ?
    LIMIT 1
");

$stmt->bind_param("i", $adminId);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = 'Admin profile not found.';
    header("Location: dashboard.php");
    exit();
}

$admin = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Profile - Online Library System</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>

<body>

<div class="d-flex">

    <?php include '../includes/sidebar.php'; ?>

    <div class="main-container flex-grow-1">

        <?php include '../includes/header.php'; ?>

        <main class="content px-4 py-3">

            <div class="page-heading mb-4">

                <div>

                    <h2>Admin Profile</h2>

                    <p>
                        View your administrator information.
                    </p>

                </div>

            </div>


            <div class="card shadow-sm">

                <div class="card-body">

                    <div class="row align-items-center">

                        <!-- Admin Icon -->

                        <div class="col-md-4 text-center">

                            <img
                                src="../assets/images/admin.svg"
                                alt="Admin"
                                style="
                                    width:150px;
                                    height:180px;
                                    object-fit:contain;
                                "
                            >

                            <h4 class="mb-1">

                                <?= htmlspecialchars(
                                    $admin['username']
                                ); ?>

                            </h4>

                            <span class="badge bg-primary">
                                Administrator
                            </span>

                        </div>


                        <!-- Admin Details -->

                        <div class="col-md-8">

                            <div class="row g-3">


                                <!-- Admin ID -->

                                <div class="col-md-6">

                                    <label class="form-label text-muted">
                                        Admin ID
                                    </label>

                                    <div class="form-control bg-light">

                                        <?= htmlspecialchars(
                                            $admin['admin_id']
                                        ); ?>

                                    </div>

                                </div>


                                <!-- Username -->

                                <div class="col-md-6">

                                    <label class="form-label text-muted">
                                        Username
                                    </label>

                                    <div class="form-control bg-light">

                                        <?= htmlspecialchars(
                                            $admin['username']
                                        ); ?>

                                    </div>

                                </div>


                                <!-- Email -->

                                <div class="col-12">

                                    <label class="form-label text-muted">
                                        Email
                                    </label>

                                    <div class="form-control bg-light">

                                        <?= htmlspecialchars(
                                            $admin['email']
                                        ); ?>

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


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>

<script src="../assets/js/script.js"></script>

</body>

</html>