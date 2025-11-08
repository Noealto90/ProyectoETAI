<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado y tiene el rol de 'estudiante'
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] != 'estudiante') {
    header('Location: ../auth/login.php'); // Redirige si no tiene acceso
    exit();
}

// Recupera el nombre del usuario
$nombreUsuario = $_SESSION['nombre'];

// Variables para el header
$title = "Inicio Estudiante";
$headerTitle = "Inicio Estudiante";

include_once __DIR__ . '/../../templates/layouts/header.php';
include_once __DIR__ . '/../../templates/navbars/navbar_estudiante.php';
?>

<div class="container">
    <div class="welcome">
        <h2>Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?></h2>
    </div>
    <div class="card-grid">
        <div class="card" onclick="location.href='reservasEstu.php'">
            <i class="fas fa-calendar-check fa-3x"></i>
            <h2>Reservar un Espacio</h2>
        </div>
        <div class="card" onclick="location.href='../shared/reporte.php'">
            <i class="fas fa-tools fa-3x"></i>
            <h2>Reporte de Daños</h2>
        </div>
        <div class="card" onclick="location.href='ver_reservas_estudiante.php'">
            <i class="fas fa-calendar-alt fa-3x"></i>
            <h2>Mis reservas</h2>
        </div>
        <div class="card" onclick="location.href='renovar.php'">
            <i class="fas fa-sync-alt fa-3x"></i>
            <h2>Renovar el Espacio</h2>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../templates/layouts/footer.php'; ?>