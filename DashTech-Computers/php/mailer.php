<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../vendor/phpmailer/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;

function dashtechMailer(): PHPMailer
{
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = SMTP_ENCRYPTION === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = SMTP_PORT;
    $mail->CharSet = 'UTF-8';
    $mail->isHTML(true);
    $mail->setFrom(SMTP_USERNAME, SITE_NAME);
    return $mail;
}

function sendBookingStatusEmail(array $booking): void
{
    $mail = dashtechMailer();
    $mail->addAddress($booking['email'], $booking['fullname']);
    $mail->Subject = 'Booking update: ' . $booking['booking_reference'];
    $reference = htmlspecialchars($booking['booking_reference'], ENT_QUOTES, 'UTF-8');
    $name = htmlspecialchars($booking['fullname'], ENT_QUOTES, 'UTF-8');
    $status = htmlspecialchars($booking['status'], ENT_QUOTES, 'UTF-8');
    $portal = 'https://www.dashtechwebhosting.com.ng/client.php?reference=' . rawurlencode($booking['booking_reference']);
    $mail->Body = "<html><body style=\"font-family:Arial,sans-serif;line-height:1.6\"><h2>Your booking has been updated</h2><p>Hello {$name},</p><p>Your DashTech booking <strong>{$reference}</strong> is now <strong>{$status}</strong>.</p><p><a href=\"{$portal}\">View your booking status</a></p><p>DashTech Computers<br>Email: " . htmlspecialchars(SITE_EMAIL, ENT_QUOTES, 'UTF-8') . "</p></body></html>";
    $mail->AltBody = "Hello {$booking['fullname']}, your booking {$booking['booking_reference']} is now {$booking['status']}. View it at {$portal}";
    $mail->send();
}
