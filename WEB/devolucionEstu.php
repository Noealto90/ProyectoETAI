<?php
require 'conexionEstu.php';  // Conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['laboratorio'])) {
    // Obtener los espacios ocupados del laboratorio seleccionado
    $laboratorio_id = $_GET['laboratorio'];

    // Consulta para obtener los espacios ocupados
    $query = "SELECT espacio_id FROM reservas WHERE laboratorio_id = :laboratorio_id AND activa = TRUE";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':laboratorio_id' => $laboratorio_id]);
    $espacios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Enviar los espacios ocupados en formato JSON
    echo json_encode(['espacios' => $espacios]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario
    $laboratorio_id = $_POST['laboratorio'];
    $espacio_id = $_POST['espacio'];

    try {
        // Actualizar la tabla de reservas (activa = false)
        $queryUpdateReservas = "UPDATE reservas SET activa = FALSE WHERE laboratorio_id = :laboratorio_id AND espacio_id = :espacio_id AND activa = TRUE";
        $stmt = $pdo->prepare($queryUpdateReservas);
        $stmt->execute([
            ':laboratorio_id' => $laboratorio_id,
            ':espacio_id' => $espacio_id
        ]);

        // Actualizar la tabla de espacios (activa = true)
        $queryUpdateEspacios = "UPDATE espacios SET activa = TRUE WHERE laboratorio_id = :laboratorio_id AND espacio_id = :espacio_id";
        $stmt = $pdo->prepare($queryUpdateEspacios);
        $stmt->execute([
            ':laboratorio_id' => $laboratorio_id,
            ':espacio_id' => $espacio_id
        ]);

        echo "Devolución confirmada";
    } catch (PDOException $e) {
        echo "Error al realizar la devolución: " . $e->getMessage();
    }
}
?>
