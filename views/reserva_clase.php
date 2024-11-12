<?php 
session_start();
$title = "Reservar Actividad o Clase";
$headerTitle = "Reservar Actividad o Clase";

require '../includes/conexion_estudiante.php';
require 'C:\Users\HP\Desktop\ProyectoDeAdminETAI\ReservasETAI\assets\PHPMailer\Exception.php';
require 'C:\Users\HP\Desktop\ProyectoDeAdminETAI\ReservasETAI\assets\PHPMailer\PHPMailer.php';
require 'C:\Users\HP\Desktop\ProyectoDeAdminETAI\ReservasETAI\assets\PHPMailer\SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";
$nombreProfesorPorDefecto = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    global $pdo;

    $dia = $_POST['dia'];
    $horaInicio = $_POST['horaInicio'];
    $horaFinal = $_POST['horaFinal'];
    $nombreProfesor = !empty($_POST['nombreProfesor']) ? $_POST['nombreProfesor'] : $nombreProfesorPorDefecto;
    $laboratorioId = $_POST['laboratorio'];

    try {
        $pdo->query("DELETE FROM cancelados");

        $queryCorreo = "SELECT correo_institucional FROM usuarios WHERE nombre = :nombreProfesor";
        $stmtCorreo = $pdo->prepare($queryCorreo);
        $stmtCorreo->bindParam(':nombreProfesor', $nombreProfesor);
        $stmtCorreo->execute();
        $correoProfesor = $stmtCorreo->fetchColumn();

        if ($correoProfesor) {
            $queryReserva = "SELECT realizar_reserva(:dia, :horaInicio, :horaFinal, :correoProfesor, :laboratorioId) AS resultado";
            $stmt = $pdo->prepare($queryReserva);
            $stmt->bindParam(':dia', $dia);
            $stmt->bindParam(':horaInicio', $horaInicio);
            $stmt->bindParam(':horaFinal', $horaFinal);
            $stmt->bindParam(':correoProfesor', $correoProfesor);
            $stmt->bindParam(':laboratorioId', $laboratorioId);
            $stmt->execute();
            $resultado = $stmt->fetchColumn();

            if ($resultado == 1) {
                $message = "<div id='confirmation-message' style='background-color: #dc3545; color: white; padding: 15px; border-radius: 5px; text-align: center;'>No se puede realizar la reserva, hay una reserva de mayor o igual jerarquía.</div>";
            } else {
                enviarMensajeCorreo($correoProfesor, "Reserva de Actividad o Clase", "
                    A quien corresponda: 

                    Le informamos que se ha confirmado la reserva para la actividad o clase programada. A continuación, encontrará los detalles de la reserva:

                    Fecha: $dia
                    Hora de inicio: $horaInicio
                    Hora de finalizacion: $horaFinal
                    Laboratorio: $laboratorioId

                    Saludos cordiales,
                    Sistema de Reservas
                ");

                $message = "<div id='confirmation-message' style='background-color: #28a745; color: white; padding: 15px; border-radius: 5px; text-align: center;'>Reserva realizada exitosamente y correo enviado al profesor.</div>";

                $sqlCheckCancelados = "SELECT COUNT(*) FROM cancelados";
                $stmtCheckCancelados = $pdo->query($sqlCheckCancelados);
                $cantidadCancelados = $stmtCheckCancelados->fetchColumn();

                if ($cantidadCancelados > 0) {
                    $pdo->query("SELECT eliminar_duplicados_cancelados()");
                    $sqlCorreosCancelados = "SELECT correo FROM cancelados";
                    $stmtCorreosCancelados = $pdo->query($sqlCorreosCancelados);
                    $cancelados = $stmtCorreosCancelados->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($cancelados as $cancelado) {
                        $correoCancelado = $cancelado['correo'];
                        $mensajeCancelacion = "
                            Su reserva ha sido cancelada debido a la asignación de una clase.

                            Detalles de la reserva cancelada:
                            Laboratorio: $laboratorioId
                            Hora de inicio: $horaInicio
                            Hora de finalizacion: $horaFinal
                            Fecha: $dia
                        ";
                        enviarMensajeCorreo($correoCancelado, "Cancelacion de Reserva", $mensajeCancelacion);
                    }
                }
            }
        } else {
            $message = "<div id='confirmation-message' style='background-color: #dc3545; color: white; padding: 15px; border-radius: 5px; text-align: center;'>Error: No se encontró el correo del profesor.</div>";
        }
    } catch (PDOException $e) {
        $message = "<div id='confirmation-message' style='background-color: #dc3545; color: white; padding: 15px; border-radius: 5px; text-align: center;'>Error al realizar la reserva: " . $e->getMessage() . "</div>";
    }
}

function enviarMensajeCorreo($correo, $asunto, $cuerpo) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  
        $mail->SMTPAuth = true;
        $mail->Username = 'noealto03@gmail.com';
        $mail->Password = 'swro tdsr scpk fqwk';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('noealto03@gmail.com', 'Sistema de Reservas');
        $mail->addAddress($correo);
        $mail->Subject = $asunto;
        $mail->Body = $cuerpo;
        $mail->send();
    } catch (Exception $e) {
        echo "Error al enviar correo: {$mail->ErrorInfo}";
    }
}

include '../templates/header.php';
include '../templates/navbar_profesor.php';
?>

<div class="container">
    <?php if (!empty($message)) echo $message; ?>

    <h2>Nueva Reserva</h2>

    <form id="reserva-form" class="formulario" action="reserva_clase.php" method="POST">
        <label for="dia">Fecha</label>
        <input type="date" id="dia" name="dia" required>

        <label for="nombreProfesor">Nombre del profesor o administrador</label>
        <input type="text" id="nombreProfesor" name="nombreProfesor" placeholder="Ingrese el nombre del profesor/administrador" value="<?= htmlspecialchars($nombreProfesorPorDefecto) ?>" required>

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

<script>
<?php if (!empty($message)) { ?>
    setTimeout(function() {
        var messageElement = document.getElementById('confirmation-message');
        if (messageElement) {
            messageElement.style.display = 'none';
        }
    }, 3000); 
<?php } ?>
</script>
