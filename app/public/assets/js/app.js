// ============================================================
// app.js — interacción del lado del cliente (sin frameworks)
// No reemplaza la validación del servidor, solo mejora la UX.
// ============================================================

document.addEventListener('DOMContentLoaded', () => {
    initPasswordToggle();
    initDeleteConfirm();
    initFadeIn();
});

/**
 * Botón de "ojito" para mostrar/ocultar la contraseña en el login.
 */
function initPasswordToggle() {
    const toggle = document.querySelector('[data-password-toggle]');
    const input = document.querySelector('[data-password-input]');
    if (!toggle || !input) return;

    toggle.addEventListener('click', () => {
        const isHidden = input.getAttribute('type') === 'password';
        input.setAttribute('type', isHidden ? 'text' : 'password');
        toggle.textContent = isHidden ? 'Ocultar' : 'Mostrar';
        toggle.setAttribute('aria-label', isHidden ? 'Ocultar contraseña' : 'Mostrar contraseña');
    });
}

/**
 * Confirmación accesible antes de eliminar una nota, con una
 * pequeña animación de salida antes de navegar al enlace real.
 */
function initDeleteConfirm() {
    document.querySelectorAll('[data-delete-note]').forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();
            const card = link.closest('.note-card');
            const title = link.getAttribute('data-note-title') || 'esta nota';

            if (!window.confirm(`¿Eliminar "${title}"? Esta acción no se puede deshacer.`)) {
                return;
            }

            if (card) {
                card.classList.add('removing');
                setTimeout(() => { window.location.href = link.href; }, 180);
            } else {
                window.location.href = link.href;
            }
        });
    });
}

/**
 * Pequeña animación de entrada para tarjetas visibles al cargar.
 */
function initFadeIn() {
    document.querySelectorAll('.note-card, .app-card').forEach((el, i) => {
        el.classList.add('fade-in-up');
        el.style.animationDelay = `${Math.min(i * 40, 240)}ms`;
    });
}
