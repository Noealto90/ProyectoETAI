<?php
session_start();
$title = "Reservas de Laboratorios ETAI";
$headerTitle = "Reservas de Laboratorios ETAI";
include '../templates/header.php';
include '../templates/navbar.php';
?>

<div class="container">
    <!-- Progress Bar -->
    <div class="progress-container">
        <div class="progress-bar">
            <div class="step completed">
                <span class="circle">1</span>
                <p>Seleccionar Fecha</p>
            </div>
            <div class="step">
                <span class="circle">2</span>
                <p>Seleccionar Espacio</p>
            </div>
            <div class="step">
                <span class="circle">3</span>
                <p>Confirmar</p>
            </div>
        </div>
    </div>
    
    <!-- Step 1: Select Date -->
    <section class="step-content" id="step1">
        <!-- Calendario a la izquierda -->
        <div class="calendar-container">
            <div id="reservation-calendar"></div>
        </div>
        
        <!-- Selector de hora a la derecha -->
        <div class="time-container">
            <label for="reservation-time">Seleccionar hora:</label>
            <input type="time" id="reservation-time" name="reservation-time" required>
        </div>

        <button type="button" onclick="nextStep(2)">Continuar</button>
    </section>

    <!-- Step 2: Select Lab and Desk -->
    <section class="step-content1" id="step2" style="display:none;">
        <h2>Selecciona tu Laboratorio</h2>
        <div class="lab-selector">
            <select id="lab-select" onchange="selectLab(this.value)">
                <option value="" disabled selected>Selecciona un laboratorio</option>
                <option value="1">Laboratorio #1</option>
                <option value="2">Laboratorio #2</option>
            </select>
        </div>

        <div class="workspace-container">
            <h2>Selecciona tu Espacio de Trabajo</h2>
            <div class="workspace-grid" id="workspace-grid">
                <!-- Los espacios se generarán dinámicamente mediante JavaScript -->
            </div>
        </div>

        <div class="step-2-buttons">
            <button class="back-button" onclick="previousStep(1)">Atrás</button>
            <button class="button" onclick="nextStep(3)">Continuar</button>
        </div>
    </section>

    <!-- Step 3: Confirm -->
    <section class="step-content confirm-section" id="step3" style="display:none;">
        <h2>Confirmar Reserva</h2>
        <p class="instruction">Por favor confirma los detalles de tu reserva:</p>

        <div class="reservation-details">
            <!-- Nuevo detalle para el laboratorio -->
            <div class="detail-item">
                <span class="detail-label">Laboratorio:</span>
                <span class="detail-value" id="confirm-lab">No seleccionado</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Fecha:</span>
                <span class="detail-value" id="confirm-date">No seleccionada</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Hora:</span>
                <span class="detail-value" id="confirm-time">No seleccionada</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Espacio:</span>
                <span class="detail-value" id="confirm-desk">No seleccionado</span>
            </div>
        </div>

        <!-- Botón para añadir compañero -->
        <div class="add-companion">
            <button id="add-companion-btn" class="btn companion-btn">Añadir compañero</button>
            <div id="companion-input" style="display:none;">
                <label for="companion-email">Correo del compañero:</label>
                <input type="email" id="companion-email" name="companion-email" placeholder="Ingresa el correo del compañero">
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="action-buttons">
            <button class="btn confirm-btn" type="submit">Confirmar Reserva</button>
            <button class="back-button" onclick="previousStep(2)">Atrás</button>
        </div>
    </section>
</div>

<div id="toast"></div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="../assets/css/reservas-estudiante.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="../assets/js/script.js"></script>
<script src="../assets/js/validacion_uso_compartido.js"></script>

<?php include '../templates/footer.php'; ?>

<?php
require '../includes/conexion.php'; // Incluir el archivo de conexión a la base de datos

$con = new Conexion();
$pdo = $con->getConexion();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (is_null($input)) {
        die(json_encode(['success' => false, 'message' => 'No se recibieron datos']));
    }

    try {
        $pdo->query("SELECT activar_espacios();");

        $fecha = $input['date'] ?? '2024-10-15';
        $hora = $input['time'] ?? '12:00:00';
        $stmtDesactivar = $pdo->prepare("SELECT desactivar_espacios_por_reservas(:fecha, :hora);");
        $stmtDesactivar->execute([':fecha' => $fecha, ':hora' => $hora]);

        if (isset($input['lab'])) {
            $laboratorio_id = $input['lab'];
            $query = "SELECT espacio_id, activa FROM espacios WHERE laboratorio_id = :laboratorio_id ORDER BY espacio_id ASC";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':laboratorio_id' => $laboratorio_id]);
            $espacios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'espacios' => $espacios]);
        }
    

    if (isset($input['lab']) && isset($input['date']) && isset($input['time']) && isset($input['desk'])) {
        $laboratorio_id = (int) $input['lab'];  
        $espacio_id = (int) $input['desk'];     
        $fecha = $input['date'];                
        $horaInicio = $input['time'];           
        $nombreEncargado = $input['encargado']; 
        $nombreAcompanante = $input['companion'] ?? null;  
        $activa = $input['activa'];
    
        try {
            $queryCheck = "SELECT COUNT(*) FROM reservas 
                           WHERE laboratorio_id = :laboratorio_id 
                           AND espacio_id = :espacio_id 
                           AND diaR = :fecha 
                           AND horaInicio = :horaInicio";
    
            $stmtCheck = $pdo->prepare($queryCheck);
            $stmtCheck->execute([
                ':laboratorio_id' => $laboratorio_id,
                ':espacio_id' => $espacio_id,
                ':fecha' => $fecha,
                ':horaInicio' => $horaInicio
            ]);
    
            $count = $stmtCheck->fetchColumn();
    
            if ($count > 0) {
                echo json_encode(['success' => false, 'message' => 'Ya existe una reserva para esta fecha y hora.']);
                exit;
            }
    
            $query = "INSERT INTO reservas (laboratorio_id, espacio_id, nombreEncargado, nombreAcompanante, horaInicio, diaR, activa) 
                      VALUES (:laboratorio_id, :espacio_id, :nombreEncargado, :nombreAcompanante, :horaInicio, :fecha, :activa)";
    
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':laboratorio_id' => $laboratorio_id,
                ':espacio_id' => $espacio_id,
                ':nombreEncargado' => $nombreEncargado,
                ':nombreAcompanante' => $nombreAcompanante,
                ':horaInicio' => $horaInicio,
                ':fecha' => $fecha,
                ':activa' => $activa
            ]);

            $queryUpdate = "UPDATE espacios SET activa = FALSE 
                            WHERE laboratorio_id = :laboratorio_id 
                            AND espacio_id = :espacio_id";
            $stmtUpdate = $pdo->prepare($queryUpdate);
            $stmtUpdate->execute([
                ':laboratorio_id' => $laboratorio_id,
                ':espacio_id' => $espacio_id
            ]);

            echo json_encode(['success' => true, 'message' => 'Reserva confirmada y espacio actualizado']);
        } catch (PDOException $e) {
            error_log('Error de PDO: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al realizar la reserva: ' . $e->getMessage()]);
        }
    }} catch (PDOException $e) {
        error_log('Error de PDO: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error en la operación: ' . $e->getMessage()]);
    }
}
?>