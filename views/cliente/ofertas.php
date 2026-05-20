<?php
// Variables preparadas desde index.php (case 'ofertas'):
// $ofertasVista — array de productos con oferta activa + imagen
?>

<style>
/* ============ PÁGINA OFERTAS ============ */
.ofertas-page { background:#f4f6fb; min-height:100vh; padding:0 0 60px; }

/* HERO BANNER OFERTAS */
.ofertas-hero {
    background: linear-gradient(135deg, #dc2626 0%, #7c3aed 50%, #1a1a2e 100%);
    padding: 60px 40px;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.ofertas-hero::before {
    content: '';
    position: absolute;
    width: 600px; height: 600px;
    background: rgba(255,255,255,0.04);
    border-radius: 50%;
    top: -200px; right: -100px;
}

.ofertas-hero::after {
    content: '';
    position: absolute;
    width: 400px; height: 400px;
    background: rgba(255,255,255,0.03);
    border-radius: 50%;
    bottom: -150px; left: -80px;
}

.ofertas-hero-content { position: relative; z-index: 1; }

.ofertas-hero h1 {
    font-size: clamp(32px, 5vw, 56px);
    font-weight: 900;
    color: #fff;
    margin-bottom: 12px;
    letter-spacing: -1px;
}

.ofertas-hero p {
    font-size: 18px;
    color: rgba(255,255,255,0.8);
    margin-bottom: 24px;
}

.ofertas-hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    color: #fff;
    padding: 8px 20px;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 700;
}

/* CONTADOR REGRESIVO */
.countdown-bar {
    background: rgba(0,0,0,0.3);
    padding: 16px 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 24px;
    flex-wrap: wrap;
}

.countdown-item {
    text-align: center;
    color: #fff;
}

.countdown-num {
    font-size: 28px;
    font-weight: 900;
    line-height: 1;
    display: block;
}

.countdown-label {
    font-size: 11px;
    opacity: .7;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.countdown-sep {
    font-size: 24px;
    color: rgba(255,255,255,0.5);
    font-weight: 900;
    margin-top: -8px;
}

/* GRID DE OFERTAS */
.ofertas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 24px;
    padding: 40px;
}

/* CARD DE OFERTA */
.oferta-card {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    transition: transform .25s ease, box-shadow .25s ease;
    position: relative;
    display: flex;
    flex-direction: column;
}

.oferta-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(220,38,38,0.15);
}

/* Badge descuento */
.oferta-badge-desc {
    position: absolute;
    top: 14px;
    left: 14px;
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: #fff;
    font-size: 13px;
    font-weight: 900;
    padding: 5px 12px;
    border-radius: 20px;
    z-index: 2;
    box-shadow: 0 4px 12px rgba(220,38,38,0.4);
}

/* Badge tiempo */
.oferta-badge-tiempo {
    position: absolute;
    top: 14px;
    right: 14px;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    z-index: 2;
}

/* Imagen */
.oferta-img-wrap {
    width: 100%;
    height: 220px;
    overflow: hidden;
    background: #f8f8fb;
    display: block;
    text-decoration: none;
}

.oferta-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .4s ease;
}

.oferta-card:hover .oferta-img { transform: scale(1.06); }

/* Cuerpo */
.oferta-body {
    padding: 18px 20px;
    display: flex;
    flex-direction: column;
    flex: 1;
    gap: 8px;
}

.oferta-categoria {
    font-size: 11px;
    font-weight: 700;
    color: #7c3aed;
    text-transform: uppercase;
    letter-spacing: .5px;
}

.oferta-nombre {
    font-size: 15px;
    font-weight: 700;
    color: #1a1a2e;
    text-decoration: none;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.oferta-nombre:hover { color: #dc2626; }

/* Precios */
.oferta-precios {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.oferta-precio-nuevo {
    font-size: 24px;
    font-weight: 900;
    color: #dc2626;
}

.oferta-precio-viejo {
    font-size: 15px;
    color: #9ca3af;
    text-decoration: line-through;
}

.oferta-ahorro {
    font-size: 12px;
    font-weight: 700;
    color: #10b981;
    background: #d1fae5;
    padding: 2px 8px;
    border-radius: 20px;
}

/* Barra de progreso de stock */
.oferta-stock-bar {
    margin-top: 4px;
}

.oferta-stock-label {
    display: flex;
    justify-content: space-between;
    font-size: 11px;
    color: #6b7280;
    margin-bottom: 4px;
}

.oferta-stock-track {
    height: 6px;
    background: #f3f4f6;
    border-radius: 10px;
    overflow: hidden;
}

.oferta-stock-fill {
    height: 100%;
    border-radius: 10px;
    background: linear-gradient(90deg, #10b981, #34d399);
    transition: width .3s;
}

.oferta-stock-fill.critico { background: linear-gradient(90deg, #dc2626, #f87171); }

/* Botones */
.oferta-actions {
    display: flex;
    gap: 8px;
    margin-top: 8px;
}

.oferta-btn-comprar {
    flex: 1;
    padding: 11px;
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: #fff;
    border: none;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    transition: all .2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.oferta-btn-comprar:hover {
    background: linear-gradient(135deg, #b91c1c, #991b1b);
    color: #fff;
    transform: translateY(-1px);
}

.oferta-btn-carrito {
    width: 44px;
    height: 44px;
    background: #f3f4f6;
    border: none;
    border-radius: 12px;
    font-size: 18px;
    cursor: pointer;
    transition: all .2s;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.oferta-btn-carrito:hover { background: #1a1a2e; color: #fff; }

/* Sin ofertas */
.sin-ofertas {
    text-align: center;
    padding: 80px 40px;
    color: #9ca3af;
}

.sin-ofertas .icon { font-size: 5rem; margin-bottom: 16px; }
.sin-ofertas h4 { font-weight: 700; color: #374151; margin-bottom: 8px; }

@media (max-width: 576px) {
    .ofertas-hero { padding: 40px 20px; }
    .ofertas-grid { padding: 20px; gap: 16px; }
    .oferta-img-wrap { height: 180px; }
}
</style>

<div class="ofertas-page">

    <!-- HERO -->
    <div class="ofertas-hero">
        <div class="ofertas-hero-content">
            <div class="ofertas-hero-badge mb-3">
                🔥 Ofertas por tiempo limitado
            </div>
            <h1>¡Aprovecha los<br>mejores descuentos!</h1>
            <p>Productos seleccionados con precios increíbles. Solo por tiempo limitado.</p>
            <?php if (!empty($ofertasVista)): ?>
                <div class="ofertas-hero-badge">
                    <?= count($ofertasVista) ?> <?= count($ofertasVista) === 1 ? 'producto' : 'productos' ?> en oferta ahora
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- PRODUCTOS EN OFERTA -->
    <?php if (!empty($ofertasVista)): ?>

        <div class="ofertas-grid">
            <?php foreach ($ofertasVista as $o): ?>
            <?php
            $ahorro    = $o['precio'] - $o['precio_oferta'];
            $diasRest  = max(0, (int)((strtotime($o['fecha_fin']) - time()) / 86400));
            $stockPct  = $o['stock'] > 0 ? min(100, ($o['stock'] / 50) * 100) : 0;
            $critico   = $o['stock'] <= 5;
            ?>
            <div class="oferta-card">

                <!-- Badge descuento -->
                <div class="oferta-badge-desc">-<?= $o['descuento'] ?>%</div>

                <!-- Badge tiempo restante -->
                <div class="oferta-badge-tiempo">
                    ⏱ <?= $diasRest === 0 ? 'Último día' : $diasRest . ' días' ?>
                </div>

                <!-- Imagen -->
                <a href="index.php?action=detalle_producto&id=<?= $o['id_producto'] ?>"
                   class="oferta-img-wrap">
                    <?php if ($o['imagen']): ?>
                        <img src="<?= UPLOADS_URL ?>/<?= htmlspecialchars($o['imagen']) ?>"
                             alt="<?= htmlspecialchars($o['nombre']) ?>"
                             class="oferta-img">
                    <?php else: ?>
                        <img src="<?= IMG_URL ?>/logo.jpg" alt="Sin imagen" class="oferta-img">
                    <?php endif; ?>
                </a>

                <!-- Cuerpo -->
                <div class="oferta-body">

                    <div class="oferta-categoria">
                        <?= htmlspecialchars($o['nombre_categoria'] ?? 'General') ?>
                    </div>

                    <a href="index.php?action=detalle_producto&id=<?= $o['id_producto'] ?>"
                       class="oferta-nombre">
                        <?= htmlspecialchars($o['nombre']) ?>
                    </a>

                    <!-- Precios -->
                    <div class="oferta-precios">
                        <span class="oferta-precio-nuevo">
                            $<?= number_format($o['precio_oferta'], 0, ',', '.') ?>
                        </span>
                        <span class="oferta-precio-viejo">
                            $<?= number_format($o['precio'], 0, ',', '.') ?>
                        </span>
                        <span class="oferta-ahorro">
                            Ahorras $<?= number_format($ahorro, 0, ',', '.') ?>
                        </span>
                    </div>

                    <!-- Barra de stock -->
                    <div class="oferta-stock-bar">
                        <div class="oferta-stock-label">
                            <span><?= $critico ? '🔥 ¡Quedan pocos!' : 'Disponibles' ?></span>
                            <span><?= $o['stock'] ?> unidades</span>
                        </div>
                        <div class="oferta-stock-track">
                            <div class="oferta-stock-fill <?= $critico ? 'critico' : '' ?>"
                                 style="width:<?= $stockPct ?>%"></div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="oferta-actions">
                        <?php if (isset($_SESSION['usuario'])): ?>
                            <form method="POST" action="index.php?action=agregar_carrito" style="flex:1">
                                <input type="hidden" name="id_producto" value="<?= $o['id_producto'] ?>">
                                <input type="hidden" name="cantidad" value="1">
                                <input type="hidden" name="ir_a_pago" value="1">
                                <button type="submit" class="oferta-btn-comprar w-100">
                                    ⚡ Comprar ahora
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="oferta-btn-comprar" onclick="abrirLogin()">
                                ⚡ Comprar ahora
                            </button>
                        <?php endif; ?>

                        <form method="POST" action="index.php?action=agregar_carrito">
                            <input type="hidden" name="id_producto" value="<?= $o['id_producto'] ?>">
                            <input type="hidden" name="cantidad" value="1">
                            <button type="submit" class="oferta-btn-carrito" title="Agregar al carrito">
                                🛒
                            </button>
                        </form>
                    </div>

                </div>
            </div>
            <?php endforeach; ?>
        </div>

    <?php else: ?>

        <div class="sin-ofertas">
            <div class="icon">🏷️</div>
            <h4>No hay ofertas activas en este momento</h4>
            <p>Vuelve pronto, ¡tendremos descuentos increíbles para ti!</p>
            <a href="index.php" class="btn btn-dark mt-3 rounded-pill px-4">
                Ver todos los productos
            </a>
        </div>

    <?php endif; ?>

</div>
