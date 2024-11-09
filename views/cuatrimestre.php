<?php
session_start();
$title = "Reservas por Cuatrimestre";
$headerTitle = "Reservas por Cuatrimestre";
include '../templates/header.php';
include '../templates/navbar.php';
?>

<div class="container">
  <div class="form-card">
    <h2>Nueva Reserva</h2>

    <div class="form-group">
      <label for="cuatrimestre">Cuatrimestre</label>
      <select id="cuatrimestre" name="cuatrimestre" required>
        <option value="">Seleccione un cuatrimestre</option>
        <!-- Las opciones de cuatrimestres serán cargadas aquí con AJAX -->
      </select>
    </div>

    <div class="form-group">
      <label for="day">Día de la semana</label>
      <select id="day" name="day" required>
        <option value="">Seleccione un día</option>
        <!-- Las opciones de días serán cargadas aquí con AJAX -->
      </select>
    </div>

    <div class="form-group">
      <label for="professor">Profesor</label>
      <select id="professor" name="professor" required>
        <option value="">Seleccione un profesor</option>
        <!-- Las opciones de profesores serán cargadas aquí con AJAX -->
      </select>
    </div>

    <div class="form-group">
      <label for="start-time">Hora de inicio</label>
      <input type="time" id="start-time" required>
    </div>

    <div class="form-group">
      <label for="end-time">Hora de finalización</label>
      <input type="time" id="end-time" required>
    </div>

    <button class="btn-confirm">Confirmar Reserva</button>
  </div>
</div>

<?php include '../templates/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Incluye jQuery -->
<script>
  // Cargar datos de cuatrimestres, profesores y días desde el servidor
  $(document).ready(function() {
    // Cargar cuatrimestres
    $.ajax({
      url: 'cuatrimestre.php',
      type: 'GET',
      data: { action: 'getCuatrimestres' },
      success: function(data) {
        $('#cuatrimestre').append(data); // Añade los datos recibidos al select
      },
      error: function() {
        alert('Error al cargar los cuatrimestres');
      }
    });

    // Cargar días
    $.ajax({
      url: 'cuatrimestre.php',
      type: 'GET',
      data: { action: 'getDias' },
      success: function(data) {
        $('#day').append(data); // Añade los datos recibidos al select
      },
      error: function() {
        alert('Error al cargar los días');
      }
    });

    // Cargar profesores
    $.ajax({
      url: 'cuatrimestre.php',
      type: 'GET',
      data: { action: 'getProfesores' },
      success: function(data) {
        $('#professor').append(data); // Añade los datos recibidos al select
      },
      error: function() {
        alert('Error al cargar los profesores');
      }
    });
  });
</script>

<?php
require '../includes/conexion.php'; // Incluir el archivo de conexión a la base de datos

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

        // Obtener días
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
            $sql = "SELECT id, nombre FROM profesores";
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
    }
}
?>