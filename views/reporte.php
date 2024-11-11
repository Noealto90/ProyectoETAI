<?php
session_start();
$title = "Reporte de Daños - Sistema de Gestión de Equipos";
$headerTitle = "Reporte de Daños";
include '../templates/header.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['nombre']) || !isset($_SESSION['rol'])) {
    header('Location: login.php');
    exit();
}

// Incluir la barra de navegación correspondiente según el rol del usuario
if ($_SESSION['rol'] == 'superAdmin') {
    include '../templates/navbar_super_admin.php';
} elseif ($_SESSION['rol'] == 'estudiante') {
    include '../templates/navbar_estudiante.php';
} elseif ($_SESSION['rol'] == 'profesor') {
    include '../templates/navbar_profesor.php';
} else {
    header('Location: login.php');
    exit();
}

// Conexión a la base de datos
require '../includes/conexion.php';
$con = new Conexion();
$pdo = $con->getConexion();

// Variables para almacenar errores y mensajes
$errores = [];
$mensaje_exito = "";

// Función para enviar el correo
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../assets/PHPMailer/Exception.php';
require '../assets/PHPMailer/PHPMailer.php';
require '../assets/PHPMailer/SMTP.php';

function enviarCorreo($codigo, $descripcion, $responsable, $pdo) {
    $mail = new PHPMailer(true);

    try {
        // Consultar los correos de los administradores
        $query = "SELECT correo_institucional FROM usuarios WHERE rol IN ('administrador', 'superAdmin')";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $administradores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agregar cada correo de los administradores como destinatario
        foreach ($administradores as $admin) {
            $mail->addAddress($admin['correo_institucional']);
        }

        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'noealto03@gmail.com';
        $mail->Password = 'swro tdsr scpk fqwk';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Configuración del correo
        $mail->setFrom('noealto03@gmail.com', 'Sistema de Reportes');
        $mail->Subject = 'Reporte de equipo afectado';

        // Mensaje del correo
        $mensaje = "A quien corresponda:\n\n";
        $mensaje .= "Por medio de la presente, se le informa que el equipo $codigo ha sido reportado como dañado. A continuación, los detalles de este reporte:\n\n";
        $mensaje .= "- Responsable del reporte: $responsable\n";
        $mensaje .= "- Descripción del problema: $descripcion\n\n";
        $mensaje .= "Gracias por su atención a este asunto.\n\n";
        $mensaje .= "Atentamente,\nEl equipo de soporte";

        $mail->Body = $mensaje;

        // Enviar el correo
        $mail->send();
    } catch (Exception $e) {
        echo "Error al enviar el mensaje: {$mail->ErrorInfo}";
    }
}

// Validar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $codigo = $_POST['codigo'];
    $descripcion = $_POST['descripcion'];
    $reportado_por = $_SESSION['nombre'];

    // Validación de campos
    if (empty($codigo)) {
        $errores[] = "Por favor ingrese el código del equipo.";
    }
    if (empty($descripcion)) {
        $errores[] = "Por favor ingrese una descripción del problema.";
    }

    // Si no hay errores, insertar el reporte en la base de datos y bloquear el equipo
    if (empty($errores)) {
        try {
            $pdo->beginTransaction();

            // Insertar el reporte de daño
            $query = "INSERT INTO reportes_daños (codigo_equipo, descripcion, reportado_por) VALUES (:codigo, :descripcion, :reportado_por)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':codigo' => $codigo, ':descripcion' => $descripcion, ':reportado_por' => $reportado_por]);

            // Bloquear el equipo
            $query = "UPDATE equipos SET estado = 'bloqueado' WHERE codigo = :codigo";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':codigo' => $codigo]);

            $pdo->commit();
            $mensaje_exito = "Reporte enviado y equipo bloqueado correctamente.";

            // Enviar correo con los detalles
            enviarCorreo($codigo, $descripcion, $reportado_por, $pdo);

        } catch (PDOException $e) {
            $pdo->rollBack();
            $errores[] = "Error al enviar el reporte: " . $e->getMessage();
        }
    }
}
?>

<div class="container">
    <h2>Reporte de Daños</h2>
    <?php if (!empty($errores)): ?>
        <div class="error">
            <?php foreach ($errores as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($mensaje_exito)): ?>
    <div class="success">
        <p><?php echo htmlspecialchars($mensaje_exito); ?></p>
    </div>
<?php endif; ?>
    <form action="reporte.php" method="POST" class="formulario">
        <label for="codigo">Código del Equipo:</label>
        <input type="text" id="codigo" name="codigo" placeholder="Ingrese el código" required>
        <label for="descripcion">Descripción del Problema:</label>
        <textarea id="descripcion" name="descripcion" placeholder="Describa el problema" required></textarea>
        <button type="submit">Enviar Reporte</button>
    </form>
</div>

<script>
    // Función para mostrar el mensaje de éxito temporalmente
    window.onload = function() {
        var mensajeExito = document.querySelector('.success');
        if (mensajeExito) {
            // Mostrar el mensaje
            mensajeExito.style.display = 'block';
            
            setTimeout(function() {
                mensajeExito.style.display = 'none';
            }, 2000); 
        }
    }
</script>


<?php include '../templates/footer.php'; ?>
