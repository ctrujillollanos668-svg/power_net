<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 1) {
    header("Location: /power-net/public/index.php"); exit;
}
require_once __DIR__ . '/../../../models/Venta.php';

$ventaModel   = new Venta();
$totales      = $ventaModel->totales();
$ventasDia    = $ventaModel->ventasPorDia(30);
$topProductos = $ventaModel->topProductos(10);
$todasVentas  = $ventaModel->obtenerTodas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ventas</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="d-flex">
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
<div class="flex-grow-1 p-4" style="margin-left:250px;margin-top:70px;">

    <h2 class="fw-bold mb-4">💰 Ventas</h2>

    <!-- MÉTRICAS -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Total ingresos</div>
                    <div class="fw-bold fs-3 text-success">
                        $<?= number_format($totales['monto_total'] ?? 0, 0, ',', '.') ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Ventas realizadas</div>
                    <div class="fw-bold fs-3"><?= $totales['total_ventas'] ?? 0 ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Ticket promedio</div>
                    <div class="fw-bold fs-3">
                        $<?= number_format($totales['promedio'] ?? 0, 0, ',', '.') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">

        <!-- VENTAS POR DÍA -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white fw-bold">📅 Ventas últimos 30 días</div>
                <div class="card-body p-0" style="max-height:320px;overflow-y:auto;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>Fecha</th>
                            <th class="text-center">Ventas</th>
                            <th class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($ventasDia as $v): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($v['dia'])) ?></td>
                        <td class="text-center"><?= $v['total_ventas'] ?></td>
                        <td class="text-end fw-semibold text-success">
                            $<?= number_format($v['monto_total'], 0, ',', '.') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($ventasDia)): ?>
                    <tr><td colspan="3" class="text-center text-muted py-3">Sin ventas aún</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <!-- TOP PRODUCTOS -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white fw-bold">🏆 Productos más vendidos</div>
                <div class="card-body p-0" style="max-height:320px;overflow-y:auto;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>#</th>
                            <th>Producto</th>
                            <th class="text-center">Unidades</th>
                            <th class="text-end">Ingresos</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($topProductos as $i => $tp): ?>
                    <tr>
                        <td><span class="badge bg-dark"><?= $i + 1 ?></span></td>
                        <td><?= htmlspecialchars($tp['nombre']) ?></td>
                        <td class="text-center fw-bold"><?= $tp['unidades'] ?></td>
                        <td class="text-end text-success">
                            $<?= number_format($tp['ingresos'], 0, ',', '.') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($topProductos)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-3">Sin datos</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

    </div>

    <!-- TODAS LAS VENTAS -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-dark text-white fw-bold d-flex justify-content-between align-items-center">
            <span>📋 Historial de ventas</span>
            <input type="text" id="buscarVenta" class="form-control form-control-sm w-25"
                   placeholder="Buscar...">
        </div>
        <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0" id="tablaVentas">
            <thead class="table-light">
                <tr>
                    <th>#Venta</th>
                    <th>#Pedido</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Método pago</th>
                    <th>Factura</th>
                    <th>Fecha</th>
                    <th>Estado pedido</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($todasVentas as $v): ?>
            <?php
            $estado = strtolower($v['estado_pedido'] ?? '');
            $badge  = match($estado) {
                'entregado' => 'bg-success',
                'enviado'   => 'bg-primary',
                'pendiente' => 'bg-warning text-dark',
                'cancelado' => 'bg-danger',
                default     => 'bg-secondary'
            };
            ?>
            <tr>
                <td class="fw-bold">#<?= $v['id_venta'] ?></td>
                <td>#<?= $v['id_pedido'] ?></td>
                <td><?= htmlspecialchars($v['nombre_cliente'] ?? '—') ?></td>
                <td class="fw-semibold text-success">$<?= number_format($v['total'], 0, ',', '.') ?></td>
                <td><?= ucfirst(htmlspecialchars($v['metodo_pago'] ?? '—')) ?></td>
                <td><code class="small"><?= htmlspecialchars($v['factura'] ?? '—') ?></code></td>
                <td><?= $v['fecha_venta'] ? date('d/m/Y H:i', strtotime($v['fecha_venta'])) : '—' ?></td>
                <td><span class="badge <?= $badge ?>"><?= ucfirst($v['estado_pedido'] ?? '—') ?></span></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($todasVentas)): ?>
            <tr><td colspan="8" class="text-center text-muted py-4">Sin ventas registradas</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>

</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('buscarVenta').addEventListener('keyup', function() {
    const f = this.value.toLowerCase();
    document.querySelectorAll('#tablaVentas tbody tr').forEach(r => {
        r.style.display = r.innerText.toLowerCase().includes(f) ? '' : 'none';
    });
});
</script>
</body>
</html>
