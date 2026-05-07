<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 1) {
    header("Location: /power-net/public/index.php"); exit;
}
require_once __DIR__ . '/../../../models/Order.php';
$order   = new Order();
$pedidos = $order->obtenerPedidos();

// Cambiar estado si viene por GET
if (isset($_GET['cambiar_estado'], $_GET['id'], $_GET['estado'])) {
    require_once __DIR__ . '/../../../config/Database.php';
    $pdo  = (new Database())->getConnection();
    $stmt = $pdo->prepare("UPDATE pedido SET estado_pedido = ? WHERE id_pedido = ?");
    $stmt->execute([$_GET['estado'], (int)$_GET['id']]);
    header("Location: /power-net/views/admin/pedidos/pedidos.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Pedidos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="d-flex">
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
<div class="flex-grow-1 p-4" style="margin-left:250px;margin-top:70px;">

    <h2 class="fw-bold mb-4">📦 Pedidos</h2>

    <!-- MÉTRICAS -->
    <?php
    $total     = count($pedidos);
    $pendientes = count(array_filter($pedidos, fn($p) => strtolower($p['estado_pedido']) === 'pendiente'));
    $enviados   = count(array_filter($pedidos, fn($p) => strtolower($p['estado_pedido']) === 'enviado'));
    $entregados = count(array_filter($pedidos, fn($p) => strtolower($p['estado_pedido']) === 'entregado'));
    $ingresos   = array_sum(array_column($pedidos, 'total_pedido'));
    ?>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Total pedidos</div>
                    <div class="fw-bold fs-3"><?= $total ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Pendientes</div>
                    <div class="fw-bold fs-3 text-warning"><?= $pendientes ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Enviados</div>
                    <div class="fw-bold fs-3 text-primary"><?= $enviados ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Ingresos totales</div>
                    <div class="fw-bold fs-3 text-success">$<?= number_format($ingresos, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTRO -->
    <div class="d-flex gap-2 mb-3">
        <input type="text" id="buscar" class="form-control w-25" placeholder="Buscar pedido...">
        <select id="filtroEstado" class="form-select w-auto">
            <option value="">Todos los estados</option>
            <option value="pendiente">Pendiente</option>
            <option value="enviado">Enviado</option>
            <option value="entregado">Entregado</option>
            <option value="cancelado">Cancelado</option>
        </select>
    </div>

    <!-- TABLA -->
    <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
    <table class="table table-hover align-middle mb-0" id="tablaPedidos">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Pago</th>
                <th>Estado</th>
                <th class="text-center">Cambiar estado</th>
                <th class="text-center">Detalle</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($pedidos as $p): ?>
        <?php
        $estado = strtolower($p['estado_pedido']);
        $badge  = match($estado) {
            'entregado' => 'bg-success',
            'enviado'   => 'bg-primary',
            'pendiente' => 'bg-warning text-dark',
            'cancelado' => 'bg-danger',
            default     => 'bg-secondary'
        };
        $fecha = $p['fecha_pedido'] ?? null;
        ?>
        <tr>
            <td class="fw-bold">#<?= $p['id_pedido'] ?></td>
            <td><?= htmlspecialchars($p['nombre_cliente'] ?? '—') ?></td>
            <td><?= $fecha ? date('d/m/Y', strtotime($fecha)) : '—' ?></td>
            <td class="fw-semibold text-success">$<?= number_format($p['total_pedido'], 0, ',', '.') ?></td>
            <td>
                <?php if ($p['estado_pago']): ?>
                    <span class="badge bg-success"><?= ucfirst($p['estado_pago']) ?></span>
                    <div class="small text-muted"><?= htmlspecialchars($p['metodo_pago'] ?? '') ?></div>
                <?php else: ?>
                    <span class="badge bg-secondary">Sin pago</span>
                <?php endif; ?>
            </td>
            <td><span class="badge <?= $badge ?>"><?= ucfirst($p['estado_pedido']) ?></span></td>
            <td class="text-center">
                <div class="d-flex gap-1 justify-content-center flex-wrap">
                    <?php
                    $estados = ['pendiente','enviado','entregado','cancelado'];
                    foreach ($estados as $e):
                        if ($e === $estado) continue;
                    ?>
                    <a href="?cambiar_estado=1&id=<?= $p['id_pedido'] ?>&estado=<?= $e ?>"
                       class="btn btn-xs btn-outline-secondary"
                       style="font-size:11px;padding:2px 8px;"
                       onclick="return confirm('¿Cambiar estado a <?= ucfirst($e) ?>?')">
                        <?= ucfirst($e) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </td>
            <td class="text-center">
                <button class="btn btn-sm btn-outline-dark"
                        onclick="verDetalle(<?= $p['id_pedido'] ?>)">
                    <i class="bi bi-eye"></i>
                </button>
            </td>
        </tr>
        <!-- Fila detalle oculta -->
        <tr id="det_<?= $p['id_pedido'] ?>" style="display:none;">
            <td colspan="8" class="bg-light p-3">
                <?php
                $det = $order->obtenerDetalle($p['id_pedido']);
                if (!empty($det)):
                ?>
                <table class="table table-sm mb-0">
                    <thead><tr><th>Producto</th><th>Cant.</th><th>Precio unit.</th><th>Subtotal</th></tr></thead>
                    <tbody>
                    <?php foreach ($det as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['nombre'] ?? '—') ?></td>
                        <td><?= $d['cantidad'] ?></td>
                        <td>$<?= number_format($d['precio_unitario'], 0, ',', '.') ?></td>
                        <td>$<?= number_format($d['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <p class="text-muted mb-0">Sin detalle.</p>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    </div>

</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
function verDetalle(id) {
    const row = document.getElementById('det_' + id);
    row.style.display = row.style.display === 'none' ? '' : 'none';
}
document.getElementById('buscar').addEventListener('keyup', function() {
    filtrar();
});
document.getElementById('filtroEstado').addEventListener('change', function() {
    filtrar();
});
function filtrar() {
    const txt    = document.getElementById('buscar').value.toLowerCase();
    const estado = document.getElementById('filtroEstado').value.toLowerCase();
    document.querySelectorAll('#tablaPedidos tbody tr:not([id^="det_"])').forEach(r => {
        const texto  = r.innerText.toLowerCase();
        const matchT = txt    ? texto.includes(txt)    : true;
        const matchE = estado ? texto.includes(estado) : true;
        r.style.display = (matchT && matchE) ? '' : 'none';
    });
}
</script>
</body>
</html>
