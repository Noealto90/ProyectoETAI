<?php
session_start();
header('Content-Type: application/json');

require 'conexion.php'; // Incluir el archivo de conexión a la base de datos

// Crear la instancia de conexión y obtener el PDO
$con = new Conexion();
$pdo = $con->getConexion();

if ($pdo === null) {
    die(json_encode(['success' => false, 'message' => 'Error al conectarse a la base de datos']));
}

// Verificar si se han enviado datos de reserva
$reservaData = json_decode(file_get_contents('php://input'), true);

if ($reservaData) {
    $lab = $reservaData['lab'];
    $date = $reservaData['date'];
    $time = $reservaData['time'];

    // Aquí puedes guardar estos datos en la base de datos o procesarlos
    // Simulación de respuesta de éxito
    $response = [
        'success' => true, 
        'message' => 'Reserva procesada correctamente',
        'reserva' => $reservaData
    ];

    echo json_encode($response);
} else {
    echo json_encode(['success' => false, 'message' => 'No se recibieron datos de reserva']);
}
?>
