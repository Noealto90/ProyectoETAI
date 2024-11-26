<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado y si tiene el rol de 'estudiante'
if (!isset($_SESSION['nombre'])) {
    header('Location: login.php'); // Redirige si no tiene acceso
    exit();
}

// Recupera el nombre del usuario
$nombreUsuario = $_SESSION['nombre'];

// Variables para el header
$title = "Reservas de Laboratorios ETAI";
$headerTitle = "Reservas de Laboratorios ETAI";

include '../templates/header.php';
include '../templates/navbar_estudiante.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="../assets/css/reservas-estudiante.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
    <div class="container">
        <!-- Header se maneja mediante PHP -->
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
                <button class="btn back1-button" onclick="previousStep(1)">Atrás</button>
                <button class="btn continue-button" onclick="nextStep(3)">Continuar</button>
            </div>

        </section>

        <!-- Step 3: Confirm -->
        <section class="step-content confirm-section" id="step3" style="display:none;">
            <h2>Confirmar Reserva</h2>
            <p class="instruction">Por favor confirma los detalles de tu reserva:</p>

            <div class="reservation-details">
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

            <div class="action-buttons">
                <button class="btn back-button" onclick="previousStep(2)">Atrás</button>
                <button class="btn confirm-btn" type="submit">Confirmar Reserva</button>
            </div>


        </section>
    </div>

    <div id="toast"></div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/validacion_uso_compartido.js"></script>
</body>
</html>

<?php include '../templates/footer.php'; ?>
