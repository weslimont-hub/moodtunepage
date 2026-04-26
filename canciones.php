<?php
include 'conexion.php';
session_start();

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(["error" => "No autorizado"]);
    exit();
}

$estado = $_GET['estado'] ?? '';
$estados_validos = ['feliz', 'triste', 'enojado', 'relajado'];

if (!in_array($estado, $estados_validos)) {
    echo json_encode(["error" => "Estado no valido"]);
    exit();
}

$resultado = mysqli_query($conn, "SELECT * FROM canciones WHERE estado_animo = '$estado' ORDER BY RAND() LIMIT 4");

$canciones = [];
while ($fila = mysqli_fetch_assoc($resultado)) {
    $canciones[] = $fila;
}

if (count($canciones) > 0) {
    $uid = $_SESSION['usuario_id'];
    $cid = $canciones[0]['id'];
    mysqli_query($conn, "INSERT INTO historial (usuario_id, estado_detectado, cancion_id) VALUES ('$uid', '$estado', '$cid')");
}

header('Content-Type: application/json');
echo json_encode($canciones);
?>
