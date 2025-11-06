<?php
// Archivo movido desde includes/conexion.php (adaptado a nueva ubicación)
require_once __DIR__ . '/setting.php'; // Carga las constantes de configuración

class Conexion {
    private $conector = null;

    public function getConexion() {
        try {
            $this->conector = new PDO("pgsql:host=".SERVIDOR.";port=5432;dbname=".DATABASE, USUARIO, PASSWORD);
            $this->conector->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Error de conexión: ' . $e->getMessage();
            $this->conector = null;
        }

        return $this->conector;
    }
}

$con = new Conexion();
if ($con->getConexion() == null) {
    echo "Error al conectarse a la base de datos";
}

?>
