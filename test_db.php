<?php
require_once __DIR__ . '/config/setting.php';
try {
    $dsn = "pgsql:host=".SERVIDOR.";port=5432;dbname=".DATABASE;
    $pdo = new PDO($dsn, USUARIO, PASSWORD, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "OK: Conectado a PostgreSQL via PDO\n";
    $res = $pdo->query("SELECT version()")->fetchColumn();
    echo "Server version: " . $res;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
