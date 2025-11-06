<?php
// Incluir la conexión a la base de datos
include 'db_connection.php';

// Obtener los datos del formulario
$codigo = $_POST['codigo'];
$laboratorio_id = $_POST['laboratorio_id'];
$tipo = $_POST['tipo'];

try {
    // Actualizar el equipo en la base de datos
    $query = "UPDATE equipos SET laboratorio_id = :laboratorio_id, tipo = :tipo WHERE codigo = :codigo";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':laboratorio_id', $laboratorio_id);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':codigo', $codigo);

    $stmt->execute();
    echo "Equipo actualizado correctamente.";
} catch (PDOException $e) {
    echo "Error al actualizar el equipo: " . $e->getMessage();
}
?>
