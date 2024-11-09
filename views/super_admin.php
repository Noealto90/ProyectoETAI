<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado y si tiene el rol de 'superAdmin'
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] != 'superAdmin') {
    header('Location: login.php'); // Redirige si no tiene acceso
    exit();
}

// Recupera el nombre del usuario
$nombreUsuario = $_SESSION['nombre'];

// Variables para el header
$title = "Super Administrador - Panel Principal";
$headerTitle = "Panel del Super Administrador";

include '../templates/header.php';
include '../templates/navbar.php';
?>

<div class="container">
    <h2>Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?></h2>
    <div class="card-grid1">
        <div class="cardSuperAdmin" onclick="location.href='#'">
            <i class="fas fa-chart-line fa-3x"></i>
            <h2>Generar Informes</h2>
        </div>
        <div class="cardSuperAdmin" onclick="location.href='../index.php'">
            <i class="fas fa-cogs fa-3x"></i>
            <h2>Gestión de Laboratorios</h2>
        </div>
        <div class="cardSuperAdmin" onclick="location.href='reservas_super_admin.php'">
            <i class="fas fa-calendar-check fa-3x"></i>
            <h2>Realizar Reservas</h2>
        </div>
        <div class="cardSuperAdmin" onclick="location.href='#'">
            <i class="fas fa-tools fa-3x"></i>
            <h2>Reportes de Daños</h2>
        </div>
    </div>
    <div class="card-grid2">
        <div class="cardSuperAdmin" onclick="location.href='ver_reservas_super_admin.php'">
            <i class="fas fa-calendar-alt fa-3x"></i>
            <h2>Administrar Reservas</h2>
        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>