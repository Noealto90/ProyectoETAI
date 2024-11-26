<?php
session_start();
$title = "Renovar Espacio - Sistema de Gestión de Equipos";
$headerTitle = "Renovar Espacio";
include '../templates/header.php';
include '../templates/navbar_estudiante.php';
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
<link rel="stylesheet" href="../assets/css/renovar.css">

<?php include '../templates/footer.php'; ?>