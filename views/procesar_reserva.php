<?php
session_start();
require '../includes/conexion.php';
$con = new Conexion();
$pdo = $con->getConexion();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dia = $_POST['dia'];
    $correoProfesor = $_POST['correoProfesor'];
    $horaInicio = $_POST['horaInicio'];
    $horaFinal = $_POST['horaFinal'];
    $laboratorioId = $_POST['laboratorio'];

    // Obtener el nombre del profesor desde la sesión
    $nombreEncargado = $_SESSION['nombre'];

    try {
        $query = "INSERT INTO reservas (diaR, nombreEncargado, horaInicio, laboratorio_id) VALUES (:dia, :nombreEncargado, :horaInicio, :laboratorioId)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':dia' => $dia,
            ':nombreEncargado' => $nombreEncargado,
            ':horaInicio' => $horaInicio,
            ':laboratorioId' => $laboratorioId
        ]);

        // Redirigir a reserva_clase.php con un mensaje de éxito
        header('Location: reserva_clase.php?mensaje=creado');
        exit();
    } catch (PDOException $e) {
        echo "<p>Error al crear la reserva: " . $e->getMessage() . "</p>";
    }
}
?>