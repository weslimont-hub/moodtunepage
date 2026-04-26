<?php
include 'conexion.php';
session_start();

$error = "";
$exito = "";

if (isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre    = trim($_POST['nombre']);
    $email     = trim($_POST['email']);
    $password  = $_POST['password'];
    $confirmar = $_POST['confirmar'];

    if (empty($nombre) || empty($email) || empty($password)) {
        $error = "Por favor llena todos los campos.";
    } elseif ($password !== $confirmar) {
        $error = "Las contrasenas no coinciden.";
    } elseif (strlen($password) < 6) {
        $error = "La contrasena debe tener al menos 6 caracteres.";
    } else {
        $email_seguro = mysqli_real_escape_string($conn, $email);
        $nombre_seguro = mysqli_real_escape_string($conn, $nombre);
        $check = mysqli_query($conn, "SELECT id FROM usuarios WHERE email = '$email_seguro'");

        if (mysqli_num_rows($check) > 0) {
            $error = "Ese correo ya esta registrado.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql  = "INSERT INTO usuarios (nombre, email, password) VALUES ('$nombre_seguro', '$email_seguro', '$hash')";
            if (mysqli_query($conn, $sql)) {
                $exito = "Cuenta creada correctamente.";
            } else {
                $error = "Error al crear la cuenta. Intenta de nuevo.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>MoodTune - Registro</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
<div class="auth-container">
    <div class="auth-box">
        <div class="logo">MoodTune</div>
        <h2>Crear cuenta</h2>

        <?php if ($error): ?>
            <div class="mensaje error"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($exito): ?>
            <div class="mensaje exito"><?= $exito ?> <a href="login.php">Iniciar sesion</a></div>
        <?php endif; ?>

        <form method="POST">
            <label>Nombre</label>
            <input type="text" name="nombre" placeholder="Tu nombre" required>

            <label>Correo</label>
            <input type="email" name="email" placeholder="tu@correo.com" required>

            <label>Contrasena</label>
            <input type="password" name="password" placeholder="Minimo 6 caracteres" required>

            <label>Confirmar contrasena</label>
            <input type="password" name="confirmar" placeholder="Repite tu contrasena" required>

            <button type="submit">Registrarse</button>
        </form>

        <p class="link-abajo">Ya tienes cuenta? <a href="login.php">Inicia sesion</a></p>
    </div>
</div>
</body>
</html>
