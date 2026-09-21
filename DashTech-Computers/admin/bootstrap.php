<?php
declare(strict_types=1);

function startAdminSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/admin',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

function requireAdmin(): void
{
    startAdminSession();
    $timeout = 60 * 60 * 2;
    if (empty($_SESSION['admin_logged_in']) || (isset($_SESSION['admin_last_activity']) && time() - $_SESSION['admin_last_activity'] > $timeout)) {
        $_SESSION = [];
        session_destroy();
        header('Location: login.php?expired=1');
        exit;
    }
    $_SESSION['admin_last_activity'] = time();
}

function csrfToken(): string
{
    startAdminSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function adminStatuses(): array
{
    return ['Pending', 'Processing', 'Approved', 'Completed', 'Cancelled'];
}
