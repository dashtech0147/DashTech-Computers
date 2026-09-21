<?php
declare(strict_types=1);

/*
 * Shared hosting normally cannot set PHP environment variables. Copy
 * config.local.example.php to config.local.php and add the real values there.
 * config.local.php is ignored by Git and blocked from web access.
 */
$localConfigFile = __DIR__ . '/config.local.php';
$dashtechLocalConfig = is_file($localConfigFile) ? require $localConfigFile : [];
if (!is_array($dashtechLocalConfig)) {
    throw new RuntimeException('php/config.local.php must return an array.');
}

function environmentValue(string $name, ?string $default = null): string
{
    global $dashtechLocalConfig;
    $value = getenv($name);
    if ($value !== false && $value !== '') return $value;
    if (isset($dashtechLocalConfig[$name]) && $dashtechLocalConfig[$name] !== '') {
        return (string) $dashtechLocalConfig[$name];
    }
    if ($default !== null) return $default;
    throw new RuntimeException('Missing required server configuration: ' . $name);
}

define('SITE_NAME', environmentValue('SITE_NAME', 'DashTech Computers'));
define('SITE_EMAIL', environmentValue('SITE_EMAIL', 'info@dashtechwebhosting.com.ng'));
define('ADMIN_EMAIL', environmentValue('ADMIN_EMAIL', SITE_EMAIL));
define('SMTP_HOST', environmentValue('SMTP_HOST', 'mail.dashtechwebhosting.com.ng'));
define('SMTP_PORT', (int) environmentValue('SMTP_PORT', '465'));
define('SMTP_USERNAME', environmentValue('SMTP_USERNAME', SITE_EMAIL));
define('SMTP_PASSWORD', environmentValue('SMTP_PASSWORD'));
define('SMTP_ENCRYPTION', environmentValue('SMTP_ENCRYPTION', 'ssl'));
define('RECAPTCHA_SITE_KEY', environmentValue('RECAPTCHA_SITE_KEY', '6Ld4vEstAAAAAHKwdNi9vla2_3kXWAJZFNTfSmee'));
define('RECAPTCHA_SECRET_KEY', environmentValue('RECAPTCHA_SECRET_KEY'));
define('ADMIN_USERNAME', environmentValue('ADMIN_USERNAME', 'admin'));
define('ADMIN_PASSWORD_HASH', environmentValue('ADMIN_PASSWORD_HASH'));
