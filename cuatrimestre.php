<?php
require 'conexion.php'; // Incluir el archivo de conexión a la base de datos

$con = new Conexion();
$pdo = $con->getConexion();

if ($pdo) {
    // Consulta para obtener los profesores
    $sql = "SELECT id, nombre FROM usuarios WHERE rol = 'profesor'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($profesores) {
        // Crear opciones en el combobox
        foreach ($profesores as $profesor) {
            echo "<option value='" . $profesor['id'] . "'>" . $profesor['nombre'] . "</option>";
        }
    } else {
        echo "<option value=''>No se encontraron profesores</option>";
    }
} else {
    echo "<option value=''>Error al cargar profesores</option>";
}
?>
