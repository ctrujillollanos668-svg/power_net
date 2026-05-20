<?php
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 1) {
    header("Location: index.php"); exit;
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

// Cargar métodos de pago desde config
$metodosPagoConfig = require __DIR__ . '/MetodosPago.php';

$tabActivo = $_GET['tab'] ?? 'transacciones';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Pagos — Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background:#f4f6fb; font-family:'Segoe UI',sans-serif; }
.tab-nav { display:flex; gap:4px; background:#fff; border-radius:14px; padding:6px; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:24px; width:fit-content; }
.tab-btn { padding:10px 24px; border:none; border-radius:10px; background:transparent; color:#6b7280; font-weight:600; font-size:14px; cursor:pointer; transition:all .2s; text-decoration:none; }
.tab-btn.activo { background:#1a1a2e; color:#fff; }
.tab-btn:hover  { background:#f3f4f6; color:#1a1a2e; }
.tab-btn.activo:hover { background:#1a1a2e; color:#fff; }
.metodo-card { background:#fff; border-radius:14px; box-shadow:0 2px 8px rgba(0,0,0,.06); padding:18px 22px; margin-bottom:12px; display:flex; align-items:center; gap:16px; }
.metodo-icon { font-size:30px; flex-shrink:0; }
.badge-activo   { background:#dcfce7; color:#166534; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700; }
.badge-inactivo { background:#fee2e2; color:#991b1b; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:700; }
.btn-ac { border:none; border-radius:8px; width:34px; height:34px; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; font-size:14px; transition:all .2s; }
.btn-ac-edit { background:#eff6ff; color:#2563eb; }
.btn-ac-edit:hover { background:#2563eb; color:#fff; }
</style>
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="d-flex">
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
<div class="flex-grow-1 p-4" style="margin-left:250px;margin-top:70px;">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="fw-bold mb-0">💳 Pagos</h2>
        <?php if ($tabActivo === 'metodos'): ?>
        <button class="btn btn-dark fw-bold px-4" data-bs-toggle="modal" data-bs-target="#modalNuevo">
            ➕ Agregar método
        </button>
        <?php endif; ?>
    </div>

    <!-- ALERTAS -->
    <?php if (!empty($_SESSION['alert'])): $a = $_SESSION['alert']; unset($_SESSION['alert']); ?>
    <div class="alert alert-<?= $a['icon'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
        <strong><?= htmlspecialchars($a['title']) ?>:</strong> <?= htmlspecialchars($a['text']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- TABS -->
    <div class="tab-nav">
        <a href="index.php?action=pagos&tab=transacciones"
           class="tab-btn <?= $tabActivo === 'transacciones' ? 'activo' : '' ?>">
            📋 Transacciones
        </a>
        <a href="index.php?action=pagos&tab=metodos"
           class="tab-btn <?= $tabActivo === 'metodos' ? 'activo' : '' ?>">
            🏧 Métodos de Pago
        </a>
    </div>

    <!-- ======================== TAB: TRANSACCIONES ======================== -->
    <?php if ($tabActivo === 'transacciones'): ?>

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

    <input type="text" id="buscar" class="form-control mb-3 w-25" placeholder="Buscar pago...">

    <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
    <table class="table table-hover align-middle mb-0" id="tablaPagos">
        <thead class="table-dark">
            <tr>
                <th>#Pago</th><th>#Pedido</th><th>Cliente</th>
                <th>Monto</th><th>Método</th><th>Factura</th>
                <th>Fecha</th><th>Estado</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($pagos as $pg): ?>
        <tr>
            <td class="fw-bold">#<?= $pg['id_pago'] ?></td>
            <td>#<?= $pg['id_pedido'] ?></td>
            <td><?= htmlspecialchars($pg['nombre_cliente'] ?? '—') ?></td>
            <td class="fw-semibold text-success">$<?= number_format($pg['monto'], 0, ',', '.') ?></td>
            <td><?= $pg['metodo_pago'] === 'tarjeta' ? '💳' : '🏦' ?> <?= ucfirst(htmlspecialchars($pg['metodo_pago'] ?? '—')) ?></td>
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

    <!-- ======================== TAB: MÉTODOS DE PAGO ======================== -->
    <?php elseif ($tabActivo === 'metodos'): ?>

    <div class="alert alert-info border-0" style="border-radius:12px;font-size:14px;">
        💡 Estos son los métodos que verán los clientes al pagar. Edítalos según los medios que acepte tu negocio.
    </div>

    <?php foreach ($metodosPagoConfig as $i => $m): ?>
    <div class="metodo-card">
        <div class="metodo-icon"><?= $m['icono'] ?></div>
        <div class="flex-grow-1">
            <div class="fw-bold" style="font-size:15px;color:#1a1a2e;"><?= htmlspecialchars($m['nombre']) ?></div>
            <div class="text-muted" style="font-size:13px;"><?= htmlspecialchars($m['descripcion']) ?></div>
            <div style="font-size:12px;color:#7c3aed;margin-top:3px;">📋 <?= htmlspecialchars($m['instrucciones']) ?></div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="<?= $m['activo'] ? 'badge-activo' : 'badge-inactivo' ?>">
                <?= $m['activo'] ? '✅ Activo' : '⛔ Inactivo' ?>
            </span>
            <button class="btn-ac btn-ac-edit" title="Editar"
                    onclick="abrirEditar(<?= $i ?>,'<?= htmlspecialchars($m['id'],ENT_QUOTES) ?>','<?= htmlspecialchars($m['nombre'],ENT_QUOTES) ?>','<?= htmlspecialchars($m['icono'],ENT_QUOTES) ?>','<?= htmlspecialchars($m['descripcion'],ENT_QUOTES) ?>','<?= htmlspecialchars($m['instrucciones'],ENT_QUOTES) ?>',<?= $m['activo'] ? 'true' : 'false' ?>)">
                ✏️
            </button>
            <button class="btn-ac" style="background:#fef2f2;color:#dc2626;"
                    title="Eliminar"
                    onclick="confirmarEliminar(<?= $i ?>,'<?= htmlspecialchars($m['nombre'],ENT_QUOTES) ?>')">
                🗑️
            </button>
        </div>
    </div>
    <?php endforeach; ?>

    <?php endif; ?>

</div>
</div>

<!-- MODAL AGREGAR MÉTODO -->
<div class="modal fade" id="modalNuevo" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title fw-bold">➕ Agregar método de pago</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=guardar_metodo_config">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej: Nequi, Bancolombia" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ícono (emoji)</label>
                        <input type="text" name="icono" class="form-control" placeholder="🏦" maxlength="5" value="💰">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <input type="text" name="descripcion" class="form-control" placeholder="Ej: Transferencia bancaria">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Instrucciones para el cliente</label>
                        <textarea name="instrucciones" class="form-control" rows="2"
                                  placeholder="Ej: Transferir al número 300 123 4567"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="activo" value="1" id="chkActivo" checked>
                        <label class="form-check-label fw-semibold" for="chkActivo">Activo (visible para clientes)</label>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark fw-bold px-4">💾 Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR MÉTODO -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title fw-bold">✏️ Editar método de pago</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="index.php?action=editar_metodo_config">
                <div class="modal-body p-4">
                    <input type="hidden" name="indice" id="edit_indice">
                    <input type="hidden" name="id_metodo" id="edit_id_metodo">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ícono (emoji)</label>
                        <input type="text" name="icono" id="edit_icono" class="form-control" maxlength="5">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Descripción</label>
                        <input type="text" name="descripcion" id="edit_descripcion" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Instrucciones para el cliente</label>
                        <textarea name="instrucciones" id="edit_instrucciones" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="activo" value="1" id="edit_activo">
                        <label class="form-check-label fw-semibold" for="edit_activo">Activo (visible para clientes)</label>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark fw-bold px-4">💾 Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const buscarInput = document.getElementById('buscar');
if (buscarInput) {
    buscarInput.addEventListener('keyup', function() {
        const f = this.value.toLowerCase();
        document.querySelectorAll('#tablaPagos tbody tr').forEach(r => {
            r.style.display = r.innerText.toLowerCase().includes(f) ? '' : 'none';
        });
    });
}
function abrirEditar(indice, id, nombre, icono, descripcion, instrucciones, activo) {
    document.getElementById('edit_indice').value       = indice;
    document.getElementById('edit_id_metodo').value    = id;
    document.getElementById('edit_nombre').value       = nombre;
    document.getElementById('edit_icono').value        = icono;
    document.getElementById('edit_descripcion').value  = descripcion;
    document.getElementById('edit_instrucciones').value = instrucciones;
    document.getElementById('edit_activo').checked     = activo;
    new bootstrap.Modal(document.getElementById('modalEditar')).show();
}
function confirmarEliminar(indice, nombre) {
    Swal.fire({
        title: '¿Eliminar "' + nombre + '"?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#dc2626', cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar'
    }).then(r => {
        if (r.isConfirmed)
            location.href = 'index.php?action=eliminar_metodo_config&indice=' + indice;
    });
}
</script>
</body>
</html>
