<?php
include 'conexion.php';
session_start();

$error = "";

if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['es_admin']) {
        header("Location: admin.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Por favor llena todos los campos.";
    } else {
        $email_seguro = mysqli_real_escape_string($conn, $email);
        $resultado    = mysqli_query($conn, "SELECT * FROM usuarios WHERE email = '$email_seguro'");

        if (mysqli_num_rows($resultado) == 1) {
            $usuario = mysqli_fetch_assoc($resultado);

            $acceso = false;

            if ($usuario['es_admin'] == 1) {
                if (md5($password) === $usuario['password']) {
                    $acceso = true;
                }
            } else {
                if (password_verify($password, $usuario['password'])) {
                    $acceso = true;
                }
            }

            if ($acceso) {
                $_SESSION['usuario_id']     = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['es_admin']       = $usuario['es_admin'];

                if ($usuario['es_admin']) {
                    header("Location: admin.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $error = "Contrasena incorrecta.";
            }
        } else {
            $error = "No encontramos ese correo.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>MoodTune - Iniciar sesion</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
<div class="auth-container">
    <div class="auth-box">
        <div class="logo">MoodTune</div>
        <h2>Iniciar sesion</h2>

        <?php if ($error): ?>
            <div class="mensaje error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Correo</label>
            <input type="email" name="email" placeholder="tu@correo.com" required>

            <label>Contrasena</label>
            <input type="password" name="password" placeholder="Tu contrasena" required>

            <button type="submit">Entrar</button>
        </form>

        <p class="link-abajo">No tienes cuenta? <a href="registro.php">Registrate aqui</a></p>
    </div>
</div>
</body>
</html>
