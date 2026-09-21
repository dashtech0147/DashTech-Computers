<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('Invalid request.');
}

if (!defined('RECAPTCHA_SECRET_KEY')) {
    exit('reCAPTCHA configuration error.');
}

if (empty($_POST['g-recaptcha-response'])) {
    exit('reCAPTCHA token missing.');
}

$token = trim($_POST['g-recaptcha-response']);

$data = [
    'secret'   => RECAPTCHA_SECRET_KEY,
    'response' => $token
];

$options = [
    'http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query($data),
        'timeout' => 15,
        'ignore_errors' => true
    ]
];

$context = stream_context_create($options);

$result = file_get_contents(
    'https://www.google.com/recaptcha/api/siteverify',
    false,
    $context
);

if ($result === false) {
    exit('Unable to contact Google reCAPTCHA.');
}

$response = json_decode($result, true);

if (!is_array($response)) {
    exit('Invalid response from Google reCAPTCHA.');
}

if (empty($response['success'])) {
    exit('reCAPTCHA verification failed.');
}

if (($response['score'] ?? 0) < 0.5) {
    exit('reCAPTCHA score is too low.');
}

if (($response['action'] ?? '') !== 'booking') {
    exit('Invalid reCAPTCHA action.');
}