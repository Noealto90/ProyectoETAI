<?php
// Verifica si la sesión ya está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión solo si no está activa
}

// Verifica si el usuario está autenticado y tiene el rol de 'superAdmin'
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] != 'superAdmin') {
    header('Location: login.php'); // Redirige si no tiene acceso
    exit();
}

// Recupera el nombre del usuario y rol
$nombreUsuario = $_SESSION['nombre'];


$title = "Informe de Uso de Laboratorios - Sistema de Gestión de Espacios";
$headerTitle = "Informe de Uso de Laboratorios";
include '../templates/header.php';
include '../templates/navbar_super_admin.php';

// Conexión a la base de datos
require '../includes/conexion.php'; 

$con = new Conexion();
$pdo = $con->getConexion();

// Incluir el archivo CSS externo
echo '<link rel="stylesheet" href="../assets/css/reporte_superadmin.css">';

$reservas = [];

// Verificar si se ha seleccionado una fecha
if (isset($_GET['fecha']) && isset($_GET['laboratorio'])) {
    $fecha = $_GET['fecha'];
    $laboratorio = $_GET['laboratorio'];

    // Forzar el formato de la fecha a YYYY-MM-DD
    $fecha = date('Y-m-d', strtotime($fecha));

    // Construir la consulta SQL con filtro de laboratorio
    $query = "
        SELECT 
            l.nombre AS laboratorio,
            r.espacio_id,
            r.nombreEncargado AS encargado,
            r.nombreAcompanante AS acompanante,
            r.horaInicio,
            r.horaFinal
        FROM reservas r
        JOIN laboratorios l ON r.laboratorio_id = l.id
        WHERE r.diaR = :fecha";

    if ($laboratorio != 'ambos') {
        $query .= " AND r.laboratorio_id = :laboratorio";
    }

    $query .= " ORDER BY l.nombre, r.horaInicio";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':fecha', $fecha);

    if ($laboratorio != 'ambos') {
        $stmt->bindParam(':laboratorio', $laboratorio);
    }

    // Ejecutar la consulta y verificar resultados
    if ($stmt->execute()) {
        $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "Error en la consulta: ";
        print_r($stmt->errorInfo());
    }
}
?>

<div class="container">
    <h2>Informe Detallado del Uso de Laboratorios</h2>

    <!-- Contenedor para el calendario, el selector de laboratorio y el botón en línea -->
    <div class="form-group" style="display: flex; align-items: flex-start; gap: 9px;">
        <label for="report-date">Selecciona una fecha:</label>
        <input type="date" id="report-date" name="fecha" class="form-control" value="<?php echo isset($fecha) ? $fecha : ''; ?>">

        <label for="lab-select">Laboratorio:</label>
        <select id="lab-select" name="laboratorio" class="form-control styled-select">
            <option value="ambos" <?php if (isset($laboratorio) && $laboratorio == 'ambos') echo 'selected'; ?>>Ambos</option>
            <option value="1" <?php if (isset($laboratorio) && $laboratorio == '1') echo 'selected'; ?>>Laboratorio 1</option>
            <option value="2" <?php if (isset($laboratorio) && $laboratorio == '2') echo 'selected'; ?>>Laboratorio 2</option>
        </select>



        <button onclick="mostrarTabla()" class="button btn-confirm">Ver informes</button>
    </div>

    <!-- Botón de descarga de PDF, siempre visible -->
    <div class="form-group" id="download-button">
        <a href="generar_pdf.php?fecha=<?php echo isset($fecha) ? $fecha : ''; ?>&laboratorio=<?php echo isset($laboratorio) ? $laboratorio : ''; ?>" class="button">Descargar PDF</a>
    </div>

    <!-- Tabla con los datos del informe -->
    <table class="table">
        <thead>
            <tr>
                <th>Espacio</th>
                <th>Encargado</th>
                <th>Acompañante</th>
                <th>Hora Inicio</th>
                <th>Hora Final</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $laboratorioActual = null;
            if (!empty($reservas)) : 
                foreach ($reservas as $reserva):
                    // Si se seleccionan ambos laboratorios, mostrar el nombre del laboratorio como separador
                    if ($laboratorio == 'ambos' && $laboratorioActual != $reserva['laboratorio']) {
                        $laboratorioActual = $reserva['laboratorio'];
                        echo '<tr><td colspan="5"><strong>Laboratorio: ' . htmlspecialchars($laboratorioActual) . '</strong></td></tr>';
                    }
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($reserva['espacio_id']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['encargado']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['acompanante']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['horainicio']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['horafinal']); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5">No hay datos disponibles para la fecha y laboratorio seleccionados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>



<script>
function mostrarTabla() {
    const reportDate = document.getElementById('report-date').value;
    const labSelect = document.getElementById('lab-select').value;

    if (!reportDate) {
        alert("Por favor, selecciona una fecha.");
        return;
    }

    window.location.href = `reporte_superadmin.php?fecha=${reportDate}&laboratorio=${labSelect}`;
}
</script>


<?php include '../templates/footer.php'; ?>
