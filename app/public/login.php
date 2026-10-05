<?php
session_start();
require_once __DIR__ . '/../src/db.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: notes.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Usuario y contraseña son obligatorios.';
    } else {
        $mysqli = get_db_connection();
        $stmt = $mysqli->prepare('SELECT id, password FROM users WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        $mysqli->close();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            header('Location: notes.php');
            exit;
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    }
}

$pageTitle = 'Iniciar sesión';
$showNavbar = false;
require __DIR__ . '/../src/templates/header.php';
?>

<main class="login-shell d-flex align-items-center justify-content-center px-3 py-5 flex-grow-1">
    <div class="app-card fade-in-up p-4 p-sm-5" style="max-width: 420px; width: 100%;">

        <div class="d-flex flex-column align-items-center text-center mb-4">
            <div class="login-badge mb-3">🔐</div>
            <h1 class="h4 brand-font fw-semibold mb-1">Bienvenido de vuelta</h1>
            <p class="text-muted small mb-0">Inicia sesión para ver tus notas</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger py-2 small" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" action="login.php" autocomplete="off" novalidate>
            <div class="mb-3">
                <label for="username" class="form-label small fw-medium">Usuario</label>
                <input
                    type="text"
                    class="form-control form-control-lg"
                    id="username"
                    name="username"
                    placeholder="tu.usuario"
                    autofocus
                    required
                >
            </div>

            <div class="mb-4">
                <label for="password" class="form-label small fw-medium">Contraseña</label>
                <div class="input-group">
                    <input
                        type="password"
                        class="form-control form-control-lg"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                        data-password-input
                        required
                    >
                    <button
                        class="btn btn-outline-secondary password-toggle"
                        type="button"
                        data-password-toggle
                        aria-label="Mostrar contraseña"
                    >Mostrar</button>
                </div>
            </div>

            <button type="submit" class="btn btn-brand btn-lg w-100 rounded-3">
                Entrar
            </button>
        </form>

        <div class="text-center mt-4 pt-3 border-top">
            <p class="text-muted small mb-0">
                Usuarios de prueba:<br>
                <code>user1 / password1</code> &nbsp;·&nbsp; <code>user2 / password2</code>
            </p>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../src/templates/footer.php'; ?>
