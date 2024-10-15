<?php

require 'setting.php'; // Archivo con las constantes de configuración

class Conexion {
    private $conector = null;

    public function getConexion() {
        try {
            // Usamos pgsql para conectarnos a PostgreSQL
            $this->conector = new PDO("pgsql:host=".SERVIDOR.";port=5432;dbname=".DATABASE, USUARIO, PASSWORD);
            $this->conector->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Captura y muestra el error si no puede conectarse
            echo 'Error de conexión: ' . $e->getMessage();
            $this->conector = null;
        }

        return $this->conector;
    }
}

$con = new Conexion();
if ($con->getConexion() != null) {
    echo "Conexión exitosa a la base de datos PostgreSQL";
} else {
    echo "Error al conectarse a la base de datos";
}


?>
