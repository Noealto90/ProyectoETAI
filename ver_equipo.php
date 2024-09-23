<?php
// Incluir la conexión a la base de datos
include 'db_connection.php';

// Obtener filtros de búsqueda (si los hay)
$tipo = $_GET['tipo'] ?? '';

// Crear la consulta SQL para mostrar los equipos
$query = "SELECT e.codigo, l.nombre as laboratorio, e.tipo 
          FROM equipos e
          JOIN laboratorios l ON e.laboratorio_id = l.id
          WHERE 1=1";

// Aplicar los filtros a la consulta
if ($tipo != '') {
    $query .= " AND e.tipo = :tipo";
}


$stmt = $pdo->prepare($query);

// Asignar parámetros para los filtros
if ($tipo != '') {
    $stmt->bindParam(':tipo', $tipo);
}

$stmt->execute();
$equipos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Equipos</title>
</head>
<body>
    <h1>Equipos Disponibles</h1>

    <form action="ver_equipo.php" method="GET">
        <label for="tipo">Filtrar por tipo:</label>
        <select id="tipo" name="tipo">
            <option value="">Todos</option>
            <option value="computadora" <?= ($tipo == 'computadora') ? 'selected' : '' ?>>Computadora</option>
            <option value="mesa" <?= ($tipo == 'mesa') ? 'selected' : '' ?>>Mesa</option>
            <option value="silla" <?= ($tipo == 'silla') ? 'selected' : '' ?>>Silla</option>
        </select>

        <button type="submit">Buscar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Laboratorio</th>
                <th>Tipo</th>
                <th>Fecha de Modificación</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($equipos as $equipo): ?>
            <tr>
                <td><?= $equipo['codigo'] ?></td>
                <td><?= $equipo['laboratorio'] ?></td>
                <td><?= $equipo['tipo'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
