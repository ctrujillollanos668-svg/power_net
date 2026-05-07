<div class="container mt-5" style="max-width:500px;">

    <h3 class="mb-3">Nueva contraseña</h3>

    <form method="POST" action="index.php?action=guardar_nueva_password">

        <!-- TOKEN OCULTO -->
        <input type="hidden" name="token" value="<?= $_GET['token'] ?? '' ?>">

        <!-- NUEVA -->
        <div class="mb-3">
            <label>Nueva contraseña</label>
            <input type="password" name="password_nueva" class="form-control" required>
        </div>

        <!-- CONFIRMAR -->
        <div class="mb-3">
            <label>Confirmar contraseña</label>
            <input type="password" name="password_confirmar" class="form-control" required>
        </div>

        <button class="btn btn-primary w-100">
            Guardar contraseña
        </button>

    </form>

</div>