<div class="container mt-4">

    <!-- BOTÓN VOLVER -->
    <a href="index.php?action=mi_perfil" class="btn btn-outline-secondary mb-4">
        ← Volver a mi perfil
    </a>

    <!-- 🔒 CAMBIAR CONTRASEÑA -->
    <div class="card shadow-sm p-4">

        <h4 class="mb-3">🔒 Seguridad</h4>

        <p class="text-muted">
            Aquí puedes cambiar tu contraseña.
        </p>

        <!-- FORMULARIO -->
        <form method="POST" action="index.php?action=cambiar_password">

            <!-- ACTUAL -->
            <div class="mb-3">
                <label class="form-label">Contraseña actual</label>
                <input type="password"
                       name="password_actual"
                       class="form-control"
                       required>
            </div>

            <!-- NUEVA -->
            <div class="mb-3">
                <label class="form-label">Nueva contraseña</label>
                <input type="password"
                       name="password_nueva"
                       class="form-control"
                       required>
            </div>

            <!-- CONFIRMAR -->
            <div class="mb-3">
                <label class="form-label">Confirmar nueva contraseña</label>
                <input type="password"
                       name="password_confirmar"
                       class="form-control"
                       required>
            </div>

            <!-- MENSAJE -->
            <div id="mensajePassword" class="mt-2"></div>

            <!-- BOTÓN -->
            <button type="submit" class="btn btn-dark mt-3">
                Cambiar contraseña
            </button>

        </form>

    </div>

</div>

<!-- VALIDACIÓN EN VIVO -->
<script>
const nueva = document.querySelector('input[name="password_nueva"]');
const confirmar = document.querySelector('input[name="password_confirmar"]');
const mensaje = document.getElementById('mensajePassword');

confirmar.addEventListener('input', function() {

    if (confirmar.value === "") {
        mensaje.innerHTML = "";
        return;
    }

    if (nueva.value !== confirmar.value) {
        mensaje.innerHTML = "❌ Las contraseñas no coinciden";
        mensaje.style.color = "red";
    } else {
        mensaje.innerHTML = "✔ Las contraseñas coinciden";
        mensaje.style.color = "green";
    }
});
</script>