<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    exit('Invalid request.');
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$status = trim((string) ($_POST['status'] ?? ''));
if (!$id || !in_array($status, adminStatuses(), true)) {
    http_response_code(422);
    exit('Invalid booking update.');
}

require_once __DIR__ . '/../php/database.php';

try {
    $stmt = $pdo->prepare('SELECT id, booking_reference, fullname, email, status FROM bookings WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $booking = $stmt->fetch();
    if (!$booking) {
        http_response_code(404);
        exit('Booking not found.');
    }
    $changed = $booking['status'] !== $status;
    if ($changed) {
        $update = $pdo->prepare('UPDATE bookings SET status = ? WHERE id = ?');
        $update->execute([$status, $id]);
        $booking['status'] = $status;
        try {
            require_once __DIR__ . '/../php/mailer.php';
            sendBookingStatusEmail($booking);
            $notice = 'Status updated and the customer was notified.';
        } catch (Throwable $error) {
            error_log('DashTech status email error: ' . $error->getMessage());
            $notice = 'Status updated, but the customer email could not be sent.';
        }
    } else {
        $notice = 'Booking status is unchanged.';
    }
} catch (PDOException $error) {
    error_log('DashTech booking update error: ' . $error->getMessage());
    $notice = 'The booking could not be updated.';
}

header('Location: view-booking.php?id=' . $id . '&notice=' . rawurlencode($notice));
exit;
