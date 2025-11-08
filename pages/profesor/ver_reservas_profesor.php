<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado y tiene el rol de 'profesor'
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] != 'profesor') {
    header('Location: ../auth/login.php'); // Redirige si no tiene acceso
    exit();
}

// Recupera el nombre y correo del usuario (correo se guarda en sesión como 'usuario')
$nombreUsuario = $_SESSION['nombre'];
$correoUsuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;

// Conexión a la base de datos
include_once __DIR__ . '/../../config/conexion.php';  // Asegúrate de que el archivo existe y la variable $pdo esté correctamente definida

$con = new Conexion();
$pdo = $con->getConexion();

// Consulta las reservas del profesor
$sql = "SELECT id, laboratorio_id, espacio_id, nombreEncargado, nombreAcompanante, horaInicio, horaFinal, diaR, activa 
        FROM reservas 
        WHERE (nombreEncargado = :nombreUsuario";
if ($correoUsuario) {
    $sql .= " OR nombreEncargado = :correoUsuario";
}
$sql .= ") AND activa = true";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':nombreUsuario', $nombreUsuario);
if ($correoUsuario) {
    $stmt->bindParam(':correoUsuario', $correoUsuario);
}
$stmt->execute();
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si no se encontraron reservas, obtener algunas filas recientes para depuración
if (empty($reservas)) {
    try {
        $debugSql = "SELECT * FROM reservas WHERE nombreEncargado = :nombreUsuario";
        if ($correoUsuario) {
            $debugSql .= " OR nombreEncargado = :correoUsuario";
        }
        $debugSql .= " ORDER BY id DESC LIMIT 10";

        $dbgStmt = $pdo->prepare($debugSql);
        $dbgStmt->bindParam(':nombreUsuario', $nombreUsuario);
        if ($correoUsuario) {
            $dbgStmt->bindParam(':correoUsuario', $correoUsuario);
        }
        $dbgStmt->execute();
        $debugRows = $dbgStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $debugRows = [];
        error_log('Error debug reservas: ' . $e->getMessage());
    }
}

// Variables para el header
$title = "Ver Reservas - Profesor";
$headerTitle = "Reservas del Profesor";

include_once __DIR__ . '/../../templates/layouts/header.php';
include_once __DIR__ . '/../../templates/navbars/navbar_profesor.php';
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
                        <!-- Acompañante oculto para reservas de profesores -->
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas as $reserva): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reserva['laboratorio_id']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['espacio_id']); ?></td>
                            <td>
                                <?php
                                    // Accept either DB column casing (Postgres folds unquoted identifiers to lowercase)
                                    $rawHi = $reserva['horaInicio'] ?? $reserva['horainicio'] ?? null;
                                    if (!empty($rawHi)) {
                                        $t = DateTime::createFromFormat('H:i:s', $rawHi);
                                        echo $t ? htmlspecialchars($t->format('H:i')) : htmlspecialchars($rawHi);
                                    } else {
                                        echo 'No especificada';
                                    }
                                ?>
                            </td>
                            <td>
                                <?php
                                    $rawHf = $reserva['horaFinal'] ?? $reserva['horafinal'] ?? null;
                                    if (!empty($rawHf)) {
                                        $t2 = DateTime::createFromFormat('H:i:s', $rawHf);
                                        echo $t2 ? htmlspecialchars($t2->format('H:i')) : htmlspecialchars($rawHf);
                                    } else {
                                        echo 'No especificada';
                                    }
                                ?>
                            </td>
                            <td>
                                <?php
                                    $rawDia = $reserva['diaR'] ?? $reserva['diar'] ?? null;
                                    if (!empty($rawDia)) {
                                        $d = DateTime::createFromFormat('Y-m-d', $rawDia);
                                        echo $d ? htmlspecialchars($d->format('d/m/Y')) : htmlspecialchars($rawDia);
                                    } else {
                                        echo 'No especificado';
                                    }
                                ?>
                            </td>
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
            <?php if (isset($debugRows) && !empty($debugRows)): ?>
                <div style="margin-top:16px;padding:12px;border:1px solid #eee;background:#fff;">
                    <h3>Depuración: filas recientes para este usuario</h3>
                    <p>Verifique si las columnas horaInicio, horaFinal o diaR están nulas o con otro valor.</p>
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <thead>
                            <tr>
                                <?php foreach (array_keys($debugRows[0]) as $col): ?>
                                    <th style="border:1px solid #ddd;padding:6px;background:#f9f9f9;"><?php echo htmlspecialchars($col); ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($debugRows as $r): ?>
                                <tr>
                                    <?php foreach ($r as $v): ?>
                                        <td style="border:1px solid #eee;padding:6px;"><?php echo htmlspecialchars((string)$v); ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
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

<?php include_once __DIR__ . '/../../templates/layouts/footer.php'; ?>