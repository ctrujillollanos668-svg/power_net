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

// Filtros GET
$filtroDesde  = $_GET['desde']  ?? '';
$filtroHasta  = $_GET['hasta']  ?? '';
$filtroEstado = $_GET['estado'] ?? '';
$filtroBuscar = $_GET['buscar'] ?? '';

$todasVentas = $ventaModel->filtrar(
    $filtroDesde  ?: null,
    $filtroHasta  ?: null,
    $filtroEstado ?: null,
    $filtroBuscar ?: null
);
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
                    <div class="fw-bold fs-3 text-success">$<?= number_format($totales['monto_total'] ?? 0, 0, ',', '.') ?></div>
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
                    <div class="fw-bold fs-3">$<?= number_format($totales['promedio'] ?? 0, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- GRÁFICOS RÁPIDOS -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white fw-bold">📅 Ventas últimos 30 días</div>
                <div class="card-body p-0" style="max-height:260px;overflow-y:auto;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light sticky-top">
                        <tr><th>Fecha</th><th class="text-center">Ventas</th><th class="text-end">Total</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($ventasDia as $v): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($v['dia'])) ?></td>
                        <td class="text-center"><?= $v['total_ventas'] ?></td>
                        <td class="text-end fw-semibold text-success">$<?= number_format($v['monto_total'], 0, ',', '.') ?></td>
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
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white fw-bold">🏆 Top 10 productos más vendidos</div>
                <div class="card-body p-0" style="max-height:260px;overflow-y:auto;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light sticky-top">
                        <tr><th>#</th><th>Producto</th><th class="text-center">Unidades</th><th class="text-end">Ingresos</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($topProductos as $i => $tp): ?>
                    <tr>
                        <td><span class="badge bg-dark"><?= $i+1 ?></span></td>
                        <td><?= htmlspecialchars($tp['nombre']) ?></td>
                        <td class="text-center fw-bold"><?= $tp['unidades'] ?></td>
                        <td class="text-end text-success">$<?= number_format($tp['ingresos'], 0, ',', '.') ?></td>
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

    <!-- ── FILTROS (HU-004 criterio 2) ── -->
    <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
    <form method="GET" action="" class="row g-3 align-items-end">
        <div class="col-md-2">
            <label class="form-label fw-semibold small">Desde</label>
            <input type="date" name="desde" class="form-control form-control-sm"
                   value="<?= htmlspecialchars($filtroDesde) ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold small">Hasta</label>
            <input type="date" name="hasta" class="form-control form-control-sm"
                   value="<?= htmlspecialchars($filtroHasta) ?>">
        </div>
        <div class="col-md-2">
            <label class="form-label fw-semibold small">Estado pedido</label>
            <select name="estado" class="form-select form-select-sm">
                <option value="">Todos</option>
                <option value="pendiente"  <?= $filtroEstado==='pendiente'  ? 'selected':'' ?>>Pendiente</option>
                <option value="enviado"    <?= $filtroEstado==='enviado'    ? 'selected':'' ?>>Enviado</option>
                <option value="entregado"  <?= $filtroEstado==='entregado'  ? 'selected':'' ?>>Entregado</option>
                <option value="cancelado"  <?= $filtroEstado==='cancelado'  ? 'selected':'' ?>>Cancelado</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold small">Buscar cliente / factura</label>
            <input type="text" name="buscar" class="form-control form-control-sm"
                   placeholder="Nombre o factura..."
                   value="<?= htmlspecialchars($filtroBuscar) ?>">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm px-4">🔍 Filtrar</button>
            <a href="/power-net/views/admin/pago/ventas.php" class="btn btn-outline-secondary btn-sm">✕ Limpiar</a>
        </div>
    </form>
    </div>
    </div>

    <!-- ── LISTADO DE VENTAS (HU-004 criterios 1, 3, 4, 5) ── -->
    <div class="card border-0 shadow-sm">
    <div class="card-header bg-dark text-white fw-bold d-flex justify-content-between align-items-center">
        <span>📋 Historial de ventas
            <span class="badge bg-secondary ms-2"><?= count($todasVentas) ?> registros</span>
        </span>
    </div>
    <div class="card-body p-0">
    <table class="table table-hover align-middle mb-0" id="tablaVentas">
        <thead class="table-light">
            <tr>
                <th>#Venta</th>
                <th>#Pedido</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Método</th>
                <th>Factura</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th class="text-center">Acciones</th>
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
            <td class="text-center">
                <div class="d-flex gap-1 justify-content-center">

                    <!-- Ver detalle (HU-004 criterio 3) -->
                    <button class="btn btn-sm btn-outline-dark"
                            onclick="verDetalle(<?= $v['id_venta'] ?>)"
                            title="Ver detalle">
                        <i class="bi bi-eye"></i>
                    </button>

                    <!-- Eliminar (HU-004 criterio 4) -->
                    <button class="btn btn-sm btn-outline-danger"
                            onclick="confirmarEliminar(<?= $v['id_venta'] ?>)"
                            title="Eliminar registro">
                        <i class="bi bi-trash"></i>
                    </button>

                </div>
            </td>
        </tr>

        <!-- Fila detalle oculta -->
        <tr id="det_<?= $v['id_venta'] ?>" style="display:none;">
            <td colspan="9" class="bg-light p-3">
                <?php
                $detalle = $ventaModel->obtenerDetalle($v['id_venta']);
                ?>
                <?php if (!empty($detalle)): ?>
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th>Producto</th><th class="text-center">Cant.</th><th class="text-end">Precio unit.</th><th class="text-end">Subtotal</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($detalle as $d): ?>
                    <tr>
                        <td><?= htmlspecialchars($d['nombre'] ?? '—') ?></td>
                        <td class="text-center"><?= $d['cantidad'] ?></td>
                        <td class="text-end">$<?= number_format($d['precio_unitario'], 0, ',', '.') ?></td>
                        <td class="text-end fw-bold">$<?= number_format($d['subtotal'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <p class="text-muted mb-0 small">Sin detalle disponible.</p>
                <?php endif; ?>
            </td>
        </tr>

        <?php endforeach; ?>
        <?php if (empty($todasVentas)): ?>
        <tr><td colspan="9" class="text-center text-muted py-4">Sin ventas registradas</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
    </div>

</div>
</div>

<!-- MODAL ACTUALIZAR ESTADO -->
<div class="modal fade" id="modalEstado" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
<form method="POST" action="/power-net/public/index.php?action=actualizar_estado_venta">
<div class="modal-header bg-dark text-white border-0">
    <h5 class="modal-title fw-bold">🔄 Actualizar estado de la venta</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (isset($_SESSION['alert'])): ?>
<script>
Swal.fire({
    icon: '<?= $_SESSION['alert']['icon'] ?>',
    title: '<?= addslashes($_SESSION['alert']['title']) ?>',
    text: '<?= addslashes($_SESSION['alert']['text']) ?>'
});
</script>
<?php unset($_SESSION['alert']); ?>
<?php endif; ?>

<script>
function verDetalle(id) {
    const row = document.getElementById('det_' + id);
    row.style.display = row.style.display === 'none' ? '' : 'none';
}

function confirmarEliminar(id) {
    Swal.fire({
        title: '¿Eliminar registro de venta?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(r => {
        if (r.isConfirmed)
            window.location.href = '/power-net/public/index.php?action=eliminar_venta&id=' + id;
    });
}
</script>
</body>
</html>
