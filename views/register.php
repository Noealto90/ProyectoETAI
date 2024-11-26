<?php
session_start();
require '../includes/conexion.php'; // Incluye el archivo de conexión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    if (empty($correo) || empty($password) || empty($confirm_password) || empty($nombre)) {
        $error = "Por favor, complete todos los campos.";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } elseif (!preg_match('/@etai\.ac\.cr$/', $correo)) {
        $error = "El correo debe ser un correo institucional (@etai.ac.cr).";
    } else {
        $con = new Conexion();
        $pdo = $con->getConexion();
        // Hashea la contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Verificar si el correo tiene un rol asignado
        $sql = "SELECT rol FROM roles_asignados WHERE correo_institucional = :correo";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':correo', $correo);
        $stmt->execute();
        $rol_asignado = $stmt->fetchColumn();

        // Si no hay un rol asignado, usar 'estudiante' por defecto
        $rol = $rol_asignado ? $rol_asignado : 'estudiante';

        // Llama a la función almacenada agregar_persona para insertar el usuario
        $sql = "SELECT agregar_persona(:nombre, :correo, :password, :rol)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':rol', $rol);
        if ($stmt->execute()) {
            $resultado = $stmt->fetchColumn();
            if ($resultado == 'Usuario agregado correctamente.') {
                header('Location: login.php');
                exit();
            } else {
                $error = $resultado;
            }
        } else {
            $error = "Error al registrar el usuario.";
        }
    }
}
?>
<link rel="stylesheet" href="../assets/css/register.css">
<div class="register-main-container">
    <div class="register-container">
        <!-- Logo -->
        <img src="../assets/images/logo.png" alt="Logo de la Institución">

        <!-- Formulario de registro -->
        <div class="register-header">
            <h2>Registro de Usuario</h2>
        </div>
        <form action="register.php" method="POST">
            <input type="text" name="nombre" placeholder="Nombre Completo" value="<?php echo isset($nombre) ? htmlspecialchars($nombre) : ''; ?>" required>
            <input type="email" name="correo" placeholder="Correo Institucional" value="<?php echo isset($correo) ? htmlspecialchars($correo) : ''; ?>" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <input type="password" name="confirm_password" placeholder="Confirmar contraseña" required>

            <?php if (isset($error)): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <a href="login.php" class="login-link">Iniciar sesión</a>

            <!-- Botón de siguiente -->
            <input type="submit" value="Registrar" class="btn-register">
        </form>
    </div>
</div>