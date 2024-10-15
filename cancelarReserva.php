<?php
// Verificamos si la sesión está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificamos si el usuario está autenticado como super admin
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] != 'superAdmin') {
    header('Location: login.php');
    exit();
}

// Incluimos la conexión a la base de datos
require_once 'conexionEstu.php'; // Asegúrate de que el archivo es correcto

// Verificamos que se haya enviado el ID de la reserva
if (isset($_POST['id'])) {
    $reservaId = $_POST['id'];

    // Preparamos la consulta para marcar la reserva como inactiva
    $sql = "UPDATE reservas SET activa = FALSE WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    // Ejecutamos la consulta con el ID de la reserva
    if ($stmt->execute(['id' => $reservaId])) {
        // Verificamos si alguna fila fue afectada (si se actualizó una reserva)
        if ($stmt->rowCount() > 0) {
            // Redirigir con un mensaje de éxito
            header('Location: VerReservasSuperAdmin.php?mensaje=exito');
        } else {
            // Si no se afectó ninguna fila, significa que la reserva no existe o ya estaba cancelada
            header('Location: VerReservasSuperAdmin.php?mensaje=no_encontrada');
        }
    } else {
        // Si hubo un error en la ejecución de la consulta
        header('Location: VerReservasSuperAdmin.php?mensaje=error');
    }
} else {
    // Si no se proporcionó ningún ID
    header('Location: VerReservasSuperAdmin.php?mensaje=falta_id');
}

exit();
?>
