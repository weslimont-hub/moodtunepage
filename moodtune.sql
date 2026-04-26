CREATE DATABASE IF NOT EXISTS moodtune;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    es_admin TINYINT(1) DEFAULT 0,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE canciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    artista VARCHAR(150) NOT NULL,
    genero VARCHAR(80) NOT NULL,
    estado_animo VARCHAR(50) NOT NULL,
    link_youtube VARCHAR(300)
);

CREATE TABLE historial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    estado_detectado VARCHAR(50) NOT NULL,
    cancion_id INT NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (cancion_id) REFERENCES canciones(id)
);

-- Admin creado directamente. Contrasena: wesandluisa
INSERT INTO usuarios (nombre, email, password, es_admin) VALUES
('Admin', 'admin@moodtune.com', '31a35fdf16c27aaaf7aa1567d8f4a0bf', 1);

-- FELIZ
INSERT INTO canciones (titulo, artista, genero, estado_animo, link_youtube) VALUES
('Follow the Leader', 'Wisin & Yandel', 'Reggaeton', 'feliz', 'https://www.youtube.com/watch?v=rFnBa4B_J3k'),
('Moves Like Jagger', 'Maroon 5', 'Pop', 'feliz', 'https://www.youtube.com/watch?v=iEPTlhErT-c'),
('Jump', 'Kris Kross', 'Hip-Hop', 'feliz', 'https://www.youtube.com/watch?v=010KHlQMBvQ'),
('Like Jennie', 'Jennie', 'K-Pop', 'feliz', 'https://www.youtube.com/watch?v=vqzBqLwp9jU'),
('Gangnam Style', 'PSY', 'K-Pop', 'feliz', 'https://www.youtube.com/watch?v=9bZkp7q19f0'),
('APT.', 'Rose ft. Bruno Mars', 'K-Pop', 'feliz', 'https://www.youtube.com/watch?v=ekr2nIex040'),

-- TRISTE
('Im a Mess', 'Bebe Rexha', 'Pop', 'triste', 'https://www.youtube.com/watch?v=kOHB85vDuow'),
('Reflections', 'The Neighbourhood', 'Indie', 'triste', 'https://www.youtube.com/watch?v=Xo3VEX4GHiQ'),
('Doubt', 'twenty one pilots', 'Indie', 'triste', 'https://www.youtube.com/watch?v=MEiVnNNpJLA'),
('Happier', 'Olivia Rodrigo', 'Pop', 'triste', 'https://www.youtube.com/watch?v=9rJUxrS_MXU'),
('idfc', 'blackbear', 'R&B', 'triste', 'https://www.youtube.com/watch?v=N8xMy_XBROU'),
('Sweet Delusion', 'Hozier', 'Indie', 'triste', 'https://www.youtube.com/results?search_query=hozier+sweet+delusion'),

-- RELAJADO
('Borderline', 'Tame Impala', 'Indie', 'relajado', 'https://www.youtube.com/watch?v=sBzrzS1Ag_g'),
('DIM', 'SZA', 'R&B', 'relajado', 'https://www.youtube.com/results?search_query=sza+dim'),
('To Make You Feel My Love', 'Adele', 'Balada', 'relajado', 'https://www.youtube.com/watch?v=OgFjBTbBgMk'),
('Metamorphosis', 'Essenger', 'Electronica', 'relajado', 'https://www.youtube.com/results?search_query=essenger+metamorphosis'),
('Runaway', 'Kanye West', 'Hip-Hop', 'relajado', 'https://www.youtube.com/watch?v=Ig7n-TFBEkY'),
('No One Noticed', 'The Neighbourhood', 'Indie', 'relajado', 'https://www.youtube.com/results?search_query=the+neighbourhood+no+one+noticed'),

-- ENOJADO
('Enemy', 'Imagine Dragons', 'Rock', 'enojado', 'https://www.youtube.com/watch?v=D9G1VOjN_84'),
('Middle Finger', 'Bohnes', 'Rock', 'enojado', 'https://www.youtube.com/results?search_query=bohnes+middle+finger'),
('Angry Too', 'Lola Blanc', 'Pop', 'enojado', 'https://www.youtube.com/results?search_query=lola+blanc+angry+too'),
('Your Idol', 'Sleep Token', 'Rock', 'enojado', 'https://www.youtube.com/results?search_query=sleep+token+your+idol'),
('Villain', 'Stellar', 'K-Pop', 'enojado', 'https://www.youtube.com/results?search_query=stellar+villain+kpop'),
('Control', 'Halsey', 'Pop', 'enojado', 'https://www.youtube.com/watch?v=HYna7N0RhQA');
