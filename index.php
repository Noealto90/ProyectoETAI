<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado y tiene algún rol
if (!isset($_SESSION['nombre']) || !isset($_SESSION['rol'])) {
    header('Location: views/login.php'); // Redirige si no tiene acceso
    exit();
}

// Redirige al usuario basado en el rol
switch ($_SESSION['rol']) {
    case 'superAdmin':
        header('Location: views/super_admin.php');
        break;
    case 'estudiante':
        header('Location: views/index_estudiante.php');
        break;
    case 'profesor':
        header('Location: views/index_profesor.php'); // Asegúrate de que esta página exista
        break;
    case 'administrativo':
        header('Location: views/index_administrador.php'); // Asegúrate de que esta página exista
        break;
    default:
        header('Location: views/login.php'); // Redirige si el rol no es válido
        break;
}
exit();
?>