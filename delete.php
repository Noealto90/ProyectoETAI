<?php
session_start();
require 'conexion.php'; // Incluir el archivo de conexión a la base de datos

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
            header("Location: delete.html?status=success");
            exit();
        } catch (PDOException $e) {
            // Manejar errores de base de datos
            echo '<p>Error al eliminar el equipo: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    } else {
        // Manejar la entrada vacía
        echo '<p>Por favor, ingrese el código del equipo a eliminar.</p>';
    }
} else {
    // Redirigir si no se accede mediante POST
    header("Location: delete.html");
    exit();
}
?>
