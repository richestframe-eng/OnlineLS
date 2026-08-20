<?php

function addNotification($conn, $userType, $userId, $message)
{
    $stmt = $conn->prepare("
        INSERT INTO notification
        (user_type, user_id, message)
        VALUES (?, ?, ?)
    ");

    $stmt->bind_param(
        "sis",
        $userType,
        $userId,
        $message
    );

    $stmt->execute();
}

function getNotifications($conn, $userType, $userId)
{
    $stmt = $conn->prepare("
        SELECT notification_id, message, is_read, created_at
        FROM notification
        WHERE user_type = ?
        AND user_id = ?
        ORDER BY created_at DESC
        LIMIT 10
    ");

    $stmt->bind_param("si", $userType, $userId);
    $stmt->execute();

    return $stmt->get_result();
}


function getUnreadNotificationCount($conn, $userType, $userId)
{
    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM notification
        WHERE user_type = ?
        AND user_id = ?
        AND is_read = 0
    ");

    $stmt->bind_param("si", $userType, $userId);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc()['total'];
}

function markNotificationsRead($conn, $userType, $userId)
{
    $stmt = $conn->prepare("
        UPDATE notification
        SET is_read = 1
        WHERE user_type = ?
        AND user_id = ?
        AND is_read = 0
    ");

    $stmt->bind_param("si", $userType, $userId);
    $stmt->execute();
}

function sendDueDateReminders($conn, $studentId)
{
    $stmt = $conn->prepare("
        SELECT
            ir.transaction_id,
            ir.student_id,
            b.title,
            ir.due_date

        FROM issue_return ir

        INNER JOIN book b
            ON ir.book_id = b.book_id

        WHERE ir.student_id = ?
          AND ir.status = 'Issued'
          AND ir.due_date BETWEEN CURDATE()
          AND DATE_ADD(CURDATE(), INTERVAL 2 DAY)
    ");

    $stmt->bind_param("i", $studentId);

    $stmt->execute();

    $result = $stmt->get_result();


    while ($row = $result->fetch_assoc()) {

        $today = new DateTime('today', new DateTimeZone('Asia/Kathmandu'));

        $dueDate = new DateTime($row['due_date'], new DateTimeZone('Asia/Kathmandu'));

        $daysLeft = (int) $today->diff($dueDate)->format('%r%a');


        if ($daysLeft === 2) {

            $message =
                'Your book "' .
                $row['title'] .
                '" is due in 2 days. Please return it to avoid fines.';

        } elseif ($daysLeft === 1) {

            $message =
                'Reminder: Your book "' .
                $row['title'] .
                '" is due tomorrow. Please return it to avoid fines.';

        } elseif ($daysLeft === 0) {

            $message =
                'Your book "' .
                $row['title'] .
                '" is due today. Please return it to avoid fines.';

        } else {

            continue;
        }


        // Prevent duplicate reminder on the same day

        $check = $conn->prepare("
            SELECT notification_id

            FROM notification

            WHERE user_type = 'Student'
              AND user_id = ?
              AND message = ?
              AND DATE(created_at) = CURDATE()

            LIMIT 1
        ");

        $check->bind_param(
            "is",
            $studentId,
            $message
        );

        $check->execute();


        if ($check->get_result()->num_rows === 0) {

            addNotification(
                $conn,
                'Student',
                $studentId,
                $message
            );
        }
    }
}