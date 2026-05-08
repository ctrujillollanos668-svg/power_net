<?php
// Variables preparadas desde index.php (case 'mis_favoritos')
$favoritos    = $favoritosMis   ?? [];
$favoritoIds  = $favoritoIdsMis ?? [];
$productModel = $prodFavModel   ?? new Product();
?>


<div class="container mt-5 mb-5">

    <div class="d-flex align-items-center gap-3 mb-4">
        <h4 class="fw-bold mb-0">❤️ Mis Favoritos</h4>
        <span class="badge bg-danger"><?= count($favoritos) ?></span>
    </div>

    <?php if (empty($favoritos)): ?>
        <div class="text-center py-5">
            <div style="font-size:4rem;">🤍</div>
            <h5 class="mt-3 text-muted">Aún no tienes favoritos</h5>
            <p class="text-muted">Haz clic en el corazón de cualquier producto para guardarlo aquí.</p>
            <a href="index.php" class="btn btn-dark mt-2 rounded-pill px-4">Ver productos</a>
        </div>
    <?php else: ?>

        <div class="products-grid">
            <?php foreach ($favoritos as $p): ?>
                <?php
                $imagenes = $productModel->obtenerImagenes($p['id_producto']);
                $img      = !empty($imagenes) ? $imagenes[0]['imagen'] : null;
                $agotado  = $p['stock'] <= 0;
                ?>

                <div class="pcard <?= $agotado ? 'pcard--agotado' : '' ?>">

                    <div class="pcard__badge">
                        <?= htmlspecialchars($p['nombre_categoria'] ?? 'General') ?>
                    </div>

                    <!-- Botón quitar favorito -->
                    <button class="btn-favorito activo"
                            onclick="toggleFavorito(this, <?= $p['id_producto'] ?>, true)"
                            title="Quitar de favoritos">
                        <span class="corazon">♥</span>
                    </button>

                    <a href="index.php?action=detalle_producto&id=<?= $p['id_producto'] ?>"
                       class="pcard__img-wrap">
                        <?php if ($img): ?>
                            <img src="/power-net/public/uploads/<?= htmlspecialchars($img) ?>"
                                 alt="<?= htmlspecialchars($p['nombre']) ?>"
                                 class="pcard__img">
                        <?php else: ?>
                            <img src="/power-net/img/logo.jpg" alt="Sin imagen" class="pcard__img">
                        <?php endif; ?>
                        <?php if ($agotado): ?>
                            <div class="pcard__agotado-overlay">Agotado</div>
                        <?php endif; ?>
                    </a>

                    <div class="pcard__body">
                        <a href="index.php?action=detalle_producto&id=<?= $p['id_producto'] ?>"
                           class="pcard__name">
                            <?= htmlspecialchars($p['nombre']) ?>
                        </a>
                        <p class="pcard__desc"><?= htmlspecialchars($p['descripcion']) ?></p>
                        <div class="pcard__price">$<?= number_format($p['precio'], 0, ',', '.') ?></div>

                        <?php if (!$agotado): ?>
                            <div class="pcard__actions mt-2">
                                <?php if (isset($_SESSION['usuario'])): ?>
                                    <form method="POST" action="index.php?action=agregar_carrito" style="flex:1">
                                        <input type="hidden" name="id_producto" value="<?= $p['id_producto'] ?>">
                                        <input type="hidden" name="cantidad" value="1">
                                        <input type="hidden" name="ir_a_pago" value="1">
                                        <button type="submit" class="pcard__btn pcard__btn--buy w-100">
                                            Comprar
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST" action="index.php?action=agregar_carrito">
                                    <input type="hidden" name="id_producto" value="<?= $p['id_producto'] ?>">
                                    <input type="hidden" name="cantidad" value="1">
                                    <button type="submit" class="pcard__btn pcard__btn--cart" title="Agregar al carrito">
                                        🛒
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <button class="pcard__btn pcard__btn--disabled w-100 mt-2" disabled>Sin stock</button>
                        <?php endif; ?>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>

<script>
// En mis_favoritos, al quitar el corazón se oculta la card
function toggleFavorito(btn, idProducto, ocultarCard = false) {
    const formData = new FormData();
    formData.append('id_producto', idProducto);

    fetch('index.php?action=toggle_favorito', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (ocultarCard && !data.favorito) {
            const card = btn.closest('.pcard');
            card.style.transition = 'opacity .3s, transform .3s';
            card.style.opacity    = '0';
            card.style.transform  = 'scale(0.9)';
            setTimeout(() => card.remove(), 300);
        } else {
            const corazon = btn.querySelector('.corazon');
            if (corazon) {
                corazon.textContent = data.favorito ? '♥' : '♡';
            }
            btn.classList.toggle('activo', data.favorito);
        }
    });
}
</script>

<style>
.btn-favorito {
    position: absolute;
    top: 10px;
    left: 10px;
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 50%;
    background: rgba(255,255,255,0.92);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    transition: all .2s;
    z-index: 3;
    padding: 0;
}
.btn-favorito .corazon {
    font-size: 20px;
    line-height: 1;
    color: #d1d5db;
    transition: color .2s;
}
.btn-favorito.activo .corazon { color: #ef4444; }
.btn-favorito:hover { transform: scale(1.15); }
.btn-favorito:hover .corazon { color: #ef4444; }
.btn-favorito.activo { background: #fff0f0; }
</style>
