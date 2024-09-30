<?php
// Conexión a la base de datos
$servername = "localhost"; // Cambiar si es necesario
$username = "root"; // Cambiar si es necesario
$password = ""; // Cambiar si es necesario
$dbname = "gestion_laboratorios"; // Nombre de tu base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

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

    // Si no hay errores, guardar en la base de datos
    if (empty($errores)) {
        // Preparar la consulta SQL
        $stmt = $conn->prepare("INSERT INTO reportes_danios (tipo, codigo_equipo, laboratorio, comentario) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $tipo, $codigo, $laboratorio, $comentario); // 's' para string, 'i' para int

        // Ejecutar la consulta y verificar si fue exitosa
        if ($stmt->execute()) {
            $mensaje_exito = "Reporte enviado exitosamente.";
        } else {
            $errores[] = "Error al enviar el reporte: " . $conn->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado del Reporte de Daños</title>
    <style>
        /* Estilos básicos */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            color: #002D6D;
            text-align: center;
        }

        .container {
            padding: 50px;
        }

        .mensaje {
            padding: 20px;
            background-color: #dff0d8; /* Verde claro */
            color: #3c763d;
            margin-bottom: 20px;
            display: inline-block;
            border-radius: 5px;
        }

        .errores {
            padding: 20px;
            background-color: #f2dede; /* Rojo claro */
            color: #a94442;
            margin-bottom: 20px;
            display: inline-block;
            border-radius: 5px;
        }

        a {
            text-decoration: none;
            color: white;
            background-color: #208048;
            padding: 10px 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!empty($errores)): ?>
            <div class="errores">
                <h3>Ocurrieron los siguientes errores:</h3>
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif (!empty($mensaje_exito)): ?>
            <div class="mensaje">
                <h3><?php echo $mensaje_exito; ?></h3>
            </div>
        <?php endif; ?>

        <a href="indexEstudiante.html">Volver al Inicio</a>
    </div>
</body>
</html>
