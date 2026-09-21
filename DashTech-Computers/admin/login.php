<?php

require_once __DIR__ . '/bootstrap.php';
startAdminSession();

$configurationError = false;
try {
    require_once __DIR__ . '/../php/config.php';
} catch (Throwable $error) {
    error_log('DashTech admin configuration error: ' . $error->getMessage());
    http_response_code(503);
    $configurationError = true;
}


// If already logged in, go to dashboard
if (!empty($_SESSION['admin_logged_in'])) {

    header('Location: dashboard.php');
    exit;

}

$error = '';

$loggedOut = isset($_GET['logged_out']);


// =====================================================
// PROCESS LOGIN
// =====================================================

if (!$configurationError && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');

    $password = $_POST['password'] ?? '';


    if ($username === '' || $password === '') {

        $error = 'Please enter your username and password.';

    } elseif (
        $username !== ADMIN_USERNAME
    ) {

        $error = 'Invalid username or password.';

    } elseif (
        !password_verify(
            $password,
            ADMIN_PASSWORD_HASH
        )
    ) {

        $error = 'Invalid username or password.';

    } else {

        // Prevent session fixation
        session_regenerate_id(true);

        $_SESSION['admin_logged_in'] = true;

        $_SESSION['admin_username'] = ADMIN_USERNAME;

        $_SESSION['admin_login_time'] = time();
        $_SESSION['admin_last_activity'] = time();
        csrfToken();

        header('Location: dashboard.php');

        exit;

    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Admin Login | DashTech Computers
    </title>

    <link rel="stylesheet" href="css/login.css">

</head>

<body>

    <main>

        <h1>DashTech Computers</h1>

        <h2>Administrator Login</h2>

        <?php if ($error !== ''): ?>

            <p class="alert error">
                <?php echo htmlspecialchars($error); ?>
            </p>

        <?php endif; ?>

        <?php if ($configurationError): ?>

            <p class="alert notice">
                The administrator area has not been configured on this hosting account yet.
                Add the required values to <code>php/config.local.php</code>, then try again.
            </p>

        <?php endif; ?>
        
        
        <?php if ($loggedOut): ?>

              <p class="alert success">
                 You have been securely logged out.
             </p>

        <?php endif; ?>


        <?php if (!$configurationError): ?>
        <form
            method="POST"
            action=""
            autocomplete="off"
        >

            <div>

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    required
                    autocomplete="username"
                >

            </div>


            <br>


            <div>

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                >

            </div>


            <br>


            <button type="submit">
                Login
            </button>

        </form>
        <?php endif; ?>

    </main>

</body>

</html>
