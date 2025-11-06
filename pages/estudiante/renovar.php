<?php
session_start();
$title = "Renovar Espacio - Sistema de Gestión de Equipos";
$headerTitle = "Renovar Espacio";
// Añadir CSS específico para esta página
$extraCss = 'assets/css/renovar.css';
include_once __DIR__ . '/../../templates/layouts/header.php';
include_once __DIR__ . '/../../templates/navbars/navbar_estudiante.php';
?>

<div class="container">
    <h2>Confirme el equipo a renovar</h2>
    
    <div class="form-group">
        <label for="equipo">Equipo Actualmente Asignado:</label>
        <input type="text" id="equipo" name="equipo" value="Equipo 12" readonly>
    </div>
    
    <div class="form-group">
        <input type="submit" value="Confirmar Renovación" class="button">
    </div>
</div>
<?php include_once __DIR__ . '/../../templates/layouts/footer.php'; ?>