// Abre el modal de login cuando una acción requiere autenticación.
function abrirLogin() {
    new bootstrap.Modal(document.getElementById('loginModal')).show();
}

// Ajusta la cantidad en tarjetas de producto y sincroniza inputs hidden de formularios.
function cambiarCantidad(btn, cambio) {
    const box = btn.closest('.pcard__qty');
    const input = box.querySelector('.cantidad-input');
    const body = btn.closest('.pcard__body');
    let cantidad = parseInt(input.value);
    const min = parseInt(input.min);
    const max = parseInt(input.max);
    cantidad += cambio;
    if (cantidad < min) cantidad = min;
    if (cantidad > max) cantidad = max;
    input.value = cantidad;
    body.querySelectorAll('.cantidad-hidden, .cantidad-hidden-buy').forEach(h => { h.value = cantidad; });
}

// Marca o desmarca favoritos vía fetch sin recargar la página.
function toggleFavorito(btn, idProducto) {
    if (!document.querySelector('[data-logueado]')) { abrirLogin(); return; }
    const formData = new FormData();
    formData.append('id_producto', idProducto);
    fetch('index.php?action=toggle_favorito', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.error === 'no_auth') { abrirLogin(); return; }
            const corazon = btn.querySelector('.corazon');
            if (data.favorito) { corazon.textContent = '♥'; btn.classList.add('activo'); btn.title = 'Quitar de favoritos'; }
            else { corazon.textContent = '♡'; btn.classList.remove('activo'); btn.title = 'Agregar a favoritos'; }
        })
        .catch(() => abrirLogin());
}

// Controla la transición entre modal login y modal recuperar contraseña.
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('abrirRecuperar')?.addEventListener('click', function (e) {
        e.preventDefault();
        const loginEl = document.getElementById('loginModal');
        const recuperarEl = document.getElementById('recuperarModal');
        const loginModal = bootstrap.Modal.getInstance(loginEl) || new bootstrap.Modal(loginEl);
        loginModal.hide();
        loginEl.addEventListener('hidden.bs.modal', function () {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            new bootstrap.Modal(recuperarEl).show();
        }, { once: true });
    });
});
