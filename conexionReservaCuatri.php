<?php
class Conexion {
    private $host = 'localhost'; // Cambia esto según tu configuración
    private $dbname = 'BaseDeDatosAdministracion'; // Nombre de tu base de datos
    private $username = 'postgres'; // Usuario de la base de datos
    private $password = 'Admin'; // Contraseña de la base de datos
    private $pdo;


    public function __construct() {
        try {
            // Crear una nueva conexión PDO
            $this->pdo = new PDO("mysql:host={$this->host};dbname={$this->dbname}", $this->username, $this->password);
            // Configurar el modo de error de PDO a excepción
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
        }
    }

    public function getConexion() {
        return $this->pdo;
    }
}
?>
