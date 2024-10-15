<?php
session_start();
require 'conexion.php'; // Incluye el archivo de conexión

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($correo) || empty($password) || empty($confirm_password) || empty($nombre)) {
        echo "Por favor, complete todos los campos.";
    } elseif ($password !== $confirm_password) {
        echo "Las contraseñas no coinciden.";
    } else {
        $con = new Conexion();
        $pdo = $con->getConexion();

        // Hashea la contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Llama a la función almacenada agregar_persona para insertar el usuario
        $sql = "SELECT agregar_persona(:nombre, :correo, :password)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':password', $hashed_password);

        if ($stmt->execute()) {
            $resultado = $stmt->fetchColumn();

            if ($resultado == 'Usuario agregado correctamente.') {
                header('Location: login.html');
                exit();
            } else {
                echo $resultado;
            }
        } else {
            echo "Error al registrar el usuario.";
        }
    }
}
?>

