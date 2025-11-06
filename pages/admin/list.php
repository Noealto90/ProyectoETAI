<?php
session_start();
$title = "Listado de Equipos";
$headerTitle = "Equipos Disponibles";
include_once __DIR__ . '/../../templates/layouts/header.php';
include_once __DIR__ . '/../../templates/navbars/navbar_super_admin.php';

// Conexión a la base de datos
require_once __DIR__ . '/../../config/conexion.php';

$con = new Conexion();
$pdo = $con->getConexion();

// Consulta para obtener los equipos
$query = "SELECT e.id, e.codigo, e.tipo, l.nombre AS laboratorio
          FROM equipos e
          JOIN laboratorios l ON e.laboratorio_id = l.id";
$stmt = $pdo->prepare($query);
$stmt->execute();
$equipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Equipos Disponibles</h2>
    <table style="margin-top:-25px">
        <thead>
            <tr>
                <th>ID</th>
                <th>Código</th>
                <th>Tipo</th>
                <th>Laboratorio</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipos as $equipo): ?>
                <tr>
                    <td><?php echo htmlspecialchars($equipo['id']); ?></td>
                    <td><?php echo htmlspecialchars($equipo['codigo']); ?></td>
                    <td><?php echo htmlspecialchars($equipo['tipo']); ?></td>
                    <td><?php echo htmlspecialchars($equipo['laboratorio']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include_once __DIR__ . '/../../templates/layouts/footer.php'; ?>