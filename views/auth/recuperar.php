<div class="container mt-5" style="max-width: 500px;">

    <!-- TÍTULO -->
    <h3 class="mb-3">Recuperar contraseña</h3>

    <!-- TEXTO -->
    <p class="text-muted">
        Escribe tu correo y te enviaremos un enlace para cambiar tu contraseña.
    </p>

    <!-- FORMULARIO -->
    <form method="POST" action="index.php?action=enviar_recuperacion">

        <!-- CORREO -->
        <div class="mb-3">
            <label class="form-label">Correo electrónico</label>
            <input type="email"
                   name="email"
                   class="form-control"
                   required>
        </div>

        <!-- BOTÓN -->
        <button type="submit" class="btn btn-primary w-100">
            Enviar enlace
        </button>

    </form>

</div>