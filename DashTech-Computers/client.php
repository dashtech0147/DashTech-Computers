<?php
declare(strict_types=1);
require_once __DIR__ . '/php/database.php';

$reference = strtoupper(trim((string) ($_GET['reference'] ?? $_POST['reference'] ?? '')));
$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
$booking = null;
$searched = $_SERVER['REQUEST_METHOD'] === 'POST';
if ($searched && $reference !== '' && $email) {
    $stmt = $pdo->prepare('SELECT booking_reference, fullname, website_type, status, created_at FROM bookings WHERE booking_reference = ? AND email = ? LIMIT 1');
    $stmt->execute([$reference, $email]);
    $booking = $stmt->fetch();
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Client Portal | DashTech Computers</title><style>body{margin:0;background:#f4f7fb;color:#17212b;font:16px Arial,sans-serif}.wrap{max-width:650px;margin:6rem auto;padding:2rem;background:#fff;border-radius:16px;box-shadow:0 8px 28px #00336620}input,button{box-sizing:border-box;width:100%;padding:14px;margin:8px 0;border:1px solid #c7d1dc;border-radius:8px}button{border:0;background:#003366;color:#fff;font-weight:bold;cursor:pointer}.status{display:inline-block;padding:7px 12px;border-radius:99px;background:#e7f1ff;color:#003366;font-weight:bold}a{color:#005bb5}.error{color:#a51d2d}</style></head><body><main class="wrap"><h1>Client Portal</h1><p>Enter the email address used for your booking to view its current status.</p><form method="post"><label>Booking reference<input name="reference" required value="<?= htmlspecialchars($reference, ENT_QUOTES, 'UTF-8') ?>"></label><label>Email address<input type="email" name="email" required></label><button>View booking</button></form><?php if ($searched && !$booking): ?><p class="error">We could not find a booking with those details.</p><?php endif; ?><?php if ($booking): ?><hr><h2>Hello, <?= htmlspecialchars($booking['fullname'], ENT_QUOTES, 'UTF-8') ?></h2><p><strong>Reference:</strong> <?= htmlspecialchars($booking['booking_reference'], ENT_QUOTES, 'UTF-8') ?></p><p><strong>Service:</strong> <?= htmlspecialchars($booking['website_type'], ENT_QUOTES, 'UTF-8') ?></p><p><strong>Status:</strong> <span class="status"><?= htmlspecialchars($booking['status'], ENT_QUOTES, 'UTF-8') ?></span></p><p><strong>Submitted:</strong> <?= htmlspecialchars($booking['created_at'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?><p><a href="index.html">Return to DashTech</a></p></main></body></html>
