<?php
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../assets/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../../assets/PHPMailer/SMTP.php';
require_once __DIR__ . '/../../assets/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];

    if (empty($correo)) {
        $error = "Por favor, ingrese su correo electrónico.";
    } else {
        $con = new Conexion();
        $pdo = $con->getConexion();

        if ($pdo) {
            $sql = "SELECT * FROM usuarios WHERE correo_institucional = :correo";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                $codigo = rand(100000, 999999);

                $sql = "UPDATE usuarios SET codigo_recuperacion = :codigo WHERE correo_institucional = :correo";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':codigo', $codigo);
                $stmt->bindParam(':correo', $correo);
                $stmt->execute();

                $mail = new PHPMailer(true);

                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.example.com'; // Cambiar por el host SMTP real
                    $mail->SMTPAuth = true;
                    $mail->Username = 'tu_correo@example.com'; // Cambiar por el correo real
                    $mail->Password = 'tu_contraseña'; // Cambiar por la contraseña real
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('tu_correo@example.com', 'Recuperación de contraseña');
                    $mail->addAddress($correo);

                    $mail->isHTML(true);
                    $mail->Subject = 'Código de recuperación';
                    $mail->Body = "<p>Su código de recuperación es: <strong>$codigo</strong></p>";

                    $mail->send();
                    $success = "Se ha enviado un código de recuperación a su correo electrónico.";
                } catch (Exception $e) {
                    $error = "No se pudo enviar el correo. Error: {$mail->ErrorInfo}";
                }
            } else {
                $error = "El correo electrónico no está registrado.";
            }
        } else {
            $error = "Error de conexión a la base de datos.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="../../assets/css/login.css">
</head>
<body>
<div class="login-main-container">
    <div class="login-container">
        <h2>Recuperar Contraseña</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        <form action="forgot_password.php" method="POST">
            <input type="email" name="correo" placeholder="Correo Institucional" required>
            <input type="submit" value="Enviar Código" class="btn-login">
        </form>
    </div>
</div>
</body>
</html>