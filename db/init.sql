-- Esquema de la base de datos para la app de notas
-- Incluye usuarios de prueba para el laboratorio de captura de tráfico.

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Usuarios de prueba (user1/password1, user2/password2)
-- Las contraseñas se guardan con password_hash() (bcrypt) en la BD,
-- tal como lo haría cualquier app PHP real; el texto plano solo
-- viaja por la red al momento del login (ese es el punto del lab).
-- Hash generado con: password_hash('password1', PASSWORD_BCRYPT)
INSERT INTO users (username, password) VALUES
    ('user1', '$2y$10$Z1m0U/svbf7ZIFq7jF5t4.HwsWFMVck31ZhnLeFQS9M62WziYRSCC'), -- password1
    ('user2', '$2y$10$v8qYhFjzY0VSu8D7Xe1i..osHCOQAkMikBnUMzk6v/Ipuv0RQvK.W'); -- password2

INSERT INTO notes (user_id, title, content) VALUES
    (1, 'Nota de bienvenida', 'Esta es una nota de ejemplo para user1.'),
    (2, 'Lista de tareas', 'Comprar café, revisar el servidor, dormir.');
