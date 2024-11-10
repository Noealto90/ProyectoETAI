<?php
session_start();
$title = "Reservar Actividad o Clase";
$headerTitle = "Reservar Actividad o Clase";
include '../templates/header.php';
include '../templates/navbar_super_admin.php';

// Conexión a la base de datos
require '../includes/conexion.php';
$con = new Conexion();
$pdo = $con->getConexion();

// Obtener los laboratorios de la base de datos
$query = "SELECT id, nombre FROM laboratorios";
$stmt = $pdo->prepare($query);
$stmt->execute();
$laboratorios = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            <option value="" selected disabled>Seleccione un laboratorio</option>
            <?php foreach ($laboratorios as $laboratorio): ?>
                <option value="<?php echo htmlspecialchars($laboratorio['id']); ?>">
                    <?php echo htmlspecialchars($laboratorio['nombre']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" class="button">Confirmar Reserva</button>
    </form>
</div>

<?php include '../templates/footer.php'; ?>