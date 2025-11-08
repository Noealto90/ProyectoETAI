<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado y si tiene el rol de 'profesor'
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] != 'profesor') {
    header('Location: ../auth/login.php'); // Redirige al login si no tiene acceso
    exit();
}

// Recupera el nombre del usuario
$nombreUsuario = $_SESSION['nombre'];

// Variables para el header
$title = "Profesor - Panel Principal";
$headerTitle = "Panel del Profesor";

include_once __DIR__ . '/../../templates/layouts/header.php';
include_once __DIR__ . '/../../templates/navbars/navbar_profesor.php';

// Evitar acceso si la sesión ha sido destruida
if (session_status() === PHP_SESSION_NONE) {
    header('Location: ../auth/login.php');
    exit();
}
?>

<div class="container">
    <h2>Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?></h2>
    <div class="card-grid">
        <div class="cardSuperAdmin" onclick="location.href='reserva_clase.php'">
            <i class="fas fa-calendar-check fa-3x"></i>
            <h2>Reservar Lab</h2>
        </div>
        <div class="cardSuperAdmin" onclick="location.href='ver_reservas_profesor.php'">
            <i class="fas fa-calendar-alt fa-3x"></i>
            <h2>Administrar Reservas</h2>
        </div>
        <div class="cardSuperAdmin" onclick="location.href='../shared/reporte.php'">
            <i class="fas fa-tools fa-3x"></i>
            <h2>Reportar Daños</h2>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../templates/layouts/footer.php'; ?>