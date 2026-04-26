<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['usuario_id']) || !$_SESSION['es_admin']) {
    header("Location: login.php");
    exit();
}

$mensaje      = "";
$tipo_mensaje = "exito";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accion = $_POST['accion'];

    if ($accion == 'agregar') {
        $titulo  = mysqli_real_escape_string($conn, trim($_POST['titulo']));
        $artista = mysqli_real_escape_string($conn, trim($_POST['artista']));
        $genero  = mysqli_real_escape_string($conn, trim($_POST['genero']));
        $estado  = mysqli_real_escape_string($conn, $_POST['estado_animo']);
        $link    = mysqli_real_escape_string($conn, trim($_POST['link_youtube']));

        if (empty($titulo) || empty($artista) || empty($genero)) {
            $mensaje      = "Titulo, artista y genero son obligatorios.";
            $tipo_mensaje = "error";
        } else {
            $sql = "INSERT INTO canciones (titulo, artista, genero, estado_animo, link_youtube)
                    VALUES ('$titulo', '$artista', '$genero', '$estado', '$link')";
            if (mysqli_query($conn, $sql)) {
                $mensaje = "Cancion agregada correctamente.";
            } else {
                $mensaje      = "Error al agregar: " . mysqli_error($conn);
                $tipo_mensaje = "error";
            }
        }
    }

    if ($accion == 'eliminar') {
        $id = intval($_POST['id']);
        if (mysqli_query($conn, "DELETE FROM canciones WHERE id = $id")) {
            $mensaje = "Cancion eliminada correctamente.";
        } else {
            $mensaje      = "Error al eliminar.";
            $tipo_mensaje = "error";
        }
    }

    if ($accion == 'editar') {
        $id      = intval($_POST['id']);
        $titulo  = mysqli_real_escape_string($conn, trim($_POST['titulo']));
        $artista = mysqli_real_escape_string($conn, trim($_POST['artista']));
        $genero  = mysqli_real_escape_string($conn, trim($_POST['genero']));
        $estado  = mysqli_real_escape_string($conn, $_POST['estado_animo']);
        $link    = mysqli_real_escape_string($conn, trim($_POST['link_youtube']));

        $sql = "UPDATE canciones SET titulo='$titulo', artista='$artista', genero='$genero',
                estado_animo='$estado', link_youtube='$link' WHERE id=$id";
        if (mysqli_query($conn, $sql)) {
            $mensaje = "Cancion actualizada correctamente.";
        } else {
            $mensaje      = "Error al actualizar: " . mysqli_error($conn);
            $tipo_mensaje = "error";
        }
    }
}

$canciones = mysqli_query($conn, "SELECT * FROM canciones ORDER BY estado_animo, titulo");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>MoodTune - Panel Admin</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body class="mood-neutral">

<nav class="navbar">
    <div class="logo">MoodTune - Panel Admin</div>
    <div class="nav-right">
        <a href="index.php" class="btn-nav">Ver sitio</a>
        <a href="logout.php" class="btn-nav">Cerrar sesion</a>
    </div>
</nav>

<div class="admin-container">

    <?php if ($mensaje): ?>
        <div class="mensaje <?= $tipo_mensaje ?>"><?= $mensaje ?></div>
    <?php endif; ?>

    <div class="admin-card">
        <h2>Agregar cancion</h2>
        <form method="POST">
            <input type="hidden" name="accion" value="agregar">
            <div class="admin-grid">
                <div>
                    <label>Titulo</label>
                    <input type="text" name="titulo" placeholder="Nombre de la cancion" required>
                </div>
                <div>
                    <label>Artista</label>
                    <input type="text" name="artista" placeholder="Nombre del artista" required>
                </div>
                <div>
                    <label>Genero</label>
                    <input type="text" name="genero" placeholder="Pop, Rock, K-Pop..." required>
                </div>
                <div>
                    <label>Estado de animo</label>
                    <select name="estado_animo" required>
                        <option value="feliz">Feliz</option>
                        <option value="triste">Triste</option>
                        <option value="enojado">Enojado</option>
                        <option value="relajado">Relajado</option>
                    </select>
                </div>
                <div class="full-width">
                    <label>Link de YouTube</label>
                    <input type="text" name="link_youtube" placeholder="https://www.youtube.com/watch?v=...">
                </div>
            </div>
            <button type="submit" class="btn-admin-submit">Agregar cancion</button>
        </form>
    </div>

    <div class="admin-card">
        <h2>Canciones registradas</h2>
        <table class="tabla-admin">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titulo</th>
                    <th>Artista</th>
                    <th>Genero</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($c = mysqli_fetch_assoc($canciones)): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><?= htmlspecialchars($c['titulo']) ?></td>
                    <td><?= htmlspecialchars($c['artista']) ?></td>
                    <td><?= htmlspecialchars($c['genero']) ?></td>
                    <td><span class="badge-estado badge-<?= $c['estado_animo'] ?>"><?= ucfirst($c['estado_animo']) ?></span></td>
                    <td class="acciones-td">
                        <button class="btn-editar" onclick="abrirEditar(
                            '<?= $c['id'] ?>',
                            '<?= addslashes(htmlspecialchars($c['titulo'])) ?>',
                            '<?= addslashes(htmlspecialchars($c['artista'])) ?>',
                            '<?= addslashes(htmlspecialchars($c['genero'])) ?>',
                            '<?= $c['estado_animo'] ?>',
                            '<?= addslashes($c['link_youtube']) ?>'
                        )">Editar</button>

                        <form method="POST" style="display:inline" onsubmit="return confirm('Seguro que quieres eliminar esta cancion?')">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                            <button type="submit" class="btn-eliminar">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<div id="modal-editar" class="modal" style="display:none">
    <div class="modal-box">
        <h2>Editar cancion</h2>
        <form method="POST">
            <input type="hidden" name="accion" value="editar">
            <input type="hidden" name="id" id="edit-id">

            <label>Titulo</label>
            <input type="text" name="titulo" id="edit-titulo" required>

            <label>Artista</label>
            <input type="text" name="artista" id="edit-artista" required>

            <label>Genero</label>
            <input type="text" name="genero" id="edit-genero" required>

            <label>Estado de animo</label>
            <select name="estado_animo" id="edit-estado">
                <option value="feliz">Feliz</option>
                <option value="triste">Triste</option>
                <option value="enojado">Enojado</option>
                <option value="relajado">Relajado</option>
            </select>

            <label>Link de YouTube</label>
            <input type="text" name="link_youtube" id="edit-link">

            <div class="modal-botones">
                <button type="submit" class="btn-admin-submit">Guardar cambios</button>
                <button type="button" class="btn-cancelar" onclick="cerrarModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirEditar(id, titulo, artista, genero, estado, link) {
    document.getElementById('edit-id').value      = id;
    document.getElementById('edit-titulo').value  = titulo;
    document.getElementById('edit-artista').value = artista;
    document.getElementById('edit-genero').value  = genero;
    document.getElementById('edit-estado').value  = estado;
    document.getElementById('edit-link').value    = link;
    document.getElementById('modal-editar').style.display = 'flex';
}

function cerrarModal() {
    document.getElementById('modal-editar').style.display = 'none';
}
</script>

</body>
</html>
