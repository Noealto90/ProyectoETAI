<?php
// superAdmin.php

session_start(); // Inicia la sesión

// Verifica si el usuario está autenticado y si tiene el rol de 'superAdmin'
if (!isset($_SESSION['nombre']) || $_SESSION['rol'] != 'estudiante') {
    header('Location: login.php'); // Redirige si no tiene acceso
    //exit();
}

// Recupera el nombre del usuario
$nombreUsuario = $_SESSION['nombre'];

// Incluye el archivo HTML separado
include 'indexEstudiante.html';
