<?php
require 'conexion.php'; // Incluir el archivo de conexión a la base de datos

$con = new Conexion();
$pdo = $con->getConexion();

if ($pdo) {
    if (isset($_GET['action'])) {
        $action = $_GET['action'];

        // Obtener cuatrimestres
        if ($action == 'getCuatrimestres') {
            $sql = "SELECT id, numero, año FROM cuatrimestres";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $cuatrimestres = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($cuatrimestres) {
                foreach ($cuatrimestres as $cuatrimestre) {
                    echo "<option value='" . $cuatrimestre['id'] . "'>Cuatrimestre " . $cuatrimestre['numero'] . " - " . $cuatrimestre['año'] . "</option>";
                }
            } else {
                echo "<option value=''>No se encontraron cuatrimestres</option>";
            }
        }

        if ($action == 'getDias') {
            $sql = "SELECT id, idDia FROM dias";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $dias = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
            if ($dias) {
                foreach ($dias as $dia) {
                    echo "<option value='" . $dia['id'] . "'>" . $dia['idDia'] . "</option>";
                }
            } else {
                echo "<option value=''>No se encontraron días</option>";
            }
        }
        

        // Obtener profesores
        if ($action == 'getProfesores') {
            $sql = "SELECT id, nombre FROM usuarios WHERE rol = 'profesor'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $profesores = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($profesores) {
                foreach ($profesores as $profesor) {
                    echo "<option value='" . $profesor['id'] . "'>" . $profesor['nombre'] . "</option>";
                }
            } else {
                echo "<option value=''>No se encontraron profesores</option>";
            }
        }
    } else {
        echo "<option value=''>Acción no válida</option>";
    }
} else {
    echo "<option value=''>Error al cargar datos</option>";
}
?>
