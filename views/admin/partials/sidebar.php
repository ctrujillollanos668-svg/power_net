<?php
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 1) {
    header("Location: index.php");
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
        <?php sl('index.php?action=dashboard',    '📊', 'Dashboard',    $p, 'dashboard'); ?>
        <?php sl('index.php?action=pedidos',      '📦', 'Pedidos',      $p, 'pedidos'); ?>
        <?php sl('index.php?action=envios',       '🚚', 'Envíos',       $p, 'envios'); ?>

        <div class="sidebar-section">Catálogo</div>
        <?php sl('index.php?action=productos',    '🛒', 'Productos',    $p, 'productos'); ?>
        <?php sl('index.php?action=categorias',   '🗂️', 'Categorías',   $p, 'categorias'); ?>
        <?php sl('index.php?action=inventario',   '📦', 'Inventario',   $p, 'inventario'); ?>
        <?php sl('index.php?action=ofertas_admin','🏷️', 'Ofertas',      $p, 'ofertas_admin'); ?>
        <?php sl('index.php?action=proveedores',  '🏭', 'Proveedores',  $p, 'proveedores'); ?>

        <div class="sidebar-section">Finanzas</div>
        <?php sl('index.php?action=pagos',        '💳', 'Pagos',        $p, 'pagos'); ?>
        <?php sl('index.php?action=ventas',       '💰', 'Ventas',       $p, 'ventas'); ?>
        <?php sl('index.php?action=devoluciones', '🔁', 'Devoluciones', $p, 'devoluciones'); ?>

    </nav>

</aside>
