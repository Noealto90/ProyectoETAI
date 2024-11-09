<?php

$host = 'localhost';  // Ajusta los valores de tu servidor PostgreSQL
$dbname = 'administracion';
$user = 'postgres';
$password = 'Admin';

try {
    // Cerrar la conexión estableciendo la variable a null
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Error al conectar con la base de datos: ' . $e->getMessage());
}
?>
