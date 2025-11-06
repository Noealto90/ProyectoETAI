<?php
session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado y tiene el rol de 'superAdmin'
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] != 'superAdmin') {
    header('Location: ../auth/login.php'); // Redirige si no tiene acceso
    exit();
}

// Conexión a la base de datos
require_once __DIR__ . '/../../config/conexion.php';
$con = new Conexion();
$pdo = $con->getConexion();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $rol = $_POST['rol'];

    if (empty($correo) || empty($rol)) {
        $error = "Por favor, complete todos los campos.";
    } elseif (!preg_match('/@etai\.ac\.cr$/', $correo)) {
        $error = "El correo debe ser un correo institucional (@etai.ac.cr).";
    } else {
        try {
            // Insertar o actualizar el rol asignado
            $sql = "INSERT INTO roles_asignados (correo_institucional, rol) VALUES (:correo, :rol)
                    ON CONFLICT (correo_institucional) DO UPDATE SET rol = EXCLUDED.rol";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':correo', $correo);
            $stmt->bindParam(':rol', $rol);
            $stmt->execute();

            $mensaje_exito = "Rol asignado correctamente.";
        } catch (PDOException $e) {
            $error = "Error al asignar el rol: " . $e->getMessage();
        }
    }
}

// Variables para el header
$title = "Asignar Roles";
$headerTitle = "Asignar Roles";

include_once __DIR__ . '/../../templates/layouts/header.php';
include_once __DIR__ . '/../../templates/navbars/navbar_super_admin.php';
?>

<div class="container">
    <h2>Asignar Roles a Usuarios</h2>
    <form action="asignar_roles.php" method="POST" class="formulario">
        <label for="correo">Correo Institucional:</label>
        <input type="email" id="correo" name="correo" placeholder="correo@etai.ac.cr" required>

        <label for="rol">Rol:</label>
        <select id="rol" name="rol" required>
            <option value="" selected disabled>Seleccione un rol</option>
            <option value="administrador">Administrador</option>
            <option value="profesor">Profesor</option>
            <option value="estudiante">Estudiante</option>
            <option value="superAdmin">Super Admin</option>
        </select>

        <button type="submit" class="button">Asignar Rol</button>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if (isset($mensaje_exito)): ?>
            <p class="success"><?php echo htmlspecialchars($mensaje_exito); ?></p>
        <?php endif; ?>
    </form>
</div>

<?php include_once __DIR__ . '/../../templates/layouts/footer.php'; ?>