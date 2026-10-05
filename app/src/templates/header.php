<?php
/**
 * Template compartido: <head> + apertura de <body> + navbar opcional.
 * Variables esperadas antes del include:
 *   string $pageTitle   Título de la pestaña del navegador.
 *   bool   $showNavbar  Si se muestra la barra superior (requiere sesión).
 */
$pageTitle  = $pageTitle  ?? 'Notas';
$showNavbar = $showNavbar ?? false;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle) ?> — App de Notas</title>

    <!-- Bootstrap (componentes: botones, cards, alerts) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tailwind (utilidades de layout / espaciado / flex-grid) -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Estilos propios -->
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="d-flex flex-column">

<?php if ($showNavbar): ?>
    <nav class="navbar navbar-expand app-navbar shadow-sm sticky-top">
        <div class="container">
            <a href="notes.php" class="navbar-brand brand-font fw-semibold d-flex align-items-center gap-2">
                <span class="fs-5">📝</span> <span>Mis notas</span>
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted small d-none d-sm-inline">
                    Hola, <strong><?= htmlspecialchars($_SESSION['username'] ?? '') ?></strong>
                </span>
                <a href="logout.php" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Salir</a>
            </div>
        </div>
    </nav>
<?php endif; ?>
