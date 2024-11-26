<?php
$title = "Reservas";
$headerTitle = "Reservas";
include '../templates/header.php';
include '../templates/navbar_super_admin.php';
?>

<div class="reservation-container">
    <h2>¿Qué tipo de reserva deseas realizar?</h2>
    <div class="reservation-options">
        <button onclick="window.location.href='cuatrimestre.php'">Reserva por Cuatrimestre</button>
        <button onclick="window.location.href='reserva_clase.php'">Reserva para Actividad o Clase</button>
        <button onclick="window.location.href='reservasEstu.php'">Reserva de Espacio</button>
    </div>
</div>

<?php include '../templates/footer.php'; ?>