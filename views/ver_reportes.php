<?php
session_start();
$title = "Ver Reportes de Daños";
$headerTitle = "Reportes de Daños";
include '../templates/header.php';

// Verificar si el usuario está autenticado y tiene el rol adecuado
if (!isset($_SESSION['nombre']) || !isset($_SESSION['rol']) || !in_array($_SESSION['rol'], ['superAdmin', 'administrador'])) {
    header('Location: login.php');
    exit();
}

// Incluir la barra de navegación correspondiente según el rol del usuario
if ($_SESSION['rol'] == 'superAdmin') {
    include '../templates/navbar_super_admin.php';
} elseif ($_SESSION['rol'] == 'administrador') {
    include '../templates/navbar_administrador.php';
}

// Conexión a la base de datos
require '../includes/conexion.php';
$con = new Conexion();
$pdo = $con->getConexion();

// Obtener filtros de búsqueda (si los hay)
$estado = $_GET['estado'] ?? '';

// Crear la consulta SQL para mostrar los reportes de daños
$query = "SELECT r.id, r.codigo_equipo, r.descripcion, r.fecha_reporte, r.reportado_por, e.estado
          FROM reportes_daños r
          JOIN equipos e ON r.codigo_equipo = e.codigo
          WHERE 1=1";

// Aplicar los filtros a la consulta
if ($estado != '') {
    $query .= " AND e.estado = :estado";
}

$stmt = $pdo->prepare($query);

// Asignar parámetros para los filtros
if ($estado != '') {
    $stmt->bindParam(':estado', $estado);
}

$stmt->execute();
$reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Reportes de Daños</h2>

    <div class="filter-buttons">
        <button class="filter-btn" data-state="">Todos</button>
        <button class="filter-btn" data-state="disponible">Disponible</button>
        <button class="filter-btn" data-state="bloqueado">Bloqueado</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Código del Equipo</th>
                <th>Descripción</th>
                <th>Fecha de Reporte</th>
                <th>Reportado por</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="reportes-tbody">
            <?php foreach ($reportes as $reporte): ?>
                <tr>
                    <td><?php echo htmlspecialchars($reporte['id']); ?></td>
                    <td><?php echo htmlspecialchars($reporte['codigo_equipo']); ?></td>
                    <td><?php echo htmlspecialchars($reporte['descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($reporte['fecha_reporte']); ?></td>
                    <td><?php echo htmlspecialchars($reporte['reportado_por']); ?></td>
                    <td><?php echo htmlspecialchars($reporte['estado']); ?></td>
                    <td>
                        <?php if ($reporte['estado'] == 'bloqueado'): ?>
                            <form action="restaurar_equipo.php" method="POST" style="display:inline;">
                                <input type="hidden" name="codigo" value="<?php echo htmlspecialchars($reporte['codigo_equipo']); ?>">
                                <button type="submit">Restaurar</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="toast" class="toast"></div>

<script>
    document.querySelectorAll('.filter-btn').forEach(button => {
        button.addEventListener('click', () => {
            const estado = button.getAttribute('data-state');
            fetch(`ver_reportes.php?estado=${estado}`)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const tbody = doc.querySelector('#reportes-tbody');
                    document.querySelector('#reportes-tbody').innerHTML = tbody.innerHTML;
                });
        });
    });

    // Mostrar el toast si hay un mensaje de restauración
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('mensaje') && urlParams.get('mensaje') === 'restaurado') {
        showToast('El equipo ha sido restaurado y está disponible.');
        // Eliminar el parámetro de la URL después de mostrar el mensaje
        setTimeout(() => {
            window.history.replaceState({}, document.title, window.location.pathname);
        }, 3000);
    }

    function showToast(message) {
        const toast = document.getElementById('toast');
        toast.className = 'toast show';
        toast.innerHTML = message;

        setTimeout(() => {
            toast.className = toast.className.replace('show', '');
        }, 3000);
    }
</script>

<?php include '../templates/footer.php'; ?>