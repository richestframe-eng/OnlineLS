<?php
    session_start();

    function requireAdmin()
    {
        if (!isset($_SESSION['admin_id'])) {
            header("Location: ../login.php");
            exit();
        }
    }

    function requireStudent()
    {
        if (!isset($_SESSION['student_id'])) {
            header("Location: ../login.php");
            exit();
        }
    }
?>