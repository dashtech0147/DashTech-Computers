<?php
declare(strict_types=1);
require_once __DIR__ . '/config.php';

try {
    $pdo = new PDO(
        'mysql:host=' . environmentValue('DB_HOST', 'localhost') . ';dbname=' . environmentValue('DB_NAME') . ';charset=utf8mb4',
        environmentValue('DB_USER'),
        environmentValue('DB_PASSWORD'),
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]
    );
} catch (Throwable $e) {
    error_log('DashTech database error: ' . $e->getMessage());
    http_response_code(503);
    exit('We are currently unable to process your request. Please try again later.');
}
