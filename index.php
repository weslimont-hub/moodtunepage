<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
$nombre = $_SESSION['usuario_nombre'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>MoodTune</title>
    <link rel="stylesheet" href="estilo.css">
    <script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>
</head>
<body id="pagina" class="mood-neutral">

<nav class="navbar">
    <div class="logo">MoodTune</div>
    <div class="nav-right">
        <span>Hola, <?= htmlspecialchars($nombre) ?></span>
        <?php if ($_SESSION['es_admin']): ?>
            <a href="admin.php" class="btn-nav">Panel Admin</a>
        <?php endif; ?>
        <a href="logout.php" class="btn-nav">Cerrar sesion</a>
    </div>
</nav>

<div class="main-container">

    <div class="camara-section">
        <h2>Como te sientes hoy?</h2>

        <div class="video-wrapper">
            <video id="video" autoplay muted playsinline></video>
            <canvas id="canvas"></canvas>
        </div>

        <div class="estado-box" id="estado-box">
            <span id="texto-estado">Iniciando camara...</span>
        </div>

        <p class="o-text">o elige manualmente</p>
        <div class="botones-mood">
            <button class="btn-mood btn-feliz"    onclick="buscarCanciones('feliz')">Feliz</button>
            <button class="btn-mood btn-triste"   onclick="buscarCanciones('triste')">Triste</button>
            <button class="btn-mood btn-enojado"  onclick="buscarCanciones('enojado')">Enojado</button>
            <button class="btn-mood btn-relajado" onclick="buscarCanciones('relajado')">Relajado</button>
        </div>
    </div>

    <div class="recomendaciones-section">
        <h2>Recomendaciones para ti</h2>
        <p class="subtitulo">Canciones segun tu estado de animo</p>

        <div id="canciones-lista">
            <div class="placeholder-msg">
                <p>Apunta la camara a tu cara o elige un estado de animo para recibir canciones.</p>
            </div>
        </div>
    </div>

</div>

<script>
const video       = document.getElementById('video');
const canvas      = document.getElementById('canvas');
const textoEstado = document.getElementById('texto-estado');
const pagina      = document.getElementById('pagina');

let ultimoEstado = '';

const colores = {
    feliz:    'mood-feliz',
    triste:   'mood-triste',
    enojado:  'mood-enojado',
    relajado: 'mood-relajado'
};

const etiquetas = {
    feliz:    'Feliz',
    triste:   'Triste',
    enojado:  'Enojado',
    relajado: 'Relajado'
};

function cambiarColor(estado) {
    pagina.className = colores[estado] || 'mood-neutral';
}

async function cargarModelos() {
    textoEstado.textContent = "Cargando modelos de IA...";
    const URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api/model/';
    await faceapi.nets.tinyFaceDetector.loadFromUri(URL);
    await faceapi.nets.faceExpressionNet.loadFromUri(URL);
    textoEstado.textContent = "Listo. Iniciando camara...";
    iniciarCamara();
}

async function iniciarCamara() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
        video.addEventListener('play', analizarCara);
        textoEstado.textContent = "Analizando tu expresion...";
    } catch (err) {
        textoEstado.textContent = "Camara no disponible. Usa los botones de abajo.";
    }
}

async function analizarCara() {
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;

    setInterval(async () => {
        const deteccion = await faceapi
            .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
            .withFaceExpressions();

        if (deteccion) {
            const expresiones = deteccion.expressions;
            const emocionTop  = Object.entries(expresiones).sort((a, b) => b[1] - a[1])[0][0];

            const mapa = {
                happy:     'feliz',
                sad:       'triste',
                angry:     'enojado',
                neutral:   'relajado',
                fearful:   'triste',
                disgusted: 'enojado',
                surprised: 'feliz'
            };

            const estadoFinal = mapa[emocionTop] || 'relajado';
            textoEstado.textContent = etiquetas[estadoFinal];

            if (estadoFinal !== ultimoEstado) {
                ultimoEstado = estadoFinal;
                cambiarColor(estadoFinal);
                buscarCanciones(estadoFinal);
            }
        } else {
            textoEstado.textContent = "No detecto tu cara. Acercate a la camara.";
        }
    }, 3000);
}

async function buscarCanciones(estado) {
    cambiarColor(estado);
    textoEstado.textContent = etiquetas[estado];
    ultimoEstado = estado;

    const lista = document.getElementById('canciones-lista');
    lista.innerHTML = '<p class="cargando">Buscando canciones...</p>';

    try {
        const respuesta = await fetch('canciones.php?estado=' + estado);
        const canciones = await respuesta.json();

        if (canciones.error) {
            lista.innerHTML = '<p class="error-msg">' + canciones.error + '</p>';
            return;
        }

        lista.innerHTML = canciones.map(c => `
            <div class="cancion-card">
                <div class="cancion-info">
                    <span class="cancion-titulo">${c.titulo}</span>
                    <span class="cancion-artista">${c.artista}</span>
                    <span class="cancion-genero">${c.genero}</span>
                </div>
                <a href="${c.link_youtube}" target="_blank" class="btn-play">Reproducir</a>
            </div>
        `).join('');

    } catch (err) {
        lista.innerHTML = '<p class="error-msg">Error al cargar canciones.</p>';
    }
}

cargarModelos();
</script>

</body>
</html>
