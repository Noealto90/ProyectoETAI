<?php
session_start();
$title = "Reservas por Cuatrimestre";
$headerTitle = "Reservas por Cuatrimestre";
include '../templates/header.php';

// Verificar si el usuario está autenticado y tiene el rol adecuado
if (!isset($_SESSION['nombre']) || !isset($_SESSION['rol']) || $_SESSION['rol'] != 'superAdmin') {
    header('Location: login.php');
    exit();
}

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
<link rel="stylesheet" href="../assets/css/cuatrimestre.css">
<div class="container">
    <h2>Nueva Reserva</h2>
    <form id="cuatrimestre-form" class="formulario" action="procesar_cuatrimestre.php" method="POST">
        <label for="cuatrimestre">Cuatrimestre</label>
        <select id="cuatrimestre" name="cuatrimestre" required>
            <option value="">Seleccione un cuatrimestre</option>
            <!-- Las opciones de cuatrimestres serán cargadas aquí con AJAX -->
        </select>

        <label for="day">Día de la semana</label>
        <select id="day" name="day" required>
            <option value="">Seleccione un día</option>
            <option value="Lunes">Lunes</option>
            <option value="Martes">Martes</option>
            <option value="Miércoles">Miércoles</option>
            <option value="Jueves">Jueves</option>
            <option value="Viernes">Viernes</option>
        </select>

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