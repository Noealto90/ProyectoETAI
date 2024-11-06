<?php
require 'conexionEstu.php';  // Incluye tu archivo de conexión

// Verificamos si es una solicitud de tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Leemos los datos enviados desde el cliente (JavaScript)
    $input = json_decode(file_get_contents('php://input'), true);

    // Verificamos si los datos se recibieron correctamente
    if (is_null($input)) {
        die(json_encode(['success' => false, 'message' => 'No se recibieron datos']));
    }

    try {
        // Ejecutar primero activar_espacios()
        $pdo->query("SELECT activar_espacios();");

        // Ejecutar desactivar_espacios_por_reservas con la fecha y hora recibidas
        $fecha = $input['date'] ?? '2024-10-15';
        $hora = $input['time'] ?? '12:00:00';
        $stmtDesactivar = $pdo->prepare("SELECT desactivar_espacios_por_reservas(:fecha, :hora);");
        $stmtDesactivar->execute([':fecha' => $fecha, ':hora' => $hora]);

        if (isset($input['lab'])) {
            $laboratorio_id = $input['lab'];
            $query = "SELECT espacio_id, activa FROM espacios WHERE laboratorio_id = :laboratorio_id ORDER BY espacio_id ASC";
            $stmt = $pdo->prepare($query);
            $stmt->execute([':laboratorio_id' => $laboratorio_id]);
            $espacios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'espacios' => $espacios]);
        }
    

    if (isset($input['lab']) && isset($input['date']) && isset($input['time']) && isset($input['desk'])) {
        $laboratorio_id = (int) $input['lab'];  
        $espacio_id = (int) $input['desk'];     
        $fecha = $input['date'];                
        $horaInicio = $input['time'];           
        $nombreEncargado = $input['encargado']; 
        $nombreAcompanante = $input['companion'] ?? null;  
        $activa = $input['activa'];
    
        try {
            // Verificar si ya existe una reserva con los mismos parámetros
            $queryCheck = "SELECT COUNT(*) FROM reservas 
                           WHERE laboratorio_id = :laboratorio_id 
                           AND espacio_id = :espacio_id 
                           AND diaR = :fecha 
                           AND horaInicio = :horaInicio";
    
            $stmtCheck = $pdo->prepare($queryCheck);
            $stmtCheck->execute([
                ':laboratorio_id' => $laboratorio_id,
                ':espacio_id' => $espacio_id,
                ':fecha' => $fecha,
                ':horaInicio' => $horaInicio
            ]);
    
            $count = $stmtCheck->fetchColumn();
    
            if ($count > 0) {
                echo json_encode(['success' => false, 'message' => 'Ya existe una reserva para esta fecha y hora.']);
                exit;
            }
    
            // Insertamos los datos en la tabla reservas
            $query = "INSERT INTO reservas (laboratorio_id, espacio_id, nombreEncargado, nombreAcompanante, horaInicio, diaR, activa) 
                      VALUES (:laboratorio_id, :espacio_id, :nombreEncargado, :nombreAcompanante, :horaInicio, :fecha, :activa)";
    
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':laboratorio_id' => $laboratorio_id,
                ':espacio_id' => $espacio_id,
                ':nombreEncargado' => $nombreEncargado,
                ':nombreAcompanante' => $nombreAcompanante,
                ':horaInicio' => $horaInicio,
                ':fecha' => $fecha,
                ':activa' => $activa
            ]);

            // Actualizar el estado del espacio a inactivo
            $queryUpdate = "UPDATE espacios SET activa = FALSE 
                            WHERE laboratorio_id = :laboratorio_id 
                            AND espacio_id = :espacio_id";
            $stmtUpdate = $pdo->prepare($queryUpdate);
            $stmtUpdate->execute([
                ':laboratorio_id' => $laboratorio_id,
                ':espacio_id' => $espacio_id
            ]);

            // Confirmación de la inserción y actualización
            echo json_encode(['success' => true, 'message' => 'Reserva confirmada y espacio actualizado']);
        } catch (PDOException $e) {
            // Registro del error en el log de PHP
            error_log('Error de PDO: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Error al realizar la reserva: ' . $e->getMessage()]);
        }
    }} catch (PDOException $e) {
        error_log('Error de PDO: ' . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error en la operación: ' . $e->getMessage()]);
    }
}
?>