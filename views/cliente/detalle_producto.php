<?php
// Variables preparadas desde index.php (case 'detalle_producto'):
// $productoDetalle, $imagenesDetalle, $relacionadosDetalle, $productDetalle

$producto  = $productoDetalle  ?? null;
$imagenes  = $imagenesDetalle  ?? [];
$relacionados = $relacionadosDetalle ?? [];
$productModel = $productDetalle ?? new Product();
?>

<div class="container mt-4">

    <!-- BOTÓN VOLVER -->
    <a href="index.php" class="btn btn-outline-secondary mb-4">
        ← Volver
    </a>

    <?php if (!$producto): ?>

        <!-- SI NO EXISTE EL PRODUCTO -->
        <div class="alert alert-danger">
            Producto no encontrado.
        </div>

    <?php else: ?>

        <?php
        // Imágenes ya vienen preparadas desde el router
        ?>

        <!-- CARD PRINCIPAL DEL PRODUCTO -->
        <div class="card shadow-sm p-4">

            <div class="row g-4 align-items-start">

                <!-- ================= IMÁGENES ================= -->
                <div class="col-md-5">

                    <div class="d-flex gap-3">

                        <!-- MINIATURAS -->
                        <div class="d-flex flex-column gap-2">

                            <?php if (!empty($imagenes)): ?>

                                <?php foreach ($imagenes as $img): ?>
                                    <img src="/power-net/public/uploads/<?= $img['imagen'] ?>"
                                         onclick="cambiarImagen(this)"
                                         class="border rounded"
                                         style="width:75px; height:85px; object-fit:cover; cursor:pointer;">
                                <?php endforeach; ?>

                            <?php else: ?>

                                <img src="/power-net/img/logo.jpg"
                                     class="border rounded"
                                     style="width:75px; height:85px; object-fit:cover;">

                            <?php endif; ?>

                        </div>

                        <!-- IMAGEN GRANDE -->
                        <div class="border text-center bg-white rounded"
                             style="width:420px; height:330px; display:flex; align-items:center; justify-content:center;">

                            <?php if (!empty($imagenes)): ?>
                                <img id="imagenPrincipal"
                                     src="/power-net/public/uploads/<?= $imagenes[0]['imagen'] ?>"
                                     style="max-width:100%; max-height:100%; object-fit:contain;">
                            <?php else: ?>
                                <img id="imagenPrincipal"
                                     src="/power-net/img/logo.jpg"
                                     style="max-width:100%; max-height:100%; object-fit:contain;">
                            <?php endif; ?>

                        </div>

                    </div>

                </div>

                <!-- ================= INFORMACIÓN ================= -->
                <div class="col-md-7">

                    <!-- NOMBRE -->
                    <h3 class="fw-bold mb-2">
                        <?= htmlspecialchars($producto['nombre']) ?>
                    </h3>

                    <!-- PRECIO -->
                    <h1 class="text-dark mb-3">
                        $<?= number_format($producto['precio'], 0, ',', '.') ?>
                    </h1>

                    <!-- CANTIDAD -->
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <button type="button"
                                id="btn-menos"
                                onclick="cambiarCantidadDetalle(-1)"
                                style="width:36px;height:36px;border:2px solid #d1d5db;border-radius:8px;background:#fff;font-size:20px;font-weight:700;cursor:pointer;line-height:1;transition:all .2s;"
                                onmouseover="this.style.background='#1a1a2e';this.style.color='#fff'"
                                onmouseout="this.style.background='#fff';this.style.color='#000'">
                            −
                        </button>

                        <input type="number"
                               id="cantidad-detalle"
                               value="1"
                               min="1"
                               max="<?= $producto['stock'] ?>"
                               readonly
                               style="width:56px;height:36px;text-align:center;border:2px solid #d1d5db;border-radius:8px;font-size:16px;font-weight:700;background:#f9fafb;">

                        <button type="button"
                                id="btn-mas"
                                onclick="cambiarCantidadDetalle(1)"
                                style="width:36px;height:36px;border:2px solid #d1d5db;border-radius:8px;background:#fff;font-size:20px;font-weight:700;cursor:pointer;line-height:1;transition:all .2s;"
                                onmouseover="this.style.background='#1a1a2e';this.style.color='#fff'"
                                onmouseout="this.style.background='#fff';this.style.color='#000'">
                            +
                        </button>

                        <span style="font-size:13px;color:#9ca3af;margin-left:4px;">
                            (<?= $producto['stock'] ?> disponibles)
                        </span>
                    </div>

                    <!-- DESCRIPCIÓN -->
                    <h5 class="fw-bold">
                        Lo que tienes que saber de este producto
                    </h5>

                    <p class="text-muted fs-5">
                        <?= nl2br(htmlspecialchars($producto['descripcion'])) ?>
                    </p>

                    <!-- STOCK -->
                    <p>
                        Stock disponible: <?= $producto['stock'] ?>
                    </p>

                    <!-- BOTONES -->
                    <?php if ($producto['stock'] > 0): ?>

                        <div class="d-flex gap-3 mt-4 flex-wrap">

                            <!-- COMPRAR AHORA -->
                            <?php if (isset($_SESSION['usuario'])): ?>
                                <form method="POST" action="index.php?action=agregar_carrito" id="form-comprar">
                                    <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
                                    <input type="hidden" name="cantidad" value="1" id="cantidad-comprar">
                                    <input type="hidden" name="ir_a_pago" value="1">
                                    <button type="submit" class="btn btn-warning btn-lg rounded-pill px-4">
                                        ⚡ Comprar ahora
                                    </button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-warning btn-lg rounded-pill px-4"
                                        onclick="abrirLogin()">
                                    ⚡ Comprar ahora
                                </button>
                            <?php endif; ?>

                            <!-- AGREGAR AL CARRITO -->
                            <form method="POST" action="index.php?action=agregar_carrito" id="form-carrito">
                                <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
                                <input type="hidden" name="cantidad" value="1" id="cantidad-carrito">
                                <button type="submit" class="btn btn-outline-dark btn-lg rounded-pill px-4">
                                    🛒 Agregar al carrito
                                </button>
                            </form>

                        </div>

                    <?php else: ?>

                        <button class="btn btn-secondary btn-lg mt-3" disabled>
                            ❌ Agotado
                        </button>

                    <?php endif; ?>

                </div>

            </div>

        </div>

        <!-- ================= PRODUCTOS RELACIONADOS ================= -->
        <?php
        // Relacionados ya vienen preparados desde el router
        ?>

        <?php if (!empty($relacionados)): ?>

            <hr class="my-5">

            <h3 class="mb-4">Productos relacionados</h3>

            <div class="row g-4">

                <?php foreach ($relacionados as $rel): ?>

                    <?php
                    // 🖼 IMAGEN PRINCIPAL DEL PRODUCTO RELACIONADO
                    $imgsRel = $productModel->obtenerImagenes($rel['id_producto']);
                    $imgRel = !empty($imgsRel) ? $imgsRel[0]['imagen'] : null;
                    ?>

                    <div class="col-md-3">

                        <a href="index.php?action=detalle_producto&id=<?= $rel['id_producto'] ?>"
                           class="text-decoration-none text-dark">

                            <div class="card h-100 shadow-sm border-0 product-card">

                                <?php if ($imgRel): ?>
                                    <img src="/power-net/public/uploads/<?= $imgRel ?>"
                                         class="card-img-top"
                                         style="height:180px; object-fit:cover;">
                                <?php else: ?>
                                    <img src="/power-net/img/logo.jpg"
                                         class="card-img-top"
                                         style="height:180px; object-fit:cover;">
                                <?php endif; ?>

                                <div class="card-body">
                                    <h6 class="fw-bold">
                                        <?= htmlspecialchars($rel['nombre']) ?>
                                    </h6>

                                    <p class="text-success fw-semibold">
                                        $<?= number_format($rel['precio'], 0, ',', '.') ?>
                                    </p>
                                </div>

                            </div>

                        </a>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    <?php endif; ?>

</div>

<!-- JS PARA CAMBIAR IMAGEN GRANDE -->
<script>
function cambiarImagen(img) {
    document.getElementById('imagenPrincipal').src = img.src;
}

// +/- de cantidad en detalle del producto
function cambiarCantidadDetalle(cambio) {
    const input  = document.getElementById('cantidad-detalle');
    const max    = parseInt(input.max);
    const min    = parseInt(input.min);
    let   val    = parseInt(input.value) + cambio;

    // No pasar del stock ni bajar de 1
    if (val < min) val = min;
    if (val > max) val = max;

    input.value = val;

    // Sincronizar con los inputs hidden de los formularios
    const hiddenComprar  = document.getElementById('cantidad-comprar');
    const hiddenCarrito  = document.getElementById('cantidad-carrito');
    if (hiddenComprar)  hiddenComprar.value  = val;
    if (hiddenCarrito)  hiddenCarrito.value  = val;

    // Deshabilitar botón − cuando llega a 1
    document.getElementById('btn-menos').disabled = (val <= min);
    // Deshabilitar botón + cuando llega al stock máximo
    document.getElementById('btn-mas').disabled   = (val >= max);
}
</script>