<?php

// =====================================================
// DashTech Computers - Website Booking Processor
// =====================================================

ini_set('display_errors', '0');


// =====================================================
// LOAD REQUIRED FILES
// =====================================================

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/recaptcha.php';
require_once __DIR__ . '/upload.php';
require_once __DIR__ . '/database.php';

require_once __DIR__ . '/../vendor/phpmailer/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;


// =====================================================
// ONLY ALLOW POST REQUESTS
// =====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    die('Invalid request.');

}


// =====================================================
// COLLECT BOOKING INFORMATION
// =====================================================

$fullname = clean($_POST['fullname'] ?? '');

$email = filter_var(
    $_POST['email'] ?? '',
    FILTER_VALIDATE_EMAIL
);

$phone = clean($_POST['phone'] ?? '');

$company = clean($_POST['company'] ?? '');

$website = clean($_POST['website'] ?? '');

$message = clean($_POST['message'] ?? '');


// =====================================================
// VALIDATE REQUIRED FIELDS
// =====================================================

if ($fullname === '') {

    die('Please enter your full name.');

}

if (!$email) {

    die('Invalid email address.');

}

if ($phone === '') {

    die('Please enter your phone number.');

}

if ($website === '') {

    die('Please select the website you want DashTech to host.');

}


// =====================================================
// GENERATE BOOKING REFERENCE
// =====================================================

$bookingReference =
    'DT-' .
    date('Ymd') .
    '-' .
    strtoupper(bin2hex(random_bytes(3)));


// =====================================================
// UPLOADED FILE
// =====================================================

$attachmentPath = $uploadedFile ?? '';


// =====================================================
// SAVE BOOKING TO DATABASE
// =====================================================

try {

    $stmt = $pdo->prepare("
        INSERT INTO bookings (
            booking_reference,
            fullname,
            email,
            phone,
            company,
            website_type,
            project_details,
            attachment
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([

        $bookingReference,
        $fullname,
        $email,
        $phone,
        $company,
        $website,
        $message,
        $attachmentPath

    ]);

} catch (PDOException $e) {

    error_log(
        'DashTech Database Error: ' .
        $e->getMessage()
    );

    die(
        'We could not save your booking. Please try again later.'
    );

}


// =====================================================
// CONFIGURE PHPMailer
// =====================================================

function configureMailer(): PHPMailer
{

    $mail = new PHPMailer(true);

    $mail->isSMTP();

    $mail->Host = SMTP_HOST;

    $mail->SMTPAuth = true;

    $mail->Username = SMTP_USERNAME;

    $mail->Password = SMTP_PASSWORD;

    $mail->SMTPSecure = SMTP_ENCRYPTION === 'tls'
        ? PHPMailer::ENCRYPTION_STARTTLS
        : PHPMailer::ENCRYPTION_SMTPS;

    $mail->Port = SMTP_PORT;

    $mail->CharSet = 'UTF-8';

    $mail->isHTML(true);

    return $mail;

}


// =====================================================
// SEND ADMIN EMAIL
// =====================================================

try {

    $mail = configureMailer();

    $mail->setFrom(
        SMTP_USERNAME,
        SITE_NAME
    );
    
    
    // Use SITE_EMAIL because that is what exists
    // in your config.php
    $mail->addAddress(
        SITE_EMAIL,
        SITE_NAME
    );

    $mail->addReplyTo(
        $email,
        $fullname
    );

    $mail->Subject =
        'New Website Booking - ' .
        $website .
        ' - ' .
        $bookingReference;


    $mail->Body = '

    <html>

    <body style="font-family:Arial,sans-serif;line-height:1.6;">

        <h2>New Website Hosting Booking</h2>

        <p>
            A new website hosting request has been
            submitted through the DashTech Computers website.
        </p>

        <hr>

        <h3>Booking Information</h3>

        <table cellpadding="8" cellspacing="0">

            <tr>
                <td><strong>Booking Reference:</strong></td>
                <td>' .
                htmlspecialchars($bookingReference) .
                '</td>
            </tr>

            <tr>
                <td><strong>Full Name:</strong></td>
                <td>' .
                htmlspecialchars($fullname) .
                '</td>
            </tr>

            <tr>
                <td><strong>Email:</strong></td>
                <td>' .
                htmlspecialchars($email) .
                '</td>
            </tr>

            <tr>
                <td><strong>Phone:</strong></td>
                <td>' .
                htmlspecialchars($phone) .
                '</td>
            </tr>

            <tr>
                <td><strong>Company:</strong></td>
                <td>' .
                htmlspecialchars(
                    $company ?: 'Not provided'
                ) .
                '</td>
            </tr>

            <tr>
                <td><strong>Website Type:</strong></td>
                <td>' .
                htmlspecialchars($website) .
                '</td>
            </tr>

        </table>

        <h3>Project Details</h3>

        <p>' .
        nl2br(
            htmlspecialchars(
                $message ?: 'No project details provided.'
            )
        ) .
        '</p>

        <hr>

        <p>
            <strong>DashTech Computers</strong><br>
            ICT & Consultancy Services<br>
            Phone: +2347044390179<br>
            WhatsApp: +2347044390179<br>
            Email: info@dashtechwebhosting.com.ng
        </p>

    </body>

    </html>

    ';


    // Add attachment if available

    if (
        !empty($attachmentPath) &&
        is_file(__DIR__ . '/../' . $attachmentPath)
    ) {

        $mail->addAttachment(
            __DIR__ . '/../' . $attachmentPath
        );

    }


    $mail->send();


} catch (Exception $e) {

    error_log(
        'DashTech Admin Email Error: ' .
        $e->getMessage()
    );

    die(
        'Your booking was saved, but we could not send the notification email.'
    );

}


// =====================================================
// SEND CUSTOMER CONFIRMATION
// =====================================================

try {

    $customerMail = configureMailer();

    $customerMail->setFrom(
        SMTP_USERNAME,
        SITE_NAME
    );

    $customerMail->addAddress(
        $email,
        $fullname
    );

    $customerMail->Subject =
        'Booking Received - ' .
        $bookingReference;


    $customerMail->Body = '

    <html>

    <body style="font-family:Arial,sans-serif;line-height:1.6;">

        <h2>
            Thank You for Booking with DashTech Computers
        </h2>

        <p>
            Hello ' .
            htmlspecialchars($fullname) .
            ',
        </p>

        <p>
            Thank you for choosing
            <strong>DashTech Computers</strong>
            for your website hosting and ICT needs.
        </p>

        <p>
            We have successfully received your
            website hosting request.
        </p>

        <h3>Your Booking Details</h3>

        <table cellpadding="8" cellspacing="0">

            <tr>
                <td><strong>Booking Reference:</strong></td>
                <td>' .
                htmlspecialchars($bookingReference) .
                '</td>
            </tr>

            <tr>
                <td><strong>Website Type:</strong></td>
                <td>' .
                htmlspecialchars($website) .
                '</td>
            </tr>

            <tr>
                <td><strong>Status:</strong></td>
                <td>Pending</td>
            </tr>

        </table>

        <p>
            Our team will review your request and prepare
            your website hosting service within
            <strong>5 working days</strong>.
        </p>

        <p>
            If we need additional information,
            we will contact you using the details
            provided in your booking.
        </p>

        <p>
            You can check your booking status anytime at
            <a href="https://www.dashtechwebhosting.com.ng/client.php?reference=' . rawurlencode($bookingReference) . '">the DashTech client portal</a>.
        </p>

        <hr>

        <p>
            <strong>DashTech Computers</strong><br>
            ICT & Consultancy Services<br>
            Phone: +2347044390179<br>
            WhatsApp: +2347044390179<br>
            Email: info@dashtechwebhosting.com.ng
        </p>

    </body>

    </html>

    ';


    $customerMail->send();


} catch (Exception $e) {

    error_log(
        'DashTech Customer Email Error: ' .
        $e->getMessage()
    );

}


// =====================================================
// SUCCESS
// =====================================================

header(
    'Location: ../success.html'
);

exit;
