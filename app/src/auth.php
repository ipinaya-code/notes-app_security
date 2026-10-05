<?php
// Utilidades de autenticación / sesión.

function require_login(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function current_user_id(): ?int
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['user_id'] ?? null;
}
