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
    $codigo = $_POST['codigo'];
    $laboratorio_id = $_POST['laboratorio'];
    $tipo = $_POST['tipo'];

    try {
        // Llama a la función PL/pgSQL que inserta en la tabla adecuada según el tipo de equipo
        $query = "SELECT insertar_equipo(:tipo, :codigo, :laboratorio)";
        $stmt = $pdo->prepare($query);

        // Vincular los parámetros
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':laboratorio', $laboratorio_id);
        
        // Ejecutar la consulta
        $stmt->execute();

        // Redirigir a la página de éxito
        header('Location: add.php?success=1');
        exit();
    } catch (PDOException $e) {
        // Mostrar mensaje de error en caso de fallo
        $error = "Error al añadir el equipo: " . $e->getMessage();
    }
}

// Variables para el header
$title = "Añadir Equipo";
$headerTitle = "Añadir Equipo";

// Incluir el header, navbar y footer
include '../templates/header.php';
include '../templates/navbar.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="../assets/css/add.css"> <!-- Enlace al archivo CSS -->
</head>
<body>
<div class="container">
    <h2>Añadir un nuevo equipo</h2>
    <?php if (isset($_GET['success'])): ?>
        <p class="success">Equipo añadido correctamente.</p>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form action="add.php" method="POST" class="formulario">
        <label for="codigo">Código del equipo:</label>
        <input type="text" id="codigo" name="codigo" placeholder="Ingrese el código del equipo" required>
        <label for="tipo">Tipo de equipo:</label>
        <select id="tipo" name="tipo" required>
            <option value="computadora">Computadora</option>
            <option value="mesa">Mesa</option>
            <option value="silla">Silla</option>
        </select>
        <label for="laboratorio">Laboratorio:</label>
        <select id="laboratorio" name="laboratorio" required>
            <option value="1">Laboratorio 1</option>
            <option value="2">Laboratorio 2</option>
        </select>
        <button type="submit">Añadir Equipo</button>
    </form>
</div>
<?php include '../templates/footer.php'; ?>
</body>
</html>