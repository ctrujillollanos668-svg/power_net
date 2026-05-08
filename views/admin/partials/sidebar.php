<?php
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 1) {
    header("Location: /power-net/public/index.php");
    exit;
}
$p = basename($_SERVER['PHP_SELF'], '.php');
function sl($href, $icon, $label, $p, $match) {
    $a = ($p === $match) ? 'active' : '';
    echo "<a href=\"$href\" class=\"s-link $a\"><span>$icon</span> $label</a>";
}
?>

<aside class="admin-sidebar">

    <div class="sidebar-logo">
        ⚡ <span>Power Net</span>
    </div>

    <nav class="sidebar-nav">

        <div class="sidebar-section">Principal</div>
        <?php sl('/power-net/views/admin/dashboard.php',       '📊', 'Dashboard',    $p, 'dashboard'); ?>
        <?php sl('/power-net/views/admin/pedidos/pedidos.php', '📦', 'Pedidos',      $p, 'pedidos'); ?>
        <?php sl('/power-net/views/admin/envios/envios.php',   '🚚', 'Envíos',       $p, 'envios'); ?>

        <div class="sidebar-section">Catálogo</div>
        <?php sl('/power-net/views/admin/productos/productos.php',    '🛒', 'Productos',   $p, 'productos'); ?>
        <?php sl('/power-net/views/admin/categoria/categorias.php',   '🗂️', 'Categorías',  $p, 'categorias'); ?>
        <?php sl('/power-net/views/admin/inventario/inventario.php',  '📦', 'Inventario',  $p, 'inventario'); ?>
        <?php sl('/power-net/views/admin/ofertas/ofertas.php',        '🏷️', 'Ofertas',     $p, 'ofertas'); ?>
        <?php sl('/power-net/views/admin/proveedores/proveedores.php','🏭', 'Proveedores', $p, 'proveedores'); ?>

        <div class="sidebar-section">Finanzas</div>
        <?php sl('/power-net/views/admin/pago/pago.php',       '💳', 'Pagos',        $p, 'pago'); ?>
        <?php sl('/power-net/views/admin/pago/ventas.php',     '💰', 'Ventas',       $p, 'ventas'); ?>
        <?php sl('/power-net/views/admin/pago/devolucion.php', '🔁', 'Devoluciones', $p, 'devolucion'); ?>

    </nav>

</aside>
