<header class="bg-white shadow-sm mb-4">

    <!-- HEADER SUPERIOR -->
    <div class="container-fluid py-3 position-relative">

        <!-- LOGO FIJO A LA IZQUIERDA -->
        <a href="index.php"
           class="position-absolute start-0 ms-4 d-flex align-items-center text-decoration-none">

            <img src="/power-net/img/OIP (1).webp"
                 style="width:40px; height:40px; object-fit:cover;"
                 class="me-2">

            <span class="fw-bold fs-4 text-primary">
                Power Net
            </span>
        </a>

        <!-- BUSCADOR CENTRADO -->
        <form method="GET" action="index.php" class="mx-auto" style="max-width:600px;position:relative;">
            <input type="text"
                   name="buscar"
                   placeholder="🔍  Buscar productos..."
                   value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>"
                   class="form-control"
                   style="border-radius:50px;padding:10px 48px 10px 20px;border:2px solid #e5e7eb;font-size:14px;">
            <button type="submit"
                    style="position:absolute;right:6px;top:50%;transform:translateY(-50%);border:none;background:#1a1a2e;color:#fff;border-radius:50px;padding:5px 16px;font-size:13px;font-weight:700;cursor:pointer;">
                Buscar
            </button>
        </form>

        <!-- 🛒 CARRITO ARRIBA A LA DERECHA -->
        <a href="index.php?action=carrito"
           class="position-absolute end-0 me-5 text-dark text-decoration-none"
           style="top:18px;">

            <span style="font-size:26px;">🛒</span>

            <?php if (!empty($_SESSION['carrito'])): ?>
                <span style="
                    position:absolute;
                    top:-6px;
                    right:-10px;
                    background:red;
                    color:white;
                    font-size:12px;
                    padding:2px 6px;
                    border-radius:50%;
                ">
                    <?= array_sum($_SESSION['carrito']) ?>
                </span>
            <?php endif; ?>

        </a>

    </div>

    <!-- MENÚ -->
    <nav class="border-top">
        <div class="container d-flex justify-content-between align-items-center py-3">

            <a href="index.php" class="text-dark text-decoration-none">Inicio</a>

            <!-- DROPDOWN CATEGORÍAS -->
            <?php
            // Cargar categorías activas para el menú
            require_once __DIR__ . '/../../../models/Category.php';
            $catMenu = new Category();
            $listaCats = $catMenu->obtenerActivas();
            ?>
            <div class="dropdown">
                <a href="#"
                   class="text-dark text-decoration-none dropdown-toggle"
                   data-bs-toggle="dropdown"
                   aria-expanded="false">
                    Categorías
                </a>
                <ul class="dropdown-menu shadow border-0 mt-2" style="min-width:200px;border-radius:12px;overflow:hidden;">
                    <li>
                        <a class="dropdown-item py-2 fw-semibold" href="index.php">
                            📦 Todas las categorías
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <?php if (!empty($listaCats)): ?>
                        <?php foreach ($listaCats as $cat): ?>
                            <li>
                                <a class="dropdown-item py-2"
                                   href="index.php?categoria=<?= $cat['id_categoria'] ?>">
                                    <?= htmlspecialchars($cat['nombre_categoria']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li><span class="dropdown-item text-muted">Sin categorías</span></li>
                    <?php endif; ?>
                </ul>
            </div>

            <a href="index.php?oferta=1" class="text-dark text-decoration-none">Ofertas</a>
            <a href="index.php?action=mis_pedidos" class="text-dark text-decoration-none">Mis Pedidos</a>

            <?php if (isset($_SESSION['usuario'])): ?>

                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                        👤 Mi cuenta
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="index.php?action=mi_perfil">Mi perfil</a></li>
                        <li><a class="dropdown-item" href="index.php?action=carrito">🛒 Mi carrito</a></li>
                        <li><a class="dropdown-item" href="index.php?action=mis_pedidos">📦 Mis pedidos</a></li>
                        <li><a class="dropdown-item" href="index.php?action=mis_favoritos">❤️ Mis favoritos</a></li>
                        <li><a class="dropdown-item" href="index.php?action=medios_pago">💳 Métodos de pago</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="index.php?action=logout">Cerrar sesión</a></li>
                    </ul>
                </div>

            <?php endif; ?>

        </div>
    </nav>

</header>
