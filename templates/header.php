<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/css/add.css">
    <link rel="stylesheet" href="../assets/css/index.css">
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/register.css">
    <link rel="stylesheet" href="../assets/css/index-estudiante.css">
    <link rel="stylesheet" href="../assets/css/super_admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/header.css">
</head>
<body>
<header>
    <div class="header-content">
        <div class="logo-container">
            <img src="../assets/images/logo.png" alt="Logo" class="logo">
        </div>
        <h1><?php echo $headerTitle; ?></h1>
        <div class="user-menu">
            <i class="fas fa-user-circle user-icon" onclick="window.location.href='../views/mi_cuenta.php'"></i>
            <div class="user-menu-content">
                <a href="../views/mi_cuenta.php"><i class="fas fa-user"></i>Mi Cuenta</a>
                <a href="../views/logout.php"><i class="fas fa-sign-out-alt"></i>Salir</a>
            </div>
        </div>
    </div>
</header>
</body>
</html>