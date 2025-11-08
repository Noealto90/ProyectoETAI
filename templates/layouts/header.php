<?php
// Header layout - trasladado a templates/layouts/header.php
// Calcula la ruta base del proyecto (p. ej. /ProyectoETAI) para usar en los enlaces a assets
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
if (strpos($scriptName, '/pages/') !== false) {
    $parts = explode('/pages/', $scriptName, 2);
    $basePath = $parts[0];
} else {
    $basePath = dirname($scriptName);
}
if ($basePath === '/' || $basePath === '\\' || $basePath === '.' || $basePath === '') {
    $basePath = '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="<?= $basePath ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?= $basePath ?>/assets/css/add.css">
    <link rel="stylesheet" href="<?= $basePath ?>/assets/css/index.css">
    <link rel="stylesheet" href="<?= $basePath ?>/assets/css/login.css">
    <link rel="stylesheet" href="<?= $basePath ?>/assets/css/register.css">
    <link rel="stylesheet" href="<?= $basePath ?>/assets/css/index-estudiante.css">
    <link rel="stylesheet" href="<?= $basePath ?>/assets/css/super_admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?= $basePath ?>/assets/css/header.css">
    <?php
    // Permitir que páginas incluyan CSS/HTML adicional en el <head>
    if (!empty($extraCss)) {
        // $extraCss puede ser una cadena o un array de rutas
        $list = is_array($extraCss) ? $extraCss : array($extraCss);
        foreach ($list as $css) {
            // Si es URL absoluta (http/https) la usamos tal cual
            if (preg_match('#^https?://#i', $css)) {
                echo "    <link rel=\"stylesheet\" href=\"{$css}\">\n";
                continue;
            }
            // Normalizar: si empieza con '/' añadimos $basePath delante, si no, añadimos $basePath/..
            if (strpos($css, '/') === 0) {
                $href = $basePath . $css;
            } else {
                $href = rtrim($basePath, '/') . '/' . ltrim($css, '/');
            }
            echo "    <link rel=\"stylesheet\" href=\"{$href}\">\n";
        }
    }

    // Permitir insertar HTML arbitrario en el <head> (por ejemplo links CDN)
    if (!empty($extraHeadHtml)) {
        echo $extraHeadHtml . "\n";
    }
    ?>
</head>
<body>
<header>
    <div class="header-content header-flex">
        <div class="logo-container">
            <img src="<?= $basePath ?>/assets/images/logo.png" alt="Logo" class="logo">
        </div>

        <?php
        // Insert role-based navbar inside the header so links appear integrated with logo and profile
        if (session_status() === PHP_SESSION_NONE) {
            @session_start();
        }
        $role = isset($_SESSION['rol']) ? $_SESSION['rol'] : null;
        $navbarPath = __DIR__ . "/../navbars/navbar_" . ($role ? $role : 'estudiante') . ".php";
        if (file_exists($navbarPath)) {
            include_once $navbarPath;
        } else {
            // Fallback to a generic navbar if role-specific not found
            $fallback = __DIR__ . "/../navbars/navbar_estudiante.php";
            if (file_exists($fallback)) include_once $fallback;
        }
        ?>
        <div class="right-controls">
            <button id="menuToggle" class="menu-toggle" aria-label="Mostrar menú">
                <i class="fas fa-bars"></i>
            </button>

            <div class="user-menu">
                <button class="user-toggle" aria-haspopup="true" aria-expanded="false" aria-controls="userMenuContent">
                    <i class="fas fa-user-circle user-icon" aria-hidden="true"></i>
                </button>
                <div id="userMenuContent" class="user-menu-content" role="menu">
                    <a role="menuitem" href="<?= $basePath ?>/pages/shared/mi_cuenta.php"><i class="fas fa-user"></i>Mi Cuenta</a>
                    <a role="menuitem" href="<?= $basePath ?>/pages/auth/logout.php"><i class="fas fa-sign-out-alt"></i>Salir</a>
                </div>
            </div>
        </div>
    </div>
</header>
