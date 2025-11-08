<?php
session_start(); // Inicia la sesión

// Redirigir siempre al formulario de login dentro del proyecto
header('Location: pages/auth/login.php');
exit();
?>