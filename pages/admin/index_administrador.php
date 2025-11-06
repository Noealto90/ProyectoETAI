<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado y si tiene el rol de 'administrador'
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] != 'administrador') {
    header('Location: login.php'); // Redirige si no tiene acceso
    exit();
}

// Recupera el nombre del usuario
$nombreUsuario = $_SESSION['nombre'];

// Variables para el header
$title = "Administrador - Panel Principal";
$headerTitle = "Panel del Administrador";

include_once __DIR__ . '/../../templates/layouts/header.php';
include_once __DIR__ . '/../../templates/navbars/navbar_administrador.php';
?>

<div class="container">
    <h2>Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?></h2>
    <div class="card-grid">
        <div class="cardSuperAdmin" onclick="location.href='reserva_clase.php'">
            <i class="fas fa-calendar-check fa-3x"></i>
            <h2>Reservar Actividad</h2>
        </div>
        <div class="cardSuperAdmin" onclick="location.href='ver_reportes.php'">
            <i class="fas fa-tools fa-3x"></i>
            <h2>Ver Reportes de Daños</h2>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../templates/layouts/footer.php'; ?>