<?php

// =====================================================
// DashTech Computers - Admin Logout
// =====================================================

require_once __DIR__ . '/bootstrap.php';
startAdminSession();


// =====================================================
// CLEAR ALL SESSION DATA
// =====================================================

$_SESSION = [];


// =====================================================
// REMOVE SESSION COOKIE
// =====================================================

if (ini_get('session.use_cookies')) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );

}


// =====================================================
// DESTROY SESSION
// =====================================================

session_destroy();


// =====================================================
// REDIRECT TO LOGIN
// =====================================================

header('Location: login.php?logged_out=1');

exit;
