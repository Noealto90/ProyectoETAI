<?php
if (isset($_GET['fecha']) && isset($_GET['laboratorio'])) {
    $fecha = $_GET['fecha'];
    $laboratorio = $_GET['laboratorio'];

    require_once __DIR__ . '/../../config/conexion.php';
    $con = new Conexion();
    $pdo = $con->getConexion();

    // Ajuste de la consulta SQL para incluir el filtro del laboratorio
    $query = "
        SELECT 
            r.espacio_id,
            r.nombreEncargado AS encargado,
            r.nombreAcompanante AS acompanante,
            r.horaInicio,
            r.horaFinal
        FROM reservas r
        WHERE r.diaR = :fecha";
    
    // Si se seleccionó un laboratorio específico, se añade a la consulta
    if ($laboratorio != 'ambos') {
        $query .= " AND r.laboratorio_id = :laboratorio";
    }

    $query .= " ORDER BY r.horaInicio";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':fecha', $fecha);

    // Vinculamos el parámetro de laboratorio si se seleccionó uno específico
    if ($laboratorio != 'ambos') {
        $stmt->bindParam(':laboratorio', $laboratorio);
    }

    $stmt->execute();
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener el nombre del laboratorio para el encabezado
    $nombreLaboratorio = ($laboratorio == 'ambos') ? 'Ambos Laboratorios' : 'Laboratorio ' . htmlspecialchars($laboratorio);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Uso de Laboratorios</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <h2>Informe Detallado del Uso de Laboratorios</h2>
    <p style="text-align: center;">Fecha: <?php echo htmlspecialchars($fecha); ?></p>
    <p style="text-align: center;">Laboratorio: <?php echo $nombreLaboratorio; ?></p>
    <table>
        <thead>
            <tr>
                <th>Espacio</th>
                <th>Encargado</th>
                <th>Acompañante</th>
                <th>Hora Inicio</th>
                <th>Hora Final</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservas as $reserva): ?>
                <tr>
                    <td><?php echo htmlspecialchars($reserva['espacio_id']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['encargado']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['acompanante']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['horainicio']); ?></td>
                    <td><?php echo htmlspecialchars($reserva['horafinal']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button onclick="window.print()" class="no-print">Imprimir o Guardar como PDF</button>
</body>
</html>
