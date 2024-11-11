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
    // Si no se proporciona un nombre de profesor, usar el del usuario por defecto
    $nombreProfesor = !empty($_POST['nombreProfesor']) ? $_POST['nombreProfesor'] : $nombreProfesorPorDefecto;

    $laboratorioId = $_POST['laboratorio'];

    try {
        // Obtener el correo del profesor
        $queryCorreo = "SELECT correo_institucional FROM usuarios WHERE nombre = :nombreProfesor";
        $stmtCorreo = $pdo->prepare($queryCorreo);
        $stmtCorreo->bindParam(':nombreProfesor', $nombreProfesor);
        $stmtCorreo->execute();
        $correoProfesor = $stmtCorreo->fetchColumn();

        if ($correoProfesor) {
            // Llamar a la función de reserva en la base de datos
            $queryReserva = "SELECT realizar_reserva(:dia, :horaInicio, :horaFinal, :correoProfesor, :laboratorioId)";
            $stmt = $pdo->prepare($queryReserva);
            $stmt->bindParam(':dia', $dia);
            $stmt->bindParam(':horaInicio', $horaInicio);
            $stmt->bindParam(':horaFinal', $horaFinal);
            $stmt->bindParam(':correoProfesor', $correoProfesor);
            $stmt->bindParam(':laboratorioId', $laboratorioId);

            $stmt->execute();

            // Enviar correo de confirmación al profesor
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';  
            $mail->SMTPAuth = true;
            $mail->Username = 'noealto03@gmail.com';
            $mail->Password = 'swro tdsr scpk fqwk';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('noealto03@gmail.com', 'Sistema de Reservas');
            $mail->addAddress($correoProfesor);
            $mail->Subject = 'Reserva de Actividad o Clase';
            $mail->Body = "
                A quien corresponda: 

                Le informamos que se ha confirmado la reserva para la actividad o clase programada. A continuación, encontrará los detalles de la reserva:

                Fecha: $dia
                Hora de inicio: $horaInicio
                Hora de finalización: $horaFinal
                Laboratorio: $laboratorioId

                Le agradecemos su atención y quedamos a su disposición para cualquier consulta adicional.

                Saludos cordiales,
                Sistema de Reservas
                ";

            $mail->send();

            $message = "<div id='confirmation-message' style='background-color: #28a745; color: white; padding: 15px; border-radius: 5px; text-align: center; position: relative;'>Reserva realizada exitosamente y correo enviado al profesor.</div>";
        } else {
            $message = "<div id='confirmation-message' style='background-color: #dc3545; color: white; padding: 15px; border-radius: 5px; text-align: center; position: relative;'>Error: No se encontró el correo del profesor.</div>";
        }
    } catch (PDOException $e) {
        $message = "<div id='confirmation-message' style='background-color: #dc3545; color: white; padding: 15px; border-radius: 5px; text-align: center; position: relative;'>Error al realizar la reserva: " . $e->getMessage() . "</div>";
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
    }, 1000); 
<?php } ?>
</script>
