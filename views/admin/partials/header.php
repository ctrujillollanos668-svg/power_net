<nav class="bg-dark text-white px-4 py-2 d-flex justify-content-between align-items-center"
     style="position: fixed; top: 0; left: 250px; right: 0; z-index: 1000;">

    <!-- ESPACIO (NO LOGO) -->
    <div></div>

    <!-- USUARIO -->
    <div class="d-flex align-items-center gap-3">

        <div class="text-end">
            <div class="fw-semibold">
                <?= $_SESSION['usuario']['nombre'] ?>
            </div>

            <small class="text-light">
                <?php if ($_SESSION['usuario']['rol'] == 1): ?>
                    Administrador
                <?php else: ?>
                    Cliente
                <?php endif; ?>
            </small>
        </div>

        <!-- ICONO USUARIO -->
        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center"
             style="width:35px; height:35px;">
            <i class="bi bi-person text-white"></i>
        </div>

        <!-- BOTÓN LOGOUT -->
        <a href="/power-net/public/index.php?action=logout" 
           class="btn btn-danger btn-sm">
            Salir
        </a>

    </div>

</nav>
