<?php
// Archivo movido desde includes/conexion_estudiante.php (adaptado a nueva ubicación)

$host = 'localhost';
$dbname = 'administracion2';
$user = 'postgres';
$password = 'Admin';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error al conectar con la base de datos: ' . $e->getMessage());
}
?>
