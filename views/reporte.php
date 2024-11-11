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

<?php include '../templates/footer.php'; ?>