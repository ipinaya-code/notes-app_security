<?php
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_login();

$userId = current_user_id();
$mysqli = get_db_connection();

// Crear nota
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content'] ?? '');
    if ($title !== '') {
        $stmt = $mysqli->prepare('INSERT INTO notes (user_id, title, content) VALUES (?, ?, ?)');
        $stmt->bind_param('iss', $userId, $title, $content);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: notes.php');
    exit;
}

// Eliminar nota
if (isset($_GET['delete'])) {
    $noteId = (int) $_GET['delete'];
    $stmt = $mysqli->prepare('DELETE FROM notes WHERE id = ? AND user_id = ?');
    $stmt->bind_param('ii', $noteId, $userId);
    $stmt->execute();
    $stmt->close();
    header('Location: notes.php');
    exit;
}

// Listar notas del usuario
$stmt = $mysqli->prepare('SELECT id, title, content, created_at FROM notes WHERE user_id = ? ORDER BY created_at DESC');
$stmt->bind_param('i', $userId);
$stmt->execute();
$notes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$mysqli->close();

$pageTitle = 'Mis notas';
$showNavbar = true;
require __DIR__ . '/../src/templates/header.php';
?>

<main class="container flex-grow-1 py-4" style="max-width: 720px;">

    <div class="app-card fade-in-up p-4 mb-4">
        <h2 class="h6 brand-font fw-semibold mb-3 d-flex align-items-center gap-2">
            <span>✍️</span> Nueva nota
        </h2>
        <form method="post" action="notes.php">
            <div class="mb-2">
                <input
                    type="text"
                    class="form-control"
                    name="title"
                    placeholder="Título de la nota"
                    required
                >
            </div>
            <div class="mb-3">
                <textarea
                    class="form-control"
                    name="content"
                    rows="3"
                    placeholder="Escribe algo..."
                ></textarea>
            </div>
            <button type="submit" class="btn btn-brand rounded-3 px-4">
                Guardar nota
            </button>
        </form>
    </div>

    <?php if (empty($notes)): ?>
        <div class="text-center text-muted py-5">
            <div class="fs-1 mb-2">🗒️</div>
            <p class="mb-0">Aún no tienes notas. ¡Crea la primera!</p>
        </div>
    <?php endif; ?>

    <div class="d-flex flex-column gap-3">
        <?php foreach ($notes as $note): ?>
            <div class="app-card note-card p-3 p-sm-4">
                <div class="d-flex justify-content-between align-items-start gap-3">
                    <h3 class="h6 brand-font fw-semibold mb-2"><?= htmlspecialchars($note['title']) ?></h3>
                    <a
                        href="notes.php?delete=<?= (int)$note['id'] ?>"
                        class="text-danger small text-nowrap"
                        data-delete-note
                        data-note-title="<?= htmlspecialchars($note['title']) ?>"
                    >Eliminar</a>
                </div>
                <p class="mb-2 text-body"><?= nl2br(htmlspecialchars($note['content'])) ?></p>
                <p class="text-muted small mb-0"><?= htmlspecialchars($note['created_at']) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php require __DIR__ . '/../src/templates/footer.php'; ?>
