<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado y si tiene el rol de 'superAdmin'
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] != 'superAdmin') {
    header('Location: ../auth/login.php'); // Redirige si no tiene acceso
    exit();
}

// Recupera el nombre del usuario
$nombreUsuario = $_SESSION['nombre'];

// Variables para el header
$title = "Super Administrador - Panel Principal";
$headerTitle = "Panel del Super Administrador";

include_once __DIR__ . '/../../templates/layouts/header.php';
include_once __DIR__ . '/../../templates/navbars/navbar_super_admin.php';
?>

<div class="container">
    <h2>Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?></h2>
    <div class="card-grid">
        <div class="cardSuperAdmin" onclick="location.href='reporte_superadmin.php'">
            <i class="fas fa-chart-line fa-3x"></i>
            <h2>Generar Informes</h2>
        </div>
        <div class="cardSuperAdmin" onclick="location.href='gestion_equipos.php'">
            <i class="fas fa-cogs fa-3x"></i>
            <h2>Gestión de Equipos</h2>
        </div>
        <div class="cardSuperAdmin" onclick="location.href='reservas_super_admin.php'">
            <i class="fas fa-calendar-check fa-3x"></i>
            <h2>Realizar Reservas</h2>
        </div>
    </div>
    <div class="card-grid">
    <div class="cardSuperAdmin" onclick="location.href='reporte.php'">
        <i class="fas fa-tools fa-3x"></i>
        <h2>Reportes de Daños</h2>
    </div>

    <div class="cardSuperAdmin" onclick="location.href='ver_reservas_super_admin.php'">
        <i class="fas fa-calendar-alt fa-3x"></i>
        <h2>Administrar Reservas</h2>
    </div>
    <div class="cardSuperAdmin" onclick="location.href='asignar_roles.php'">
        <i class="fas fa-user-cog fa-3x"></i>
        <h2>Asignar Roles</h2>
    </div>
</div>
</div>

<?php include_once __DIR__ . '/../../templates/layouts/footer.php'; ?>