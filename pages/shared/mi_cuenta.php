<?php
session_start();
$title = "Mi Cuenta";
$headerTitle = "Mi Cuenta";
include_once __DIR__ . '/../../templates/layouts/header.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['nombre']) || !isset($_SESSION['rol'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Incluir la barra de navegación correspondiente según el rol del usuario
switch ($_SESSION['rol']) {
    case 'superAdmin':
        include_once __DIR__ . '/../../templates/navbars/navbar_super_admin.php';
        break;
    case 'estudiante':
        include_once __DIR__ . '/../../templates/navbars/navbar_estudiante.php';
        break;
    case 'profesor':
        include_once __DIR__ . '/../../templates/navbars/navbar_profesor.php';
        break;
    case 'administrativo':
        include_once __DIR__ . '/../../templates/navbars/navbar_administrador.php';
        break;
    default:
        header('Location: ../auth/login.php');
        exit();
}

// Conexión a la base de datos
require_once __DIR__ . '/../../config/conexion.php';

$con = new Conexion();
$pdo = $con->getConexion();

// Obtener los datos del usuario
$correo = $_SESSION['usuario'];
$query = "SELECT nombre, correo_institucional FROM usuarios WHERE correo_institucional = :correo";
$stmt = $pdo->prepare($query);
$stmt->execute([':correo' => $correo]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Evitar acceso a esta página si la sesión ha sido destruida
if (session_status() === PHP_SESSION_NONE) {
    header('Location: ../auth/login.php');
    exit();
}

// Actualizar los datos del usuario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];

    $query = "UPDATE usuarios SET nombre = :nombre, correo_institucional = :correo WHERE correo_institucional = :correo_original";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        ':nombre' => $nombre,
        ':correo' => $correo,
        ':correo_original' => $_SESSION['usuario']
    ]);

    // Actualizar la sesión con el nuevo correo
    $_SESSION['usuario'] = $correo;
    $_SESSION['nombre'] = $nombre;

    $mensaje_exito = "Datos actualizados correctamente.";
}
?>

<div class="container">
    <h2>Mi Cuenta</h2>
    <?php if (isset($mensaje_exito)): ?>
        <p class="success"><?php echo htmlspecialchars($mensaje_exito); ?></p>
    <?php endif; ?>
    <form action="mi_cuenta.php" method="POST" class="formulario">
        <label for="nombre">Nombre Completo:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>

        <label for="correo">Correo Institucional:</label>
        <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario['correo_institucional']); ?>" required>

        <button type="submit">Actualizar Datos</button>
    </form>
</div>

<?php include_once __DIR__ . '/../../templates/layouts/footer.php'; ?>