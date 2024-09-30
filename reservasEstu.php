<?php
header('Content-Type: application/json');

// Simulación de los escritorios ocupados
$escritoriosOcupados = [
    ['lab' => 1, 'desk' => 10],  // Laboratorio 1, Escritorio 10
    ['lab' => 1, 'desk' => 5],   // Laboratorio 1, Escritorio 5
    ['lab' => 2, 'desk' => 8],   // Laboratorio 2, Escritorio 8
    ['lab' => 2, 'desk' => 9]    // Laboratorio 2, Escritorio 9
];

// Verificar si se han enviado datos de reserva con POST
$reservaData = json_decode(file_get_contents('php://input'), true);

if ($reservaData) {
    
    // Si se recibieron datos de reserva, los guardamos en variables
    $lab = $reservaData['lab'];
    $date = $reservaData['date'];
    $time = $reservaData['time'];

    // Aquí puedes guardar estos datos en una base de datos o procesarlos
    // Simulación de guardar los datos y devolver una respuesta de éxito
    $response = [
        'success' => true, 
        'message' => 'Reserva guardada correctamente',
        'reserva' => $reservaData
    ];
    
    echo json_encode($response);
} else {
    // Si no hay datos de reserva, devolver la lista de escritorios ocupados
    echo json_encode($escritoriosOcupados);
}


?>
