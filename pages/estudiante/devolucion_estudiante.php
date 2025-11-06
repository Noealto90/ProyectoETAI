<?php
session_start();
$title = "Devolución de Equipos";
$headerTitle = "Devolución de Equipos";
include_once __DIR__ . '/../../templates/layouts/header.php';
// navbar.php no existe; usar navbar_estudiante por defecto para esta vista
include_once __DIR__ . '/../../templates/navbars/navbar_estudiante.php';

// Conexión a la base de datos
require_once __DIR__ . '/../../config/conexion.php'; // Asegúrate de que el archivo existe y la variable $pdo esté correctamente definida

$con = new Conexion();
$pdo = $con->getConexion();

// Manejar solicitudes GET para obtener espacios ocupados
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['laboratorio'])) {
    $laboratorio_id = $_GET['laboratorio'];

    // Consulta para obtener los espacios ocupados
    $query = "SELECT espacio_id FROM reservas WHERE laboratorio_id = :laboratorio_id AND activa = TRUE";
    $stmt = $pdo->prepare($query);
    $stmt->execute([':laboratorio_id' => $laboratorio_id]);
    $espacios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Enviar los espacios ocupados en formato JSON
    echo json_encode(['espacios' => $espacios]);
    exit;
}

// Manejar solicitudes POST para confirmar la devolución
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $laboratorio_id = $_POST['laboratorio'];
    $espacio_id = $_POST['espacio'];

    try {
        // Actualizar la tabla de reservas (activa = false)
        $queryUpdateReservas = "UPDATE reservas SET activa = FALSE WHERE laboratorio_id = :laboratorio_id AND espacio_id = :espacio_id AND activa = TRUE";
        $stmt = $pdo->prepare($queryUpdateReservas);
        $stmt->execute([
            ':laboratorio_id' => $laboratorio_id,
            ':espacio_id' => $espacio_id
        ]);

        // Actualizar la tabla de espacios (activa = true)
        $queryUpdateEspacios = "UPDATE espacios SET activa = TRUE WHERE laboratorio_id = :laboratorio_id AND espacio_id = :espacio_id";
        $stmt = $pdo->prepare($queryUpdateEspacios);
        $stmt->execute([
            ':laboratorio_id' => $laboratorio_id,
            ':espacio_id' => $espacio_id
        ]);

        echo "Devolución confirmada";
        exit;
    } catch (PDOException $e) {
        echo "Error al procesar la devolución: " . $e->getMessage();
        exit;
    }
}
?>

<div class="container">
    <h2>Seleccionar laboratorio y espacio para devolución</h2>
    <form id="devolucion-form" class="formulario">
        <label for="laboratorio">Seleccionar laboratorio:</label>
        <select id="laboratorio" name="laboratorio">
            <option value="" selected disabled>Seleccione un laboratorio...</option>
            <option value="1">Laboratorio 1</option>
            <option value="2">Laboratorio 2</option>
            <!-- Agregar más laboratorios según sea necesario -->
        </select>

        <label for="espacio">Seleccionar espacio ocupado:</label>
        <select id="espacio" name="espacio">
            <!-- Opciones de espacios ocupados se cargarán dinámicamente con PHP -->
        </select>

        <button type="submit">Confirmar devolución</button>
    </form>

    <!-- Mensaje de confirmación -->
    <div class="alert" id="successMessage" style="display:none;">¡Devolución confirmada!</div>
</div>

<?php include_once __DIR__ . '/../../templates/layouts/footer.php'; ?>

<script>
    // Mostrar el mensaje de éxito
    function showSuccessMessage() {
        const message = document.getElementById('successMessage');
        message.style.display = 'block';
        setTimeout(() => {
            message.style.display = 'none';
        }, 3000); // Ocultar el mensaje después de 3 segundos
    }

    // Cargar espacios ocupados al seleccionar un laboratorio
    document.getElementById('laboratorio').addEventListener('change', function() {
        const labId = this.value;
        fetch(`devolucion_estudiante.php?laboratorio=${labId}`)
            .then(response => response.json())
            .then(data => {
                const espacioSelect = document.getElementById('espacio');
                espacioSelect.innerHTML = '';
                data.espacios.forEach(espacio => {
                    const option = document.createElement('option');
                    option.value = espacio.espacio_id;
                    option.textContent = `Espacio ${espacio.espacio_id}`;
                    espacioSelect.appendChild(option);
                });
            });
    });

    // Manejar la devolución del formulario con AJAX
    document.getElementById('devolucion-form').addEventListener('submit', function(event) {
        event.preventDefault();  // Prevenir el comportamiento normal del formulario
        const laboratorio = document.getElementById('laboratorio').value;
        const espacio = document.getElementById('espacio').value;

        // Enviar los datos con fetch
        fetch('devolucion_estudiante.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `laboratorio=${laboratorio}&espacio=${espacio}`
        })
        .then(response => response.text())
        .then(data => {
            if (data.includes('Devolución confirmada')) {
                showSuccessMessage();
            }
        })
        .catch(() => {
            alert('Ocurrió un error al procesar la devolución.');
        });
    });

    // Eliminar la opción vacía del select después de un tiempo
    window.onload = function () {
        const selectElement = document.getElementById("laboratorio");

        // Escuchar la interacción del usuario
        selectElement.addEventListener("change", function () {
            eliminarOpcionVacia();
        });

        // También puedes eliminar la opción vacía tras un tiempo (opcional)
        setTimeout(eliminarOpcionVacia, 3000); // Por ejemplo, 3 segundos

        function eliminarOpcionVacia() {
            const firstOption = selectElement.querySelector("option[value='']");
            if (firstOption) {
                firstOption.remove(); // Elimina la opción vacía si existe
            }
        }
    };
</script>