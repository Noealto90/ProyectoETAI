<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado y tiene algún rol
if (!isset($_SESSION['nombre']) || !isset($_SESSION['rol'])) {
    // Redirigir al formulario de login dentro del proyecto
    header('Location: pages/auth/login.php'); // Redirige si no tiene acceso
    exit();
}

// Redirige al usuario basado en el rol
switch ($_SESSION['rol']) {
    case 'superAdmin':
        header('Location: pages/super_admin/super_admin.php');
        break;
    case 'estudiante':
        header('Location: pages/estudiante/index_estudiante.php');
        break;
    case 'profesor':
        header('Location: pages/profesor/index_profesor.php'); // Asegúrate de que esta página exista
        break;
    case 'administrativo':
        header('Location: pages/admin/index_administrador.php'); // Asegúrate de que esta página exista
        break;
    default:
        header('Location: pages/auth/login.php'); // Redirige si el rol no es válido
        break;
}
exit();
?>