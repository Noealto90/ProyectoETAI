<?php
// Simple autoloader to allow using PHPMailer without Composer.
// Place the PHPMailer source files under vendor/phpmailer/phpmailer/src/
// and this autoloader will load classes in the PHPMailer\PHPMailer\ namespace.

spl_autoload_register(function ($class) {
    // Only handle PHPMailer namespace here
    $prefix = 'PHPMailer\\PHPMailer\\';
    $base_dir = __DIR__ . '/phpmailer/phpmailer/src/';

    if (strpos($class, $prefix) !== 0) {
        return; // not our namespace
    }

    $relative_class = substr($class, strlen($prefix));
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Backwards compatibility: if code still requires specific files, we provide a
// small shim to include them if they exist in the expected location.
function PHPMailer_manual_include($name)
{
    $base = __DIR__ . '/phpmailer/phpmailer/src/';
    $map = [
        'Exception' => 'Exception.php',
        'PHPMailer' => 'PHPMailer.php',
        'SMTP' => 'SMTP.php',
    ];

    if (isset($map[$name])) {
        $file = $base . $map[$name];
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    return false;
}

// Optionally expose a small helper so older require() calls can be swapped to:
// require_once __DIR__ . '/vendor/autoload.php'; PHPMailer_manual_include('PHPMailer');
