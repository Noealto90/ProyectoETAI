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
    $tipo = $_POST['tipo']; 
    $codigo = $_POST['codigo'];
    $laboratorio_id = $_POST['nuevo_laboratorio'];

    try {
    // Obtener detalles del equipo
    $query = "SELECT modificar_equipo1(:tipo, :codigo, :nuevo_lab)";
    $stmt = $pdo->prepare($query);

    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':codigo', $codigo);
    $stmt->bindParam(':nuevo_lab', $laboratorio_id);

    $stmt->execute();
    $equipo = $stmt->fetch();


    header('Location: edit.html');
    } catch (PDOException $e) {
        // Mostrar mensaje de error en caso de fallo
        echo "Error al modificar el equipo: " . $e->getMessage();
    }
}
?>

