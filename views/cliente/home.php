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

<!-- HERO -->
<section class="mb-8 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl shadow-slate-900/5">
    <div class="grid items-center gap-6 p-5 md:grid-cols-2 md:p-10">
        <div>
            <span class="mb-3 inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-bold text-amber-800">Productos profesionales</span>
            <h1 class="mb-4 text-4xl font-black leading-tight text-slate-900 md:text-6xl">Todo para tu hogar.</h1>
            <p class="mb-6 max-w-xl text-base text-slate-600 md:text-lg">Bombillos, cables,materiales eléctricos con el respaldo de PowerNet.</p>
            <div class="flex flex-wrap gap-3">
                <a href="#productos" class="rounded-xl bg-amber-400 px-6 py-3 text-sm font-black text-slate-900 text-decoration-none transition hover:bg-amber-300">Ver catálogo</a>
                <a href="index.php?action=ofertas" class="rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-bold text-slate-700 text-decoration-none transition hover:border-brand-300 hover:text-brand-700">Ofertas</a>
            </div>
        </div>
        <div class="relative">
            <img
                src="../../img/ia.png"
                alt="Trabajadores en construcción"
                class="h-[260px] w-full rounded-3xl object-cover shadow-lg md:h-[340px]"
            >
            <div class="absolute -bottom-3 -left-3 rounded-2xl bg-white/95 px-4 py-2 text-xs font-semibold text-slate-600 shadow-md ring-1 ring-slate-200">
                <?= count($productos) ?> productos en catálogo
            </div>
        </div>
    </div>
</section>

<!-- PRODUCTOS -->
<div class="container-xl py-2 md:py-4" id="productos">
<div class="row g-4">
<div class="col-12">

    <!-- Barra de filtros -->
    <div class="mb-5 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm md:flex-row md:items-center md:justify-between">
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
            <span class="ms-2 whitespace-nowrap text-xs text-slate-500">
                <?= count($productos) ?> <?= count($productos) === 1 ? 'resultado' : 'resultados' ?>
            </span>
        </div>

        <form method="GET" action="index.php" class="flex flex-wrap items-center gap-2">
            <?php if (!empty($filtroCategoria)): ?>
                <input type="hidden" name="categoria" value="<?= htmlspecialchars($filtroCategoria) ?>">
            <?php endif; ?>
            <input type="text" name="buscar" class="h-9 rounded-lg border border-slate-300 bg-white px-3 text-xs outline-none transition focus:border-brand-400 focus:ring-4 focus:ring-brand-100"
                   style="width:180px;" placeholder="Buscar..."
                   value="<?= htmlspecialchars($filtroBuscar ?? '') ?>">
            <input type="number" name="precio_min" class="h-9 rounded-lg border border-slate-300 bg-white px-3 text-xs outline-none transition focus:border-brand-400 focus:ring-4 focus:ring-brand-100"
                   style="width:100px;" placeholder="$ Mín"
                   value="<?= htmlspecialchars($filtroPrecioMin ?? '') ?>">
            <input type="number" name="precio_max" class="h-9 rounded-lg border border-slate-300 bg-white px-3 text-xs outline-none transition focus:border-brand-400 focus:ring-4 focus:ring-brand-100"
                   style="width:100px;" placeholder="$ Máx"
                   value="<?= htmlspecialchars($filtroPrecioMax ?? '') ?>">
            <select name="orden" class="h-9 rounded-lg border border-slate-300 bg-white px-3 text-xs outline-none transition focus:border-brand-400 focus:ring-4 focus:ring-brand-100" style="width:160px;">
                <option value="nuevo"       <?= ($filtroOrden === 'nuevo'       || !$filtroOrden) ? 'selected' : '' ?>>🆕 Más recientes</option>
                <option value="precio_asc"  <?= ($filtroOrden === 'precio_asc')  ? 'selected' : '' ?>>💲 Menor precio</option>
                <option value="precio_desc" <?= ($filtroOrden === 'precio_desc') ? 'selected' : '' ?>>💲 Mayor precio</option>
                <option value="nombre_asc"  <?= ($filtroOrden === 'nombre_asc')  ? 'selected' : '' ?>>🔤 A → Z</option>
            </select>
            <button type="submit" class="h-9 rounded-lg bg-slate-900 px-4 text-xs font-bold text-white transition hover:bg-brand-700">Filtrar</button>
            <?php if ($filtroCategoria || $filtroBuscar || $filtroPrecioMin || $filtroPrecioMax || $filtroOrden): ?>
                <a href="index.php" class="inline-flex h-9 items-center rounded-lg border border-slate-300 px-3 text-xs font-semibold text-slate-600 text-decoration-none transition hover:border-slate-400 hover:text-slate-800">✕ Limpiar</a>
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
