<?php
$title = "Sistema de Gestión de Equipos";
$headerTitle = "Gestión de Equipos de Laboratorio";
include 'templates/header.php';
include 'templates/navbar.php';
?>
<div class="container">
    <h2>Seleccione una opción del menú para gestionar los equipos</h2>

    <!-- Cards alineadas en la misma fila -->
    <div class="card-grid">
        <div class="card" onclick="location.href='views/add.php'">
            <i class="fas fa-plus-circle fa-3x"></i>
            <h2>Añadir Equipos</h2>
        </div>
        <div class="card" onclick="location.href='views/ver_equipo.php'">
            <i class="fas fa-eye fa-3x"></i>
            <h2>Ver Equipos</h2>
        </div>
        <div class="card" onclick="location.href='views/edit.php'">
            <i class="fas fa-edit fa-3x"></i>
            <h2>Editar Equipos</h2>
        </div>
        <div class="card" onclick="location.href='views/delete.php'">
            <i class="fas fa-trash-alt fa-3x"></i>
            <h2>Eliminar Equipos</h2>
        </div>
    </div>
</div>
<?php include 'templates/footer.php'; ?>