<?php
session_start();
require '../includes/conexion.php'; // Incluir el archivo de conexión a la base de datos

// Crear la instancia de conexión y obtener el PDO
$con = new Conexion();
$pdo = $con->getConexion(); // Ahora $pdo tiene la conexión a la base de datos

if ($pdo === null) {
    die("Error al conectarse a la base de datos");
}

// Verifica si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = $_POST['codigo'] ?? '';
    $tipo = $_POST['tipo'];

    if ($codigo != '') {
        try {
            // Preparar la consulta para eliminar el equipo
            $query = "SELECT borrar_equipo (:tipo, :codigo)";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':codigo', $codigo);
            $stmt->execute();

            // Redirigir al usuario con un mensaje de éxito
            header("Location: delete.php?status=success");
            exit();
        } catch (PDOException $e) {
            // Manejar errores de base de datos
            $error = 'Error al eliminar el equipo: ' . htmlspecialchars($e->getMessage());
        }
    } else {
        // Manejar la entrada vacía
        $error = 'Por favor, ingrese el código del equipo a eliminar.';
    }
}

// Variables para el header
$title = "Eliminar Equipo";
$headerTitle = "Eliminar Equipo";

include '../templates/header.php';
include '../templates/navbar.php';
?>

<div class="container">
    <h2>Eliminar un equipo</h2>
    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <p class="success">Equipo eliminado correctamente.</p>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form action="delete.php" class="formulario" method="POST">
        <label for="codigo">Código del equipo:</label>
        <input type="text" id="codigo" name="codigo" placeholder="Ingrese el código del equipo" required>

        <label for="tipo">Tipo de equipo:</label>
        <select id="tipo" name="tipo" required>
            <option value="computadora">Computadora</option>
            <option value="mesa">Mesa</option>
            <option value="silla">Silla</option>
        </select>

        <button type="submit">Eliminar Equipo</button>
    </form>
</div>

<?php include '../templates/footer.php'; ?>