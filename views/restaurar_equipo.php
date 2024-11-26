<?php
session_start();

// Verificar si el usuario está autenticado y tiene el rol adecuado
if (!isset($_SESSION['nombre']) || !isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['superAdmin', 'administrador'])) {
    header('Location: login.php');
    exit();
}

// Conexión a la base de datos
require '../includes/conexion.php';
$con = new Conexion();
$pdo = $con->getConexion();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $codigo = $_POST['codigo'];

    try {
        // Restaurar el estado del equipo a "disponible"
        $query = "UPDATE equipos SET estado = 'disponible' WHERE codigo = :codigo";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':codigo' => $codigo]);

        // Redirigir a la página de reportes con un mensaje de éxito
        header('Location: ver_reportes.php?mensaje=restaurado');
        exit();
    } catch (PDOException $e) {
        // Manejar errores al restaurar el equipo
        echo "<p>Error al restaurar el equipo: " . $e->getMessage() . "</p>";
    }
}
?>