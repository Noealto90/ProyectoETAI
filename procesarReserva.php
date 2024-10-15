<?php
// Incluir el archivo de conexión a la base de datos
include 'conexionEstu.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Definir $pdo como global
    global $pdo;

    // Obtener los datos del formulario
    $dia = $_POST['dia'];
    $horaInicio = $_POST['horaInicio'];
    $horaFinal = $_POST['horaFinal'];
    $correoProfesor = $_POST['correoProfesor'];
    $laboratorioId = $_POST['laboratorio'];

    try {
        // Llamar a la función realizar_reserva en PostgreSQL
        $query = "SELECT realizar_reserva(:dia, :horaInicio, :horaFinal, :correoProfesor, :laboratorioId)";
        $stmt = $pdo->prepare($query);  // Cambiamos $conexion a $pdo
        $stmt->bindParam(':dia', $dia);
        $stmt->bindParam(':horaInicio', $horaInicio);
        $stmt->bindParam(':horaFinal', $horaFinal);
        $stmt->bindParam(':correoProfesor', $correoProfesor);
        $stmt->bindParam(':laboratorioId', $laboratorioId);
        
        $stmt->execute();
        
        // Mensaje de confirmación
        echo "<p>Reserva realizada exitosamente.</p>";
    } catch (PDOException $e) {
        // Manejar errores al realizar la reserva
        echo "<p>Error al realizar la reserva: " . $e->getMessage() . "</p>";
    }
}
?>