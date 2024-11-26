<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado y si tiene el rol de 'profesor'
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] != 'profesor') {
    header('Location: login.php'); // Redirige si no tiene acceso
    exit();
}

// Recupera el nombre del usuario
$nombreUsuario = $_SESSION['nombre'];

// Variables para el header
$title = "Profesor - Panel Principal";
$headerTitle = "Panel del Profesor";

include '../templates/header.php';
include '../templates/navbar_profesor.php';
?>

<div class="container">
    <h2>Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?></h2>
    <div class="card-grid">
        <div class="cardSuperAdmin" onclick="location.href='reserva_clase.php'">
            <i class="fas fa-calendar-check fa-3x"></i>
            <h2>Reservar Clase / Actividad</h2>
        </div>
        <div class="cardSuperAdmin" onclick="location.href='ver_reservas_profesor.php'">
            <i class="fas fa-calendar-alt fa-3x"></i>
            <h2>Administrar Reservas</h2>
        </div>
        <div class="cardSuperAdmin" onclick="location.href='reporte.php'">
            <i class="fas fa-tools fa-3x"></i>
            <h2>Reportar Daños</h2>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>