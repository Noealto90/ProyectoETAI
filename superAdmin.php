<?php
require 'conexion.php'; // Incluye el archivo de conexión

// Verifica la conexión
$con = new Conexion(); // Instancia de la clase conexión
$pdo = $con->getConexion();

$mensaje = "";

// Verifica si la conexión es exitosa
if ($pdo != null) {
    $mensaje = "Conexión exitosa a la base de datos.";
} else {
    $mensaje = "No se pudo establecer la conexión a la base de datos.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Conexión a la Base de Datos</title>
</head>
<body>
    <h1>Estado de la Conexión</h1>
    <p><?php echo $mensaje; // Muestra el mensaje de la conexión ?></p>
</body>
</html>
