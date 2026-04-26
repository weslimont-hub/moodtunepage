<?php
$host     = "localhost";
$usuario  = "root";
$password = "";
$base     = "moodtune";

$conn = mysqli_connect($host, $usuario, $password, $base);

if (!$conn) {
    die("Error de conexion: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
?>
