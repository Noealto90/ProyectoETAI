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
    // Obtener los datos del formulario
    $tipo = $_POST['tipo']; 
    $codigo = $_POST['codigo'];
    $laboratorio_id = $_POST['nuevo_laboratorio'];

    try {
        // Llama a la función PL/pgSQL que modifica el equipo
        $query = "SELECT modificar_equipo(:tipo, :codigo, :nuevo_lab)";
        $stmt = $pdo->prepare($query);

        // Vincular los parámetros
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':nuevo_lab', $laboratorio_id);

        // Ejecutar la consulta
        $stmt->execute();

        // Redirigir a la página de éxito
        header('Location: edit.php?success=1');
        exit();
    } catch (PDOException $e) {
        // Mostrar mensaje de error en caso de fallo
        $error = "Error al modificar el equipo: " . $e->getMessage();
    }
}

// Variables para el header
$title = "Editar Equipo";
$headerTitle = "Editar Equipo";

include '../templates/header.php';
include '../templates/navbar_super_admin.php';
?>

<div class="container">
    <h2>Editar un equipo existente</h2>
    <?php if (isset($_GET['success'])): ?>
        <p class="success">Equipo modificado correctamente.</p>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form action="edit.php" class="formulario" method="POST">
        <label for="tipo">Tipo de equipo actual:</label>
        <select id="tipo" name="tipo" required>
            <option value="computadora">Computadora</option>
            <option value="mesa">Mesa</option>
            <option value="silla">Silla</option>
        </select>

        <label for="codigo">Código del equipo actual:</label>
        <input type="text" id="codigo" name="codigo" placeholder="Ingrese el código del equipo" required>

        <label for="nuevo_laboratorio">Nuevo laboratorio:</label>
        <select id="nuevo_laboratorio" name="nuevo_laboratorio" required>
            <option value="1">Laboratorio 1</option>
            <option value="2">Laboratorio 2</option>
        </select>

        <button type="submit">Actualizar Equipo</button>
    </form>
</div>

<?php include '../templates/footer.php'; ?>