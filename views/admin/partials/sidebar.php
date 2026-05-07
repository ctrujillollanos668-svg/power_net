<?php
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 1) {
    header("Location: /power-net/public/index.php");
    exit;
}

// Detectar página activa
$paginaActual = basename($_SERVER['PHP_SELF']);
$dirActual    = basename(dirname($_SERVER['PHP_SELF']));
?>

<div class="bg-dark text-white p-3"
     style="width:250px;height:100vh;position:fixed;top:0;left:0;overflow-y:auto;">

    <div>
        <h4 class="mb-4 fw-bold">⚡ Power Net</h4>

        <ul class="nav flex-column gap-1">

            <li class="nav-item">
                <a href="/power-net/views/admin/dashboard.php"
                   class="nav-link text-white rounded <?= $paginaActual === 'dashboard.php' ? 'bg-secondary' : '' ?>">
                    📊 Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a href="/power-net/views/admin/pedidos/pedidos.php"
                   class="nav-link text-white rounded <?= $paginaActual === 'pedidos.php' ? 'bg-secondary' : '' ?>">
                    📦 Pedidos
                </a>
            </li>

            <li class="nav-item">
                <a href="/power-net/views/admin/categoria/categorias.php"
                   class="nav-link text-white rounded <?= $paginaActual === 'categorias.php' ? 'bg-secondary' : '' ?>">
                    🗂️ Categorías
                </a>
            </li>

            <li class="nav-item">
                <a href="/power-net/views/admin/productos/productos.php"
                   class="nav-link text-white rounded <?= $paginaActual === 'productos.php' ? 'bg-secondary' : '' ?>">
                    🛒 Productos
                </a>
            </li>

            <li class="nav-item">
                <a href="/power-net/views/admin/inventario/inventario.php"
                   class="nav-link text-white rounded <?= $paginaActual === 'inventario.php' ? 'bg-secondary' : '' ?>">
                    📊 Inventario
                </a>
            </li>

            <li class="nav-item">
                <a href="/power-net/views/admin/pago/pago.php"
                   class="nav-link text-white rounded <?= $paginaActual === 'pago.php' ? 'bg-secondary' : '' ?>">
                    💳 Pagos
                </a>
            </li>

            <li class="nav-item">
                <a href="/power-net/views/admin/pago/ventas.php"
                   class="nav-link text-white rounded <?= $paginaActual === 'ventas.php' ? 'bg-secondary' : '' ?>">
                    💰 Ventas
                </a>
            </li>

            <li class="nav-item">
                <a href="/power-net/views/admin/pago/devolucion.php"
                   class="nav-link text-white rounded <?= $paginaActual === 'devolucion.php' ? 'bg-secondary' : '' ?>">
                    🔁 Devoluciones
                </a>
            </li>

            <li class="nav-item mt-3 border-top pt-3">
                <a href="/power-net/public/index.php"
                   class="nav-link text-white-50 rounded">
                    🏠 Ver tienda
                </a>
            </li>

            <li class="nav-item">
                <a href="/power-net/public/index.php?action=logout"
                   class="nav-link text-danger rounded">
                    🚪 Cerrar sesión
                </a>
            </li>

        </ul>
    </div>
</div>
