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

        // Mensaje de éxito
        //echo "Equipo añadido correctamente.";
        // Mensaje de éxito
        header('Location: add.html');
    } catch (PDOException $e) {
        // Mostrar mensaje de error en caso de fallo
        echo "Error al añadir el equipo: " . $e->getMessage();
    }
}
?>
