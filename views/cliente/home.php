<?php
// Variables disponibles desde index.php:
// $productos, $product, $categorias, $filtroCategoria, $filtroBuscar,
// $filtroOrden, $filtroPrecioMin, $filtroPrecioMax, $filtroOfertas, $favoritoIds

// Valores por defecto para evitar errores si alguna variable no está definida
$filtroCategoria = $filtroCategoria ?? null;
$filtroBuscar    = $filtroBuscar    ?? null;
$filtroOrden     = $filtroOrden     ?? null;
$filtroPrecioMin = $filtroPrecioMin ?? null;
$filtroPrecioMax = $filtroPrecioMax ?? null;
$filtroOfertas   = $filtroOfertas   ?? false;
$favoritoIds     = $favoritoIds     ?? [];
$categorias      = $categorias      ?? [];
$productos       = $productos       ?? [];
?>

<!-- HERO BANNER -->
<div class="hero-banner">
    <div class="hero-content">
        <p class="hero-sub">Tecnología para tu hogar y negocio</p>
        <h1 class="hero-title">Los mejores productos<br>al mejor precio</h1>
        <a href="#productos" class="btn-hero">Ver catálogo</a>
    </div>
</div>

<!-- PRODUCTOS -->
<div class="container-xl py-5" id="productos">
<div class="row g-4">
<div class="col-12">

    <!-- Barra de filtros -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div class="section-header" style="flex:1;">
            <h2 class="section-title">
                <?php if (!empty($filtroOfertas)): ?>
                    🔥 Ofertas
                <?php elseif (!empty($filtroCategoria)): ?>
                    <?php
                    $catActual = array_filter($categorias ?? [], fn($c) => $c['id_categoria'] == $filtroCategoria);
                    $catActual = reset($catActual);
                    echo '📂 ' . htmlspecialchars($catActual['nombre_categoria'] ?? 'Categoría');
                    ?>
                <?php elseif (!empty($filtroBuscar)): ?>
                    🔍 "<?= htmlspecialchars($filtroBuscar) ?>"
                <?php else: ?>
                    Productos destacados
                <?php endif; ?>
            </h2>
            <span class="section-line"></span>
            <span class="text-muted ms-2" style="font-size:13px;white-space:nowrap;">
                <?= count($productos) ?> <?= count($productos) === 1 ? 'resultado' : 'resultados' ?>
            </span>
        </div>

        <form method="GET" action="index.php" class="d-flex align-items-center gap-2 flex-wrap">
            <?php if (!empty($filtroCategoria)): ?>
                <input type="hidden" name="categoria" value="<?= htmlspecialchars($filtroCategoria) ?>">
            <?php endif; ?>
            <input type="text" name="buscar" class="form-control form-control-sm"
                   style="width:180px;border-radius:20px;" placeholder="Buscar..."
                   value="<?= htmlspecialchars($filtroBuscar ?? '') ?>">
            <input type="number" name="precio_min" class="form-control form-control-sm"
                   style="width:100px;border-radius:20px;" placeholder="$ Mín"
                   value="<?= htmlspecialchars($filtroPrecioMin ?? '') ?>">
            <input type="number" name="precio_max" class="form-control form-control-sm"
                   style="width:100px;border-radius:20px;" placeholder="$ Máx"
                   value="<?= htmlspecialchars($filtroPrecioMax ?? '') ?>">
            <select name="orden" class="form-select form-select-sm" style="width:160px;border-radius:20px;">
                <option value="nuevo"       <?= ($filtroOrden === 'nuevo'       || !$filtroOrden) ? 'selected' : '' ?>>🆕 Más recientes</option>
                <option value="precio_asc"  <?= ($filtroOrden === 'precio_asc')  ? 'selected' : '' ?>>💲 Menor precio</option>
                <option value="precio_desc" <?= ($filtroOrden === 'precio_desc') ? 'selected' : '' ?>>💲 Mayor precio</option>
                <option value="nombre_asc"  <?= ($filtroOrden === 'nombre_asc')  ? 'selected' : '' ?>>🔤 A → Z</option>
            </select>
            <button type="submit" class="btn btn-sm btn-dark" style="border-radius:20px;padding:6px 16px;">Filtrar</button>
            <?php if ($filtroCategoria || $filtroBuscar || $filtroPrecioMin || $filtroPrecioMax || $filtroOrden): ?>
                <a href="index.php" class="btn btn-sm btn-outline-secondary" style="border-radius:20px;">✕ Limpiar</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Grid de productos -->
    <?php if (!empty($productos)): ?>
    <div class="products-grid">
        <?php foreach ($productos as $p): ?>
        <?php
        $imagenes   = $product->obtenerImagenes($p['id_producto']);
        $img        = !empty($imagenes) ? $imagenes[0]['imagen'] : null;
        $agotado    = $p['stock'] <= 0;
        $esFavorito = in_array($p['id_producto'], $favoritoIds ?? []);
        ?>
        <div class="pcard <?= $agotado ? 'pcard--agotado' : '' ?>">

            <div class="pcard__badge"><?= htmlspecialchars($p['nombre_categoria'] ?? 'General') ?></div>

            <button class="btn-favorito <?= $esFavorito ? 'activo' : '' ?>"
                    onclick="toggleFavorito(this, <?= $p['id_producto'] ?>)"
                    title="<?= $esFavorito ? 'Quitar de favoritos' : 'Agregar a favoritos' ?>">
                <span class="corazon"><?= $esFavorito ? '♥' : '♡' ?></span>
            </button>

            <a href="index.php?action=detalle_producto&id=<?= $p['id_producto'] ?>" class="pcard__img-wrap">
                <?php if ($img): ?>
                    <img src="/power-net/public/uploads/<?= htmlspecialchars($img) ?>"
                         alt="<?= htmlspecialchars($p['nombre']) ?>" class="pcard__img">
                <?php else: ?>
                    <img src="/power-net/img/logo.jpg" alt="Sin imagen" class="pcard__img">
                <?php endif; ?>
                <?php if ($agotado): ?>
                    <div class="pcard__agotado-overlay">Agotado</div>
                <?php endif; ?>
                <?php if (!empty($p['precio_oferta'])): ?>
                    <?php $desc = $p['descuento'] ?: round((1 - $p['precio_oferta'] / $p['precio']) * 100); ?>
                    <div style="position:absolute;top:10px;right:10px;background:#dc2626;color:#fff;font-size:11px;font-weight:800;padding:3px 8px;border-radius:20px;">
                        -<?= $desc ?>%
                    </div>
                <?php endif; ?>
            </a>

            <div class="pcard__body">
                <a href="index.php?action=detalle_producto&id=<?= $p['id_producto'] ?>" class="pcard__name">
                    <?= htmlspecialchars($p['nombre']) ?>
                </a>
                <p class="pcard__desc"><?= htmlspecialchars($p['descripcion']) ?></p>

                <?php if (!empty($p['precio_oferta'])): ?>
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                        <span style="font-size:20px;font-weight:900;color:#dc2626;">$<?= number_format($p['precio_oferta'], 0, ',', '.') ?></span>
                        <span style="font-size:13px;color:#9ca3af;text-decoration:line-through;">$<?= number_format($p['precio'], 0, ',', '.') ?></span>
                    </div>
                <?php else: ?>
                    <div class="pcard__price">$<?= number_format($p['precio'], 0, ',', '.') ?></div>
                <?php endif; ?>

                <?php if (!$agotado): ?>
                    <div class="pcard__qty" onclick="event.stopPropagation()">
                        <button type="button" class="pcard__qty-btn" onclick="cambiarCantidad(this, -1)">−</button>
                        <input type="number" value="1" min="1" max="<?= $p['stock'] ?>"
                               class="pcard__qty-input cantidad-input" readonly>
                        <button type="button" class="pcard__qty-btn" onclick="cambiarCantidad(this, 1)">+</button>
                    </div>
                    <div class="pcard__actions">
                        <?php if (!isset($_SESSION['usuario'])): ?>
                            <button class="pcard__btn pcard__btn--buy" onclick="abrirLogin()">Comprar</button>
                        <?php else: ?>
                            <form method="POST" action="index.php?action=agregar_carrito" style="flex:1">
                                <input type="hidden" name="id_producto" value="<?= $p['id_producto'] ?>">
                                <input type="hidden" name="cantidad" value="1" class="cantidad-hidden-buy">
                                <input type="hidden" name="ir_a_pago" value="1">
                                <button type="submit" class="pcard__btn pcard__btn--buy w-100">Comprar</button>
                            </form>
                        <?php endif; ?>
                        <form method="POST" action="index.php?action=agregar_carrito">
                            <input type="hidden" name="id_producto" value="<?= $p['id_producto'] ?>">
                            <input type="hidden" name="cantidad" value="1" class="cantidad-hidden">
                            <button type="submit" class="pcard__btn pcard__btn--cart" title="Agregar al carrito">🛒</button>
                        </form>
                    </div>
                    <div class="pcard__stock"><?= $p['stock'] ?> disponibles</div>
                <?php else: ?>
                    <button class="pcard__btn pcard__btn--disabled w-100 mt-2" disabled>Sin stock</button>
                <?php endif; ?>
            </div>

        </div>
        <?php endforeach; ?>
    </div>

    <?php else: ?>
        <div class="text-center py-5">
            <div style="font-size:4rem;">🔍</div>
            <h5 class="mt-3 text-muted">No se encontraron productos</h5>
            <p class="text-muted">Intenta con otros filtros o
                <a href="index.php" class="text-decoration-none" style="color:#7c3aed;">ver todos</a>
            </p>
        </div>
    <?php endif; ?>

</div>
</div>
</div>
