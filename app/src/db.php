<?php
// Conexión a la base de datos MySQL/MariaDB usando mysqli + prepared statements.

function get_db_connection(): mysqli
{
    $host = getenv('DB_HOST') ?: 'db';
    $name = getenv('DB_NAME') ?: 'notesdb';
    $user = getenv('DB_USER') ?: 'notesuser';
    $pass = getenv('DB_PASS') ?: 'notespass';

    $mysqli = mysqli_init();
    if (!$mysqli->real_connect($host, $user, $pass, $name)) {
        http_response_code(500);
        die('Error de conexión a la base de datos.');
    }
    $mysqli->set_charset('utf8mb4');

    return $mysqli;
}
