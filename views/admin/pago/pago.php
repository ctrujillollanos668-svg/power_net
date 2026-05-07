<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 1) {
    header("Location: /power-net/public/index.php"); exit;
}
require_once __DIR__ . '/../../../config/Database.php';

$db   = (new Database())->getConnection();
$stmt = $db->query(
    "SELECT pg.*, p.total_pedido, p.estado_pedido,
            pe.nombre_persona AS nombre_cliente
     FROM pago pg
     INNER JOIN pedido p   ON pg.id_pedido  = p.id_pedido
     INNER JOIN cliente c  ON p.id_cliente  = c.id_cliente
     INNER JOIN persona pe ON c.id_persona  = pe.id_persona
     ORDER BY pg.id_pago DESC"
);
$pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalRecaudado = array_sum(array_column($pagos, 'monto'));
$totalPagos     = count($pagos);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Pagos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="d-flex">
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
<div class="flex-grow-1 p-4" style="margin-left:250px;margin-top:70px;">

    <h2 class="fw-bold mb-4">💳 Pagos</h2>

    <!-- MÉTRICAS -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Total recaudado</div>
                    <div class="fw-bold fs-3 text-success">$<?= number_format($totalRecaudado, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Transacciones</div>
                    <div class="fw-bold fs-3"><?= $totalPagos ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Promedio por pago</div>
                    <div class="fw-bold fs-3">$<?= $totalPagos ? number_format($totalRecaudado / $totalPagos, 0, ',', '.') : 0 ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- BUSCADOR -->
    <input type="text" id="buscar" class="form-control mb-3 w-25" placeholder="Buscar pago...">

    <!-- TABLA -->
    <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
    <table class="table table-hover align-middle mb-0" id="tablaPagos">
        <thead class="table-dark">
            <tr>
                <th>#Pago</th>
                <th>#Pedido</th>
                <th>Cliente</th>
                <th>Monto</th>
                <th>Método</th>
                <th>Factura</th>
                <th>Fecha</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($pagos as $pg): ?>
        <tr>
            <td class="fw-bold">#<?= $pg['id_pago'] ?></td>
            <td>#<?= $pg['id_pedido'] ?></td>
            <td><?= htmlspecialchars($pg['nombre_cliente'] ?? '—') ?></td>
            <td class="fw-semibold text-success">$<?= number_format($pg['monto'], 0, ',', '.') ?></td>
            <td>
                <?= $pg['metodo_pago'] === 'tarjeta' ? '💳' : '🏦' ?>
                <?= ucfirst(htmlspecialchars($pg['metodo_pago'] ?? '—')) ?>
            </td>
            <td><code><?= htmlspecialchars($pg['factura'] ?? '—') ?></code></td>
            <td><?= $pg['fecha_pago'] ? date('d/m/Y H:i', strtotime($pg['fecha_pago'])) : '—' ?></td>
            <td>
                <span class="badge <?= $pg['estado_pago'] === 'pagado' ? 'bg-success' : 'bg-warning text-dark' ?>">
                    <?= ucfirst($pg['estado_pago'] ?? '—') ?>
                </span>
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
document.getElementById('buscar').addEventListener('keyup', function() {
    const f = this.value.toLowerCase();
    document.querySelectorAll('#tablaPagos tbody tr').forEach(r => {
        r.style.display = r.innerText.toLowerCase().includes(f) ? '' : 'none';
    });
});
</script>
</body>
</html>
