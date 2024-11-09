<?php
// ver_reservas_estudiante.php

// Verifica si la sesión ya está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión solo si no está activa
}

// Verifica si el usuario está autenticado y tiene el rol de 'estudiante'
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] != 'estudiante') {
    header('Location: login.php'); // Redirige si no tiene acceso
    exit();
}

// Recupera el nombre del usuario y rol
$nombreUsuario = $_SESSION['nombre'];

// Conexión a la base de datos
include '../includes/conexion.php';  // Asegúrate de que el archivo existe y la variable $pdo esté correctamente definida

$con = new Conexion();
$pdo = $con->getConexion();

// Consulta las reservas del responsable
$sql = "SELECT id, laboratorio_id, espacio_id, nombreEncargado, nombreAcompanante, horaInicio, horaFinal, diaR, activa 
        FROM reservas 
        WHERE nombreEncargado = :nombreUsuario 
        AND activa = true";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':nombreUsuario', $nombreUsuario);
$stmt->execute();
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Variables para el header
$title = "Ver Reservas - Estudiante";
$headerTitle = "Reservas del Estudiante";

include '../templates/header.php';
include '../templates/navbar.php';
?>

<div class="container">
    <h2>Listado de Reservas</h2>
    <div class="reservas">
        <?php if (!empty($reservas)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Laboratorio</th>
                        <th>Espacio</th>
                        <th>Hora Inicio</th>
                        <th>Hora Final</th>
                        <th>Día</th>
                        <th>Acompañante</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas as $reserva): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reserva['laboratorio_id']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['espacio_id']); ?></td>
                            <td><?php echo isset($reserva['horainicio']) ? htmlspecialchars($reserva['horainicio']) : 'No especificada'; ?></td>
                            <td><?php echo isset($reserva['horafinal']) ? htmlspecialchars($reserva['horafinal']) : 'No especificada'; ?></td>
                            <td><?php echo isset($reserva['diar']) ? htmlspecialchars($reserva['diar']) : 'No especificado'; ?></td>
                            <td><?php echo !empty($reserva['nombreacompanante']) ? htmlspecialchars($reserva['nombreacompanante']) : 'N/A'; ?></td>
                            <td><?php echo $reserva['activa'] ? 'Activa' : 'Inactiva'; ?></td>
                            <td class="cancel-icon">
                                <!-- Icono para cancelar la reserva con confirmación -->
                                <form action="cancelar_reserva.php" method="POST" onsubmit="return confirmarCancelacion()">
                                    <input type="hidden" name="id" value="<?php echo $reserva['id']; ?>">
                                    <button type="submit" class="cancel-btn">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No se encontraron reservas para este usuario.</p>
        <?php endif; ?>
    </div>
</div>

<?php if (isset($_GET['mensaje'])): ?>
    <div id="notification" class="notification <?php echo $_GET['mensaje'] == 'exito' ? '' : 'error'; ?>">
        <?php
        switch ($_GET['mensaje']) {
            case 'exito':
                echo 'Reserva cancelada con éxito.';
                break;
            case 'no_encontrada':
                echo 'No se encontró la reserva o ya estaba cancelada.';
                break;
            case 'error':
                echo 'Hubo un error al cancelar la reserva.';
                break;
            case 'falta_id':
                echo 'No se proporcionó un ID de reserva.';
                break;
        }
        ?>
    </div>
    <script>
        // Mostrar la notificación
        const notification = document.getElementById('notification');
        notification.classList.add('show');
        
        // Ocultar la notificación después de 3 segundos
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    </script>
<?php endif; ?>

<?php include '../templates/footer.php'; ?>