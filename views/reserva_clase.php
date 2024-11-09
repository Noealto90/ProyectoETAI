<?php
session_start();
$title = "Reservar Actividad o Clase";
$headerTitle = "Reservar Actividad o Clase";
include '../templates/header.php';
include '../templates/navbar.php';
?>

<div class="container">
    
        <h2>Nueva Reserva</h2>
        <form id="reserva-form" class="formulario" action="procesar_reserva.php" method="POST">
            <label for="dia">Fecha</label>
            <input type="date" id="dia" name="dia" required>

            <label for="correoProfesor">Correo del profesor</label>
            <input type="email" id="correoProfesor" name="correoProfesor" placeholder="profesor@institucion.com" required>

            <label for="horaInicio">Hora de inicio</label>
            <input type="time" id="horaInicio" name="horaInicio" required>

            <label for="horaFinal">Hora de finalización</label>
            <input type="time" id="horaFinal" name="horaFinal" required>

            <label for="laboratorio">Laboratorio</label>
            <select id="laboratorio" name="laboratorio" required>
                <option value="1">Laboratorio 1</option>
                <option value="2">Laboratorio 2</option>
            </select>

            <button type="submit" class="button">Confirmar Reserva</button>
        </form>
</div>

<?php include '../templates/footer.php'; ?>