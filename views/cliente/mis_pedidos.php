<?php
// Variables preparadas desde index.php (case 'mis_pedidos'):
// $pedidosMis, $pedidoMisModel

// Usar las variables del router
$pedidos     = $pedidosMis    ?? [];
$pedidoModel = $pedidoMisModel ?? new Pedido();
?>

<div class="container mt-5 mb-5">
    <h4 class="fw-bold mb-4">📦 Mis Pedidos</h4>

    <?php if (empty($pedidos)): ?>
        <!-- Mensaje cuando el cliente no tiene pedidos aún -->
        <div class="alert alert-info">
            Aún no tienes pedidos registrados.
            <a href="index.php" class="alert-link">Ver productos</a>
        </div>

    <?php else: ?>

        <div class="row g-3">
            <?php foreach ($pedidos as $p): ?>
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row align-items-center">

                                <!-- NÚMERO DE PEDIDO -->
                                <div class="col-md-2">
                                    <div class="text-muted small">Pedido</div>
                                    <div class="fw-bold fs-5">#<?= $p['id_pedido'] ?></div>
                                </div>

                                <!-- FECHA
                                     CAMBIO: antes era solo date(...strtotime($p['fecha_pedido']))
                                     pero si fecha_pedido es NULL lanzaba un Deprecated warning.
                                     Ahora verificamos: si fecha_pedido existe la usamos,
                                     si no, intentamos con fecha_pago, y si tampoco, mostramos 'now'.
                                -->
                                <div class="col-md-3">
                                    <div class="text-muted small">Fecha</div>
                                    <div>
                                        <?php
                                        // Prioridad: fecha_pedido > fecha_pago > fecha actual
                                        $fecha = $p['fecha_pedido'] ?? $p['fecha_pago'] ?? null;
                                        echo $fecha
                                            ? date('d/m/Y H:i', strtotime($fecha))
                                            : 'Sin fecha';
                                        ?>
                                    </div>
                                </div>

                                <!-- TOTAL DEL PEDIDO -->
                                <div class="col-md-2">
                                    <div class="text-muted small">Total</div>
                                    <div class="fw-bold text-success">
                                        $<?= number_format($p['total_pedido'], 0, ',', '.') ?>
                                    </div>
                                </div>

                                <!-- ESTADO DEL PEDIDO -->
                                <div class="col-md-2">
                                    <div class="text-muted small">Estado pedido</div>
                                    <?php
                                    $estado = strtolower($p['estado_pedido']);
                                    $badgeClass = match($estado) {
                                        'entregado' => 'bg-success',
                                        'enviado'   => 'bg-primary',
                                        'pendiente' => 'bg-warning text-dark',
                                        'cancelado' => 'bg-danger',
                                        default     => 'bg-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>">
                                        <?= ucfirst($p['estado_pedido']) ?>
                                    </span>
                                </div>

                                <!-- ESTADO DEL PAGO -->
                                <div class="col-md-2">
                                    <div class="text-muted small">Pago</div>
                                    <?php if ($p['estado_pago']): ?>
                                        <span class="badge bg-success"><?= ucfirst($p['estado_pago']) ?></span>
                                        <div class="small text-muted">
                                            <?= htmlspecialchars($p['metodo_pago'] ?? '') ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Sin pago</span>
                                    <?php endif; ?>
                                </div>

                                <!-- BOTONES: VER + FACTURA + DEVOLUCIÓN -->
                                <div class="col-md-1 text-end d-flex flex-column gap-1 align-items-end">
                                    <button class="btn btn-sm btn-outline-dark"
                                            type="button"
                                            onclick="toggleDetalle(<?= $p['id_pedido'] ?>, this)">
                                        Ver
                                    </button>

                                    <?php if (in_array(strtolower($p['estado_pedido']), ['enviado', 'entregado'])): ?>
                                        <a href="index.php?action=factura&id=<?= $p['id_pedido'] ?>"
                                           target="_blank"
                                           class="btn btn-sm btn-success"
                                           title="Descargar factura">
                                            🧾
                                        </a>
                                    <?php endif; ?>

                                    <?php if (strtolower($p['estado_pedido']) === 'entregado'): ?>
                                        <a href="index.php?action=solicitar_devolucion&id=<?= $p['id_pedido'] ?>"
                                           class="btn btn-sm btn-warning"
                                           title="Solicitar devolución">
                                            ↩️
                                        </a>
                                    <?php endif; ?>
                                </div>

                            </div>

                            <!-- DETALLE COLAPSABLE -->
                            <div id="detalle_<?= $p['id_pedido'] ?>"
                                 style="display:none; margin-top:16px;">
                                <?php
                                $detalle = $pedidoModel->obtenerDetalle($p['id_pedido']);
                                ?>

                                <?php if (!empty($detalle)): ?>
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Producto</th>
                                                <th class="text-center">Cantidad</th>
                                                <th class="text-end">Precio unit.</th>
                                                <th class="text-end">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($detalle as $d): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($d['nombre'] ?? 'Producto eliminado') ?></td>
                                                    <td class="text-center"><?= $d['cantidad'] ?></td>
                                                    <td class="text-end">$<?= number_format($d['precio_unitario'], 0, ',', '.') ?></td>
                                                    <td class="text-end">$<?= number_format($d['subtotal'], 0, ',', '.') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <td colspan="3" class="text-end fw-bold">Total:</td>
                                                <td class="text-end fw-bold text-success">
                                                    $<?= number_format($p['total_pedido'], 0, ',', '.') ?>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    <?php if ($p['factura']): ?>
                                        <div class="mt-2 text-muted small">
                                            🧾 Factura: <strong><?= htmlspecialchars($p['factura']) ?></strong>
                                        </div>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <p class="text-muted small mb-0">Sin detalle disponible.</p>
                                <?php endif; ?>

                            </div><!-- fin detalle -->

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>

<script>
// Muestra/oculta el detalle del pedido y cambia el texto del botón
function toggleDetalle(id, btn) {
    const detalle = document.getElementById('detalle_' + id);
    if (!detalle) return;

    const visible = detalle.style.display !== 'none';
    detalle.style.display = visible ? 'none' : 'block';
    btn.textContent       = visible ? 'Ver' : 'Ocultar';
    btn.classList.toggle('btn-outline-dark',  visible);
    btn.classList.toggle('btn-dark',         !visible);
}
</script>
