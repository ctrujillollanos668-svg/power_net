<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 1) {
    header("Location: /power-net/public/index.php"); exit;
}
require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../models/Devolucion.php';

$db  = (new Database())->getConnection();
$dev = new Devolucion();

$devoluciones = $dev->obtenerTodas();
$total        = count($devoluciones);
$montoTotal   = array_sum(array_column($devoluciones, 'monto_devolucion'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Devoluciones</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="d-flex">
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
<div class="flex-grow-1 p-4" style="margin-left:250px;margin-top:70px;">

    <h2 class="fw-bold mb-4">🔁 Devoluciones</h2>

    <!-- MÉTRICAS -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Total solicitudes</div>
                    <div class="fw-bold fs-3"><?= $total ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Monto total devuelto</div>
                    <div class="fw-bold fs-3 text-danger">$<?= number_format($montoTotal, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Promedio por devolución</div>
                    <div class="fw-bold fs-3">$<?= $total ? number_format($montoTotal / $total, 0, ',', '.') : 0 ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- BUSCADOR -->
    <input type="text" id="buscar" class="form-control mb-3 w-25" placeholder="Buscar devolución...">

    <!-- TABLA -->
    <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
    <table class="table table-hover align-middle mb-0" id="tablaDev">
        <thead class="table-dark">
            <tr>
                <th>#Dev</th>
                <th>#Pedido</th>
                <th>Cliente</th>
                <th>Monto</th>
                <th>Fecha</th>
                <th class="text-center">Detalle</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($devoluciones as $d): ?>
        <tr>
            <td class="fw-bold">#<?= $d['id_devolucion'] ?></td>
            <td>#<?= $d['id_pedido'] ?></td>
            <td><?= htmlspecialchars($d['nombre_cliente'] ?? '—') ?></td>
            <td class="fw-semibold text-danger">$<?= number_format($d['monto_devolucion'], 0, ',', '.') ?></td>
            <td><?= $d['fecha_devolucion'] ? date('d/m/Y H:i', strtotime($d['fecha_devolucion'])) : '—' ?></td>
            <td class="text-center">
                <button class="btn btn-sm btn-outline-dark"
                        onclick="verDetalleDev(<?= $d['id_devolucion'] ?>)">
                    <i class="bi bi-eye"></i>
                </button>
            </td>
        </tr>
        <!-- Fila detalle oculta -->
        <tr id="devdet_<?= $d['id_devolucion'] ?>" style="display:none;">
            <td colspan="6" class="bg-light p-3">
                <?php $detDev = $dev->obtenerDetalle($d['id_devolucion']); ?>

                <!-- Motivo general del cliente -->
                <?php
                $motivoGeneral = $d['motivo'] ?? null;
                if ($motivoGeneral): ?>
                <div style="background:#fff7ed;border:1.5px solid #fb923c;border-radius:10px;padding:12px 16px;margin-bottom:12px;">
                    <strong style="font-size:13px;color:#9a3412;">💬 Mensaje del cliente:</strong>
                    <p style="margin:4px 0 0;font-size:14px;color:#374151;"><?= htmlspecialchars($motivoGeneral) ?></p>
                </div>
                <?php endif; ?>

                <?php if (!empty($detDev)): ?>
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Cantidad</th>
                            <th>Motivo del producto</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($detDev as $dd): ?>
                    <tr>
                        <td class="fw-semibold"><?= htmlspecialchars($dd['nombre'] ?? '—') ?></td>
                        <td class="text-center"><?= $dd['cantidad'] ?></td>
                        <td>
                            <span class="badge bg-secondary"><?= htmlspecialchars($dd['motivo'] ?? '—') ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <p class="text-muted mb-0">Sin detalle de productos.</p>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($devoluciones)): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">No hay devoluciones registradas</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
    </div>

</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
function verDetalleDev(id) {
    const row = document.getElementById('devdet_' + id);
    row.style.display = row.style.display === 'none' ? '' : 'none';
}
document.getElementById('buscar').addEventListener('keyup', function() {
    const f = this.value.toLowerCase();
    document.querySelectorAll('#tablaDev tbody tr:not([id^="devdet_"])').forEach(r => {
        r.style.display = r.innerText.toLowerCase().includes(f) ? '' : 'none';
    });
});
</script>
</body>
</html>
