<?php
session_start();
require_once __DIR__ . '/../../config/conexion.php'; // Incluir el archivo de conexión a la base de datos

// Crear la instancia de conexión y obtener el PDO
$con = new Conexion();
$pdo = $con->getConexion(); // Ahora $pdo tiene la conexión a la base de datos

if ($pdo === null) {
    die("Error al conectarse a la base de datos");
}

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

// Variables para el header
$title = "Ver Equipos";
$headerTitle = "Equipos Disponibles";
// Añadir CSS específico para esta página
$extraCss = 'assets/css/ver-equipo.css';

include_once __DIR__ . '/../../templates/layouts/header.php';
include_once __DIR__ . '/../../templates/navbars/navbar_super_admin.php';
?>

<div class="container">
    <h2>Equipos Disponibles</h2>

        <div class="filter-buttons">
            <button class="filter-btn" data-type="">Todos</button>
            <button class="filter-btn" data-type="computadora">Computadora</button>
            <button class="filter-btn" data-type="mesa">Mesa</button>
            <button class="filter-btn" data-type="silla">Silla</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Laboratorio</th>
                    <th>Tipo</th>
                </tr>
            </thead>
            <tbody id="equipos-tbody">
                <?php foreach ($equipos as $equipo): ?>
                    <tr>
                        <td><?= htmlspecialchars($equipo['codigo']) ?></td>
                        <td><?= htmlspecialchars($equipo['laboratorio']) ?></td>
                        <td><?= htmlspecialchars($equipo['tipo']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
</div>
<script>
    document.querySelectorAll('.filter-btn').forEach(button => {
        button.addEventListener('click', () => {
            const tipo = button.getAttribute('data-type');
            fetch(`ver_equipo.php?tipo=${tipo}`)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const tbody = doc.querySelector('#equipos-tbody');
                    document.querySelector('#equipos-tbody').innerHTML = tbody.innerHTML;
                });
        });
    });
</script>

<?php include_once __DIR__ . '/../../templates/layouts/footer.php'; ?>