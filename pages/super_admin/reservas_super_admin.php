<?php
$title = "Reservas";
$headerTitle = "Reservas";
include_once __DIR__ . '/../../templates/layouts/header.php';
include_once __DIR__ . '/../../templates/navbars/navbar_super_admin.php';
?>

<div class="reservation-container">
    <h2>¿Qué tipo de reserva deseas realizar?</h2>
    <div class="reservation-options">
    <button onclick="window.location.href='cuatrimestre.php'">Reserva por Cuatrimestre</button>
    <?php require_once __DIR__ . '/../../config/conexion.php'; ?>
        <button onclick="window.location.href='reserva_clase.php'">Reserva para Actividad o Clase</button>
        <button onclick="window.location.href='reservasEstu.php'">Reserva de Espacio</button>
    </div>
</div>

<?php include_once __DIR__ . '/../../templates/layouts/footer.php'; ?>