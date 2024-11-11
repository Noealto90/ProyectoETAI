<?php
session_start();
$title = "Reservar Actividad";
$headerTitle = "Reservar Actividad";
include '../templates/header.php';
include '../templates/navbar_administrador.php';

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

        <label for="correoProfesor">Correo del Administrador</label>
        <input type="email" id="correoProfesor" name="correoProfesor" placeholder="administrador@institucion.com" required>

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

<div id="toast" class="toast"></div>

<script>
    // Mostrar el toast si hay un mensaje de creación
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('mensaje') && urlParams.get('mensaje') === 'creado') {
        showToast('La reserva ha sido creada correctamente.');
        // Eliminar el parámetro de la URL después de mostrar el mensaje
        setTimeout(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        }, 3000);
    }

    function showToast(message) {
        const toast = document.getElementById('toast');
        toast.className = 'toast show';
        toast.innerHTML = message;

        setTimeout(() => {
            toast.className = toast.className.replace('show', '');
        }, 3000);
    }
</script>

<?php include '../templates/footer.php'; ?>