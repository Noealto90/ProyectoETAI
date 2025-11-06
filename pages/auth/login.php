<?php
session_start();
// Calcular ruta base para assets
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
if (strpos($scriptName, '/pages/') !== false) {
    $parts = explode('/pages/', $scriptName, 2);
    $basePath = $parts[0];
} else {
    $basePath = dirname($scriptName);
}
if ($basePath === '/' || $basePath === '\\' || $basePath === '.' || $basePath === '') {
    $basePath = '';
}

require_once __DIR__ . '/../../config/conexion.php'; // Incluye el archivo de conexión

// Verifica si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    // Validación básica para evitar campos vacíos
    if (empty($correo) || empty($password)) {
        $error = "Por favor, complete todos los campos.";
    } else {
        $con = new Conexion();
        $pdo = $con->getConexion();

        if ($pdo) {
            // Consulta el correo en la base de datos
            $sql = "SELECT * FROM usuarios WHERE correo_institucional = :correo";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifica si el usuario existe y si la contraseña es correcta
            if ($usuario && password_verify($password, $usuario['contrasena'])) {
                // Guarda el nombre del usuario en la sesión
                $_SESSION['usuario'] = $usuario['correo_institucional'];
                $_SESSION['nombre'] = $usuario['nombre']; // Guardamos el nombre
                $_SESSION['rol'] = $usuario['rol']; // Almacena el rol en la sesión
                // Redirige al usuario basado en el rol
                if ($usuario['rol'] == 'estudiante') {
                    header('Location: ../estudiante/index_estudiante.php');
                } elseif ($usuario['rol'] == 'superAdmin') {
                    header('Location: ../super_admin/super_admin.php'); // Cambia a PHP para usar la sesión
                } elseif ($usuario['rol'] == 'administrador') {
                    header('Location: ../admin/index_administrador.php'); // Cambia a PHP para usar la sesión
                } elseif ($usuario['rol'] == 'profesor') {
                    header('Location: ../profesor/index_profesor.php'); // Cambia a PHP para usar la sesión
                } else {
                    $error = "Rol no válido.";
                }
                exit(); // Detener el script después de la redirección
            } else {
                $error = "Correo o contraseña incorrectos.";
            }
        } else {
            $error = "Error de conexión a la base de datos.";
        }
    }
}

?>
<link rel="stylesheet" href="<?= $basePath ?>/assets/css/login.css">
<div class="login-main-container">
    <div class="login-container">
        <!-- Logo (puedes cambiar el src con el logo real que deseas) -->
        <img src="<?= $basePath ?>/assets/images/logo.png" alt="Logo">

        <!-- Encabezado con fondo gris -->
        <div class="login-header">
            <h2>Inicio de Sesión</h2>
        </div>

        <!-- Formulario de inicio de sesión -->
        <form action="login.php" method="POST">
            <input type="email" name="correo" placeholder="Correo Institucional" required>
            <input type="password" name="password" placeholder="Contraseña" required>

            <?php if (isset($error)): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>

            <a href="#">¿Olvidaste tu contraseña?</a>
            <a href="register.php" class="create-account">Crear cuenta</a>

            <input type="submit" value="Iniciar Sesión" class="btn-login">
        </form>
    </div>
</div>