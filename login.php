<?php
session_start();
require 'conexion.php'; // Incluye el archivo de conexión

// Verifica si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $password = $_POST['password'];

    // Validación básica para evitar campos vacíos
    if (empty($correo) || empty($password)) {
        echo "Por favor, complete todos los campos.";
    } else {
        $con = new Conexion();
        $pdo = $con->getConexion();

        if ($pdo) {
            // Consulta el correo en la base de datos
            $sql = "SELECT * FROM usuarios WHERE correo_institucional = :correo";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':correo', $correo);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

           // Verifica si el usuario existe y si la contraseña es correcta
            if ($usuario && password_verify($password, $usuario['contrasena'])) {
                // Guarda el nombre del usuario en la sesión
                $_SESSION['usuario'] = $usuario['correo_institucional'];
                $_SESSION['nombre'] = $usuario['nombre']; // Guardamos el nombre
                $_SESSION['rol'] = $usuario['rol']; // Almacena el rol en la sesión

                // Redirige al usuario basado en el rol
                if ($usuario['rol'] == 'estudiante') {
                    header('Location: indexEstudiante.html');
                } elseif ($usuario['rol'] == 'superAdmin') {
                    header('Location: superAdmin.php'); // Cambia a PHP para usar la sesión
                } elseif ($usuario['rol'] == 'profesor') {
                    header('Location: profesor.php'); // Cambia a PHP para usar la sesión
                } else {
                    echo "Rol no válido.";
                }
                exit(); // Detener el script después de la redirección
            }
            else {
                echo "Correo o contraseña incorrectos.";
            }
        } else {
            echo "Error de conexión a la base de datos.";
        }
    }
}
?>
