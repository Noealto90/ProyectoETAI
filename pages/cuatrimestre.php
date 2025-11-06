<?php
// Verifica si la sesión ya está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión solo si no está activa
}

// Verifica si el usuario está autenticado y tiene el rol de 'superAdmin'
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] != 'superAdmin') {
    header('Location: auth/login.php'); // Redirige si no tiene acceso
    exit();
}

// Recupera el nombre del usuario y rol
$nombreUsuario = $_SESSION['nombre'];


require_once __DIR__ . '/../config/conexion.php';

$con = new Conexion();
$pdo = $con->getConexion();

$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
} else {
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


// Si hay una petición AJAX
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];

    // Obtener profesores
    if ($action == 'getProfesores') {
        $sql = "SELECT id, nombre FROM usuarios WHERE rol = 'profesor'";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($profesores) {
            foreach ($profesores as $profesor) {
                echo "<option value='" . $profesor['nombre'] . "'>" . $profesor['nombre'] . "</option>";
            }
        } else {
            echo "<option value=''>No se encontraron profesores</option>";
        }
        exit;
    }
}


// Función para enviar correos
function enviarMensajeCorreo($correoDestino, $mensaje) {
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  
        $mail->SMTPAuth = true;
        $mail->Username = 'noealto03@gmail.com'; 
        $mail->Password = 'swro tdsr scpk fqwk';   
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Configuración del correo
        $mail->setFrom('noealto03@gmail.com', 'Sistema de Reservas');
        $mail->addAddress($correoDestino); 
        $mail->Subject = 'Nueva Reserva Realizada';
        $mail->Body = $mensaje;

        // Enviar el correo
        $mail->send();
    } catch (Exception $e) {
        echo "Error al enviar el mensaje: {$mail->ErrorInfo}";
    }
}


// Ejecuta la función reserva_cuatrimestre
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fecha = $_POST['fecha'];
    $laboratorio_id = $_POST['laboratorio_id'];
    $nombre_encargado = $_POST['profesor'];
    $horaInicio = $_POST['horaInicio'];
    $horaFin = $_POST['horaFin'];

    // Limpiar la tabla cancelados antes de realizar la reserva
    $pdo->exec("DELETE FROM cancelados");

    // Obtener el correo del profesor encargado
    $sqlCorreoProfesor = "SELECT correo_institucional FROM usuarios WHERE nombre = :nombre_encargado AND rol = 'profesor'";
    $stmtCorreoProfesor = $pdo->prepare($sqlCorreoProfesor);
    $stmtCorreoProfesor->bindParam(':nombre_encargado', $nombre_encargado);
    $stmtCorreoProfesor->execute();
    $correoProfesor = $stmtCorreoProfesor->fetchColumn();

    // Obtener el correo del usuario autenticado (superAdmin)
    $nombreUsuario = $_SESSION['nombre'];
    $sqlCorreoUsuario = "SELECT correo_institucional FROM usuarios WHERE nombre = :nombreUsuario";
    $stmtCorreoUsuario = $pdo->prepare($sqlCorreoUsuario);
    $stmtCorreoUsuario->bindParam(':nombreUsuario', $nombreUsuario);
    $stmtCorreoUsuario->execute();
    $correoUsuario = $stmtCorreoUsuario->fetchColumn();

    if ($correoProfesor && $correoUsuario) {
        // Ejecuta la reserva
        $sql = "SELECT reserva_cuatrimestre(:fecha, :laboratorio_id, :nombre_encargado, :horaInicio, :horaFin)";
        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':laboratorio_id', $laboratorio_id, PDO::PARAM_INT);
        $stmt->bindParam(':nombre_encargado', $nombre_encargado);
        $stmt->bindParam(':horaInicio', $horaInicio);
        $stmt->bindParam(':horaFin', $horaFin);

        if ($stmt->execute()) {
            echo "Reserva realizada con éxito";

            // Crear mensaje de la reserva
            $mensaje = "Se ha realizado una nueva reserva con los siguientes detalles:\n\n" .
                       "Fecha: $fecha\n" .
                       "Laboratorio: $laboratorio_id\n" .
                       "Encargado: $nombre_encargado\n" .
                       "Hora de inicio: $horaInicio\n" .
                       "Hora de fin: $horaFin\n";

            // Enviar el correo al profesor encargado
            enviarMensajeCorreo($correoProfesor, $mensaje);

            // Enviar el correo al usuario autenticado
            enviarMensajeCorreo($correoUsuario, $mensaje);

            // Verificar si la tabla cancelados tiene datos
            $sqlCheckCancelados = "SELECT COUNT(*) FROM cancelados";
            $stmtCheckCancelados = $pdo->query($sqlCheckCancelados);
            $cantidadCancelados = $stmtCheckCancelados->fetchColumn();

            if ($cantidadCancelados > 0) {
                // Ejecutar la función para eliminar duplicados
                $pdo->query("SELECT eliminar_duplicados_cancelados()");

                // Obtener los correos de los registros cancelados
                $sqlCorreosCancelados = "SELECT correo FROM cancelados";
                $stmtCorreosCancelados = $pdo->query($sqlCorreosCancelados);
                $cancelados = $stmtCorreosCancelados->fetchAll(PDO::FETCH_ASSOC);

                // Enviar un correo de cancelación a cada registro en cancelados
                foreach ($cancelados as $cancelado) {
                    $correoCancelado = $cancelado['correo'];
                    $mensajeCancelacion = "Su reserva ha sido cancelada, el motivo es Asignacion de una clase.\n\n" .
                                          "Datos de su reserva:\n" .
                                          "Laboratorio: $laboratorio_id\n" .
                                          "Hora de inicio: $horaInicio\n" .
                                          "Fecha: $fecha";

                    enviarMensajeCorreo($correoCancelado, $mensajeCancelacion);
                }
            }

        } else {
            echo "Error al realizar la reserva";
        }
    } else {
        echo "Error: No se encontraron los correos del profesor o del usuario.";
    }
    exit;
}



$title = "Nueva Reserva - Sistema de Reserva de Espacios";
$headerTitle = "Reservas por Cuatrimestre";
$title = "Reservas por Cuatrimestre";
$headerTitle = "Reservas por Cuatrimestre";
// Añadir CSS y scripts específicos para esta página
$extraCss = 'assets/css/cuatrimestre.css';
$extraHeadHtml = '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
include_once __DIR__ . '/../templates/layouts/header.php';
include_once __DIR__ . '/../templates/navbars/navbar_super_admin.php';

// Conexión a la base de datos
// Incluir el archivo CSS externo
// echo '<link rel="stylesheet" href="../assets/css/cuatrimestre.css">';

?>



<!-- Contenido principal de la página -->

<div class="container">
    <div class="form-card">
        <h2>Nueva Reserva</h2>

        <div class="form-group">
            <label for="lab-select">Laboratorio:</label>
            <select id="lab-select" name="laboratorio_id" class="form-control styled-select">
                <option value="1">Laboratorio 1</option>
                <option value="2">Laboratorio 2</option>
            </select>
        </div>

        <div class="form-group">
            <label for="report-date">Fecha de Inicio</label>
            <input type="date" id="report-date" name="fecha" class="form-control" value="<?php echo isset($fecha) ? $fecha : ''; ?>">
        </div>

        <div class="form-group">
            <label for="professor">Profesor</label>
            <select id="professor" name="profesor" required>
                <option value="">Seleccione un profesor</option>
                <!-- Las opciones de profesores serán cargadas aquí con AJAX -->
            </select>
        </div>

        <div class="form-group">
            <label for="start-time">Hora de inicio</label>
            <input type="time" id="start-time" name="horaInicio" required>
        </div>

        <div class="form-group">
            <label for="end-time">Hora de finalización</label>
            <input type="time" id="end-time" name="horaFin" required>
        </div>

        <button class="btn-confirm" onclick="confirmarReserva()">Confirmar Reserva</button>
    </div>
</div>
<?php include_once __DIR__ . '/../templates/layouts/footer.php'; ?>

<script>
    $(document).ready(function() {
        // Cargar profesores
        $.ajax({
            url: 'cuatrimestre.php',
            type: 'GET',
            data: { action: 'getProfesores' },
            success: function(data) {
                $('#professor').append(data);
            },
            error: function() {
                alert('Error al cargar los profesores');
            }
        });
    });

    function confirmarReserva() {
        const fecha = $('#report-date').val();
        const laboratorio_id = $('#lab-select').val();
        const profesor = $('#professor').val();
        const horaInicio = $('#start-time').val();
        const horaFin = $('#end-time').val();

        $.post('cuatrimestre.php', {
            fecha: fecha,
            laboratorio_id: laboratorio_id,
            profesor: profesor,
            horaInicio: horaInicio,
            horaFin: horaFin
        }, function(response) {
            alert(response);
        });
    }
</script>

<?php include_once __DIR__ . '/../templates/layouts/footer.php'; ?>
