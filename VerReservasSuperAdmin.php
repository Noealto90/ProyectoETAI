<?php
// VerReservasSuperAdmin.php

// Verifica si la sesión ya está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión solo si no está activa
}

// Verifica si el usuario está autenticado y tiene el rol de 'superAdmin'
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] != 'superAdmin') {
    header('Location: login.php'); // Redirige si no tiene acceso
    exit();
}

// Recupera el nombre del usuario y rol
$nombreUsuario = $_SESSION['nombre'];

// Incluye el archivo HTML con el nombre del usuario
include 'VerReservasSuperAdmin.html';
?>
