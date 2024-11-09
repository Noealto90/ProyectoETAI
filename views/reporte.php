<?php
session_start();
$title = "Reporte de Daños - Sistema de Gestión de Equipos";
$headerTitle = "Reporte de Daños";
include '../templates/header.php';
include '../templates/navbar.php';

// Conexión a la base de datos
require '../includes/conexion.php'; // Asegúrate de que el archivo existe y la variable $pdo esté correctamente definida

$con = new Conexion();
$pdo = $con->getConexion();

// Variables para almacenar errores y mensajes
$errores = [];
$mensaje_exito = "";

// Validar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $tipo = $_POST['tipo'];
    $codigo = $_POST['codigo'];
    $laboratorio = $_POST['laboratorio'];
    $comentario = $_POST['comentario'];

    // Validación de campos
    if (empty($tipo)) {
        $errores[] = "Por favor seleccione un tipo de daño.";
    }

    // Validación del código (alfanumérico, mínimo 5 caracteres)
    if (empty($codigo) || !preg_match("/^[A-Za-z0-9]{5,}$/", $codigo)) {
        $errores[] = "El código debe ser alfanumérico y tener al menos 5 caracteres.";
    }

    if (empty($laboratorio)) {
        $errores[] = "Por favor seleccione un laboratorio.";
    }

    if (empty($comentario)) {
        $errores[] = "Por favor ingrese una descripción del problema.";
    }

    // Si no hay errores, insertar el reporte en la base de datos
    if (empty($errores)) {
        $query = "INSERT INTO reportes (tipo, codigo, laboratorio, comentario) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        if ($stmt->execute([$tipo, $codigo, $laboratorio, $comentario])) {
            $mensaje_exito = "Reporte enviado con éxito.";
        } else {
            $errores[] = "Error al enviar el reporte: " . $stmt->errorInfo()[2];
        }
    }
}
?>

<div class="container">
    <h2>Por favor complete la información del daño</h2>

    <?php if (!empty($errores)): ?>
        <div class="errores">
            <h3>Ocurrieron los siguientes errores:</h3>
            <ul>
                <?php foreach ($errores as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif (!empty($mensaje_exito)): ?>
        <div class="mensaje">
            <h3><?php echo htmlspecialchars($mensaje_exito); ?></h3>
        </div>
    <?php endif; ?>

    <form id="reporteForm" class="formulario" method="POST" onsubmit="return validarFormulario()">
        <div class="form-group">
            <label for="tipo">Tipo de Equipo:</label>
            <select id="tipo" name="tipo">
                <option value="">Seleccione un tipo</option>
                <option value="equipo">Equipo</option>
                <option value="aire">Aire Acondicionado</option>
                <option value="pantalla">Pantalla</option>
            </select>
            <p id="error-tipo" class="error"></p>
        </div>

        <div class="form-group">
            <label for="codigo">Código del Equipo:</label>
            <input type="text" id="codigo" name="codigo" placeholder="Ingrese el código">
            <p id="error-codigo" class="error"></p>
        </div>

        <div class="form-group">
            <label for="laboratorio">Laboratorio:</label>
            <select id="laboratorio" name="laboratorio">
                <option value="">Seleccione un laboratorio</option>
                <option value="1">Laboratorio 1</option>
                <option value="2">Laboratorio 2</option>
            </select>
            <p id="error-laboratorio" class="error"></p>
        </div>

        <div class="form-group">
            <label for="comentario">Descripción del Problema:</label>
            <textarea id="comentario" name="comentario" placeholder=" "></textarea>
            <p id="error-comentario" class="error"></p>
        </div>

        <div class="form-group">
            <input type="submit" value="Confirmar Reporte" class="button">
        </div>
    </form>
</div>

<?php include '../templates/footer.php'; ?>

<script>
    function validarFormulario() {
        // Limpiar errores previos
        document.getElementById("error-tipo").innerHTML = "";
        document.getElementById("error-codigo").innerHTML = "";
        document.getElementById("error-laboratorio").innerHTML = "";
        document.getElementById("error-comentario").innerHTML = "";

        // Obtener los valores de los campos
        var tipo = document.getElementById("tipo").value;
        var codigo = document.getElementById("codigo").value;
        var laboratorio = document.getElementById("laboratorio").value;
        var comentario = document.getElementById("comentario").value;

        // Variable para controlar la validez del formulario
        var esValido = true;

        // Validar tipo de daño
        if (tipo === "") {
            document.getElementById("error-tipo").innerHTML = "Por favor seleccione un tipo de daño.";
            esValido = false;
        }

        // Validar código del equipo 
        var codigoRegex = /^[A-Za-z0-9]{5,}$/;
        if (codigo === "" || !codigoRegex.test(codigo)) {
            document.getElementById("error-codigo").innerHTML = "Por favor ingrese el código del equipo.";
            esValido = false;
        }

        // Validar laboratorio
        if (laboratorio === "") {
            document.getElementById("error-laboratorio").innerHTML = "Por favor seleccione un laboratorio.";
            esValido = false;
        }

        // Validar comentario descriptivo
        if (comentario === "") {
            document.getElementById("error-comentario").innerHTML = "Por favor ingrese una descripción del problema.";
            esValido = false;
        }

        // Si todo está bien, se puede enviar el formulario
        return esValido;
    }
</script>