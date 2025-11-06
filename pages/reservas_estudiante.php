<?php

session_start(); // Inicia la sesión
// Incluir el archivo de conexión a la base de datos
require_once __DIR__ . '/../config/conexion_estudiante.php';

$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
} else {
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


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
        $fecha = isset($input['date']) ? $input['date'] : '2024-10-15';
        $hora = isset($input['time']) ? $input['time'] : '12:00:00';
        // Registrar la fecha y hora que se están usando
        error_log("Fecha usada en la reserva: $fecha, Hora usada en la reserva: $hora");
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
        $nombreEncargado = $_SESSION['nombre']; 
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
    
            // Calcular la horaFinal (horaInicio + 3 horas)
            $horaInicioDateTime = new DateTime($horaInicio);
            $horaFinalDateTime = clone $horaInicioDateTime;
            $horaFinalDateTime->add(new DateInterval('PT3H')); // Sumar 3 horas
            $horaFinal = $horaFinalDateTime->format('H:i:s'); // Obtener la hora final en formato HH:MM:SS
    
            // Insertamos los datos en la tabla reservas
            $query = "INSERT INTO reservas (laboratorio_id, espacio_id, nombreEncargado, nombreAcompanante, horaInicio, horaFinal, diaR, activa) 
                      VALUES (:laboratorio_id, :espacio_id, :nombreEncargado, :nombreAcompanante, :horaInicio, :horaFinal, :fecha, :activa)";
    
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                ':laboratorio_id' => $laboratorio_id,
                ':espacio_id' => $espacio_id,
                ':nombreEncargado' => $nombreEncargado,
                ':nombreAcompanante' => $nombreAcompanante,
                ':horaInicio' => $horaInicio,
                ':horaFinal' => $horaFinal,  // Insertamos la hora final calculada
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

            // Obtener el correo del encargado
            $queryCorreoEncargado = "SELECT correo_institucional FROM usuarios WHERE nombre = :nombreEncargado";
            $stmtCorreoEncargado = $pdo->prepare($queryCorreoEncargado);
            $stmtCorreoEncargado->execute([':nombreEncargado' => $nombreEncargado]);
            $correoEncargado = $stmtCorreoEncargado->fetchColumn();


            // Enviar correo de confirmación
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'noealto03@gmail.com';
            $mail->Password = 'swro tdsr scpk fqwk';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->setFrom('noealto03@gmail.com', 'Sistema de Reservas');

            if ($correoEncargado) {
                $mail->addAddress($correoEncargado);
            }

            if ($nombreAcompanante) {
                $mail->addAddress($nombreAcompanante);
            }

            $mail->Subject = 'Confirmacion de Reserva';
            $mail->Body = "
                A quien corresponda: 

                Se confirma su reserva de un espacio. A continuación, se le proporcionan los detalles de su reserva:

                - Laboratorio: $laboratorio_id
                - Espacio: $espacio_id
                - Fecha: $fecha
                - Hora de Inicio: $hora
                - Hora de Final: $horaFinal
                - Compañero: $nombreAcompanante

                Le agradecemos por utilizar nuestro sistema de reservas. Si tiene alguna consulta, no dude en contactarnos.

                Saludos cordiales,
                Sistema de Reservas
            ";

            $mail->send();
            



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