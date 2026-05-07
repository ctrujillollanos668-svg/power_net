<div class="container mt-4">

    <!-- BOTÓN PARA VOLVER -->
    <a href="index.php?action=mi_perfil" class="btn btn-outline-secondary mb-4">
        ← Volver a mi perfil
    </a>

    <!-- ===================== -->
    <!-- 🧑 EDITAR PERFIL -->
    <!-- ===================== -->
    <div class="card shadow-sm p-4 mb-4">

        <h4 class="mb-3">👤 Datos de tu cuenta</h4>

        <p class="text-muted">
            Actualiza la información principal de tu cuenta.
        </p>

        <!-- FORMULARIO PERFIL -->
        <form method="POST" action="index.php?action=actualizar_perfil">

            <!-- NOMBRE -->
            <div class="mb-3">
                <label class="form-label">Nombre</label>

                <input type="text"
                       name="nombre"
                       class="form-control"
                       value="<?= $_SESSION['usuario']['nombre'] ?? '' ?>"
                       required>
            </div>

            <!-- CORREO -->
            <div class="mb-3">
                <label class="form-label">Correo</label>

                <input type="email"
                       name="correo"
                       class="form-control"
                       value="<?= $_SESSION['usuario']['correo'] ?? $_SESSION['usuario']['email'] ?? '' ?>"
                       required>
            </div>

            <button type="submit" class="btn btn-primary">
                Guardar cambios
            </button>

        </form>

    </div>

    <!-- ===================== -->
    <!-- 🔒 CAMBIAR CONTRASEÑA -->
    <!-- ===================== -->
    <div class="card shadow-sm p-4">

        <h5 class="mb-3">🔒 Cambiar contraseña</h5>

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
            <!-- MENSAJE DE VALIDACIÓN -->
<div id="mensajePassword" class="mt-2"></div>

            <button type="submit" class="btn btn-dark">
                Cambiar contraseña
            </button>

        </form>
<script>
// CAMPOS
const nueva = document.querySelector('input[name="password_nueva"]');
const confirmar = document.querySelector('input[name="password_confirmar"]');
const mensaje = document.getElementById('mensajePassword');

// VALIDACIÓN EN TIEMPO REAL
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

// BLOQUEAR ENVÍO SI NO COINCIDEN
const form = document.querySelector('form[action*="cambiar_password"]');

form.addEventListener('submit', function(e) {
    if (nueva.value !== confirmar.value) {
        e.preventDefault();
        alert("Las contraseñas no coinciden");
    }
});
</script>
    </div>

</div>