<?php
// VerReservasSuperAdmin.php

// Verifica si la sesión ya está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión solo si no está activa
}

// Verifica si el usuario está autenticado y tiene el rol de 'estudiante'
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] != 'estudiante') {
    header('Location: login.php'); // Redirige si no tiene acceso
    //exit();
}

// Recupera el nombre del usuario y rol
$nombreUsuario = $_SESSION['nombre'];
//error_log('Nombre: ', $nombreUsuario);

// Conexión a la base de datos
include 'conexionEstu.php';  // Asegúrate de que el archivo existe y la variable $pdo esté correctamente definida


// Consulta las reservas del responsable
$sql = "SELECT id, laboratorio_id, espacio_id, nombreEncargado, nombreAcompanante, horaInicio, horaFinal, diaR, activa 
        FROM reservas 
        WHERE nombreEncargado = :nombreUsuario 
        AND activa = true";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':nombreUsuario', $nombreUsuario);
$stmt->execute();
$reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
//var_dump($reservas); // Verifica el contenido de $reservas



// Incluye el archivo HTML con el nombre del usuario
include 'VerReservasEstudiante.html';
?>
