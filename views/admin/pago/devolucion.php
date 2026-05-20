<?php
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 1) {
    header("Location: index.php"); exit;
}
require_once __DIR__ . '/../../../models/Devolucion.php';

$dev = new Devolucion();

$filtroEstado = $_GET['estado'] ?? '';
$filtroDesde  = $_GET['desde']  ?? '';
$filtroHasta  = $_GET['hasta']  ?? '';

$devoluciones = $dev->filtrar($filtroEstado ?: null, $filtroDesde ?: null, $filtroHasta ?: null);

$total      = count($devoluciones);
$pendientes = count(array_filter($devoluciones, fn($d) => ($d['estado'] ?? 'pendiente') === 'pendiente'));
$aprobadas  = count(array_filter($devoluciones, fn($d) => ($d['estado'] ?? '') === 'aprobada'));
$montoTotal = array_sum(array_column($devoluciones, 'monto_devolucion'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Devoluciones — Power Net</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
/* ── Estado badges ── */
.est-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 12px; border-radius: 20px;
    font-size: 12px; font-weight: 700;
}
.est-pendiente  { background:#fef3c7; color:#92400e; }
.est-aprobada   { background:#d1fae5; color:#065f46; }
.est-rechazada  { background:#fee2e2; color:#991b1b; }
.est-completada { background:#dbeafe; color:#1e40af; }

/* ── Métricas ── */
.metric-card { border-radius:14px; border:none; box-shadow:0 2px 10px rgba(0,0,0,.06); transition:transform .2s; }
.metric-card:hover { transform:translateY(-3px); }
.metric-icon { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:20px; }

/* ── Tabla ── */
#tablaDev thead th { font-size:11px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; }
#tablaDev tbody tr:not(.fila-det):hover { background:#f5f3ff; }

/* ── Fila detalle ── */
.fila-det td { background:#f8fafc; border-top:none !important; }
.det-inner { border-radius:12px; overflow:hidden; border:1px solid #e5e7eb; }
.det-inner table thead th { background:#1e293b; color:#fff; font-size:12px; padding:10px 14px; }
.det-inner table tbody td { font-size:13px; padding:10px 14px; }

/* ── Botones acción ── */
.btn-accion {
    width:34px; height:34px; border-radius:10px; border:2px solid;
    background:transparent; cursor:pointer; display:inline-flex;
    align-items:center; justify-content:center; font-size:15px;
    transition:all .18s;
}
.btn-ver    { border-color:#e5e7eb; color:#374151; }
.btn-ver:hover    { border-color:#7c3aed; color:#7c3aed; background:#faf5ff; }
.btn-ver.activo   { background:#7c3aed; border-color:#7c3aed; color:#fff; }
.btn-aprobar      { border-color:#10b981; color:#065f46; }
.btn-aprobar:hover { background:#10b981; color:#fff; }
.btn-rechazar     { border-color:#ef4444; color:#991b1b; }
.btn-rechazar:hover { background:#ef4444; color:#fff; }
.btn-reembolso    { border-color:#3b82f6; color:#1e40af; padding:0 12px; width:auto; font-size:12px; font-weight:700; gap:4px; }
.btn-reembolso:hover { background:#3b82f6; color:#fff; }
</style>
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="d-flex">
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
<div class="flex-grow-1 p-4" style="margin-left:250px;margin-top:70px;">

    <!-- Título -->
    <div class="d-flex align-items-center gap-3 mb-4">
        <div style="width:44px;height:44px;background:linear-gradient(135deg,#7c3aed,#6d28d9);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;">🔁</div>
        <div>
            <h2 class="fw-bold mb-0" style="font-size:22px;">Gestión de Devoluciones</h2>
            <p class="text-muted mb-0" style="font-size:13px;">Revisa, aprueba o rechaza las solicitudes de los clientes</p>
        </div>
    </div>

    <!-- MÉTRICAS -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card metric-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon" style="background:#f3f4f6;">📋</div>
                    <div>
                        <div class="text-muted" style="font-size:12px;">Total solicitudes</div>
                        <div class="fw-bold" style="font-size:26px;"><?= $total ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card metric-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon" style="background:#fef3c7;">⏳</div>
                    <div>
                        <div class="text-muted" style="font-size:12px;">Pendientes</div>
                        <div class="fw-bold" style="font-size:26px;color:#d97706;"><?= $pendientes ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card metric-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon" style="background:#d1fae5;">✅</div>
                    <div>
                        <div class="text-muted" style="font-size:12px;">Aprobadas</div>
                        <div class="fw-bold" style="font-size:26px;color:#059669;"><?= $aprobadas ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card metric-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon" style="background:#fee2e2;">💸</div>
                    <div>
                        <div class="text-muted" style="font-size:12px;">Monto total</div>
                        <div class="fw-bold" style="font-size:20px;color:#dc2626;">$<?= number_format($montoTotal, 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="card border-0 shadow-sm mb-3" style="border-radius:12px;">
    <div class="card-body py-3 px-4">
    <form method="GET" class="d-flex gap-3 flex-wrap align-items-end">
        <div>
            <label class="form-label fw-semibold" style="font-size:12px;">Estado</label>
            <select name="estado" class="form-select form-select-sm" style="border-radius:10px;border:2px solid #e5e7eb;min-width:160px;">
                <option value="">Todos los estados</option>
                <option value="pendiente"  <?= $filtroEstado==='pendiente'  ?'selected':'' ?>>⏳ Pendiente</option>
                <option value="aprobada"   <?= $filtroEstado==='aprobada'   ?'selected':'' ?>>✅ Aprobada</option>
                <option value="rechazada"  <?= $filtroEstado==='rechazada'  ?'selected':'' ?>>❌ Rechazada</option>
                <option value="completada" <?= $filtroEstado==='completada' ?'selected':'' ?>>💰 Completada</option>
            </select>
        </div>
        <div>
            <label class="form-label fw-semibold" style="font-size:12px;">Desde</label>
            <input type="date" name="desde" class="form-control form-control-sm" value="<?= $filtroDesde ?>" style="border-radius:10px;border:2px solid #e5e7eb;">
        </div>
        <div>
            <label class="form-label fw-semibold" style="font-size:12px;">Hasta</label>
            <input type="date" name="hasta" class="form-control form-control-sm" value="<?= $filtroHasta ?>" style="border-radius:10px;border:2px solid #e5e7eb;">
        </div>
        <div class="d-flex gap-2 align-self-end">
            <button type="submit" class="btn btn-dark btn-sm px-4" style="border-radius:10px;">🔍 Filtrar</button>
            <a href="index.php?action=devoluciones" class="btn btn-outline-secondary btn-sm" style="border-radius:10px;">✕ Limpiar</a>
        </div>
    </form>
    </div>
    </div>

    <!-- TABLA -->
    <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
    <div class="card-body p-0">
    <table class="table table-hover align-middle mb-0" id="tablaDev">
        <thead style="background:#1e293b;">
            <tr>
                <th style="color:#94a3b8;padding:14px 16px;">#Dev</th>
                <th style="color:#94a3b8;">#Pedido</th>
                <th style="color:#94a3b8;">Cliente</th>
                <th style="color:#94a3b8;">Monto</th>
                <th style="color:#94a3b8;">Fecha</th>
                <th style="color:#94a3b8;text-align:center;">Estado</th>
                <th style="color:#94a3b8;text-align:center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($devoluciones as $d): ?>
        <?php
        $est    = $d['estado'] ?? 'pendiente';
        $iconos = ['pendiente'=>'⏳','aprobada'=>'✅','rechazada'=>'❌','completada'=>'💰'];
        ?>
        <tr data-id="<?= $d['id_devolucion'] ?>">
            <td style="padding:14px 16px;">
                <span style="font-weight:800;color:#7c3aed;">#<?= $d['id_devolucion'] ?></span>
            </td>
            <td><span style="font-weight:600;">#<?= $d['id_pedido'] ?></span></td>
            <td style="font-size:14px;"><?= htmlspecialchars($d['nombre_cliente'] ?? '—') ?></td>
            <td><span style="font-weight:700;color:#dc2626;font-size:15px;">$<?= number_format($d['monto_devolucion'], 0, ',', '.') ?></span></td>
            <td style="font-size:13px;color:#6b7280;">
                <?= $d['fecha_devolucion'] ? date('d/m/Y H:i', strtotime($d['fecha_devolucion'])) : '—' ?>
            </td>
            <td style="text-align:center;">
                <span class="est-badge est-<?= $est ?>">
                    <?= $iconos[$est] ?? '📋' ?> <?= ucfirst($est) ?>
                </span>
            </td>
            <td style="text-align:center;">
                <div class="d-flex gap-1 justify-content-center">
                    <!-- Ver detalle -->
                    <button class="btn-accion btn-ver" id="btndev_<?= $d['id_devolucion'] ?>"
                            onclick="verDetDev(<?= $d['id_devolucion'] ?>)" title="Ver detalle">
                        <i class="bi bi-eye"></i>
                    </button>

                    <?php if ($est === 'pendiente'): ?>
                    <!-- Aprobar -->
                    <button class="btn-accion btn-aprobar" title="Aprobar devolución"
                            onclick="confirmarAprobar(<?= $d['id_devolucion'] ?>, <?= $d['id_pedido'] ?>)">
                        <i class="bi bi-check-circle"></i>
                    </button>
                    <!-- Rechazar -->
                    <button class="btn-accion btn-rechazar" title="Rechazar devolución"
                            onclick="abrirRechazar(<?= $d['id_devolucion'] ?>)">
                        <i class="bi bi-x-circle"></i>
                    </button>

                    <?php elseif ($est === 'aprobada'): ?>
                    <!-- Reembolso -->
                    <button class="btn-accion btn-reembolso" title="Procesar reembolso"
                            onclick="confirmarReembolso(<?= $d['id_devolucion'] ?>, '<?= number_format($d['monto_devolucion'], 0, ',', '.') ?>')">
                        <i class="bi bi-cash-coin"></i> Reembolso
                    </button>
                    <?php endif; ?>
                </div>
            </td>
        </tr>

        <!-- Fila detalle oculta -->
        <tr id="devdet_<?= $d['id_devolucion'] ?>" class="fila-det" style="display:none;">
            <td colspan="7" style="padding:0 16px 16px;">
                <div class="det-inner mt-2">
                    <?php
                    $motivo   = $d['motivo'] ?? null;
                    $detDev   = $dev->obtenerDetalle($d['id_devolucion']);
                    ?>

                    <?php if ($motivo): ?>
                    <div style="padding:14px 18px;background:#fff7ed;border-bottom:1px solid #fed7aa;">
                        <strong style="font-size:13px;color:#9a3412;">💬 Mensaje del cliente:</strong>
                        <p style="margin:4px 0 0;font-size:14px;color:#374151;"><?= htmlspecialchars($motivo) ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($d['motivo_rechazo'])): ?>
                    <div style="padding:14px 18px;background:#fef2f2;border-bottom:1px solid #fca5a5;">
                        <strong style="font-size:13px;color:#dc2626;">❌ Motivo de rechazo:</strong>
                        <p style="margin:4px 0 0;font-size:14px;color:#374151;"><?= htmlspecialchars($d['motivo_rechazo']) ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($detDev)): ?>
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th style="text-align:center;">Cantidad</th>
                                <th>Motivo del cliente</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($detDev as $dd): ?>
                        <tr>
                            <td style="font-weight:600;"><?= htmlspecialchars($dd['nombre'] ?? '—') ?></td>
                            <td style="text-align:center;">
                                <span style="background:#f3f4f6;padding:3px 12px;border-radius:20px;font-weight:700;">
                                    <?= $dd['cantidad'] ?>
                                </span>
                            </td>
                            <td>
                                <span style="background:#f3f4f6;padding:3px 10px;border-radius:20px;font-size:12px;color:#374151;">
                                    <?= htmlspecialchars($dd['motivo'] ?? '—') ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div style="padding:20px;text-align:center;color:#9ca3af;">
                        <i class="bi bi-inbox" style="font-size:28px;"></i>
                        <p class="mt-2 mb-0">Sin detalle de productos</p>
                    </div>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>

        <?php if (empty($devoluciones)): ?>
        <tr>
            <td colspan="7" style="text-align:center;padding:48px;color:#9ca3af;">
                <div style="font-size:40px;margin-bottom:8px;">🔁</div>
                <p class="mb-0">No hay devoluciones registradas</p>
            </td>
        </tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
    </div>

</div>
</div>

<!-- MODAL RECHAZAR -->
<div class="modal fade" id="modalRechazar" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content border-0 shadow-lg" style="border-radius:18px;overflow:hidden;">
<form method="POST" action="index.php?action=rechazar_devolucion">
<div class="modal-header border-0" style="background:#dc2626;color:#fff;padding:20px 24px;">
    <h5 class="modal-title fw-bold">❌ Rechazar Devolución</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body p-4">
    <input type="hidden" name="id_devolucion" id="rech_id">
    <div style="background:#fef2f2;border:1.5px solid #fca5a5;border-radius:10px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#9a3412;">
        ⚠️ El cliente recibirá una notificación con el motivo del rechazo.
    </div>
    <label class="form-label fw-semibold" style="font-size:13px;">Motivo del rechazo <span style="color:#dc2626;">*</span></label>
    <textarea name="motivo_rechazo" class="form-control" rows="3"
              style="border:2px solid #e5e7eb;border-radius:10px;"
              placeholder="Explica por qué se rechaza la devolución..." required></textarea>
</div>
<div class="modal-footer border-0" style="background:#f9fafb;padding:16px 24px;">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
    <button type="submit" class="btn btn-danger fw-bold px-4" style="border-radius:10px;">❌ Confirmar rechazo</button>
</div>
</form>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

// ── Aprobar devolución ──
function confirmarAprobar(id, idPedido) {
    Swal.fire({
        title: '¿Aprobar devolución?',
        html: `La devolución <strong>#${id}</strong> del pedido <strong>#${idPedido}</strong> será aprobada.<br><br>
               <span style="font-size:13px;color:#6b7280;">El cliente podrá ver el estado actualizado en su cuenta.</span>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '✅ Sí, aprobar',
        cancelButtonText: 'Cancelar',
        customClass: { popup: 'swal-rounded' }
    }).then(r => {
        if (r.isConfirmed) {
            Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            window.location.href = `index.php?action=aprobar_devolucion&id=${id}`;
        }
    });
}

// ── Rechazar devolución (abre modal con textarea) ──
function abrirRechazar(id) {
    document.getElementById('rech_id').value = id;
    new bootstrap.Modal(document.getElementById('modalRechazar')).show();
}

// ── Procesar reembolso ──
function confirmarReembolso(id, monto) {
    Swal.fire({
        title: '¿Procesar reembolso?',
        html: `Se registrará el reembolso de <strong style="color:#dc2626;">$${monto}</strong> para la devolución <strong>#${id}</strong>.<br><br>
               <span style="font-size:13px;color:#6b7280;">Esta acción marcará la devolución como completada.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '💰 Sí, procesar reembolso',
        cancelButtonText: 'Cancelar',
        customClass: { popup: 'swal-rounded' }
    }).then(r => {
        if (r.isConfirmed) {
            Swal.fire({ title: 'Procesando reembolso...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            window.location.href = `index.php?action=reembolso_devolucion&id=${id}`;
        }
    });
}

// ── Ver / ocultar detalle ──
function verDetDev(id) {
    const fila = document.getElementById('devdet_' + id);
    const btn  = document.getElementById('btndev_' + id);
    const visible = fila.style.display !== 'none';

    document.querySelectorAll('[id^="devdet_"]').forEach(f => f.style.display = 'none');
    document.querySelectorAll('[id^="btndev_"]').forEach(b => b.classList.remove('activo'));

    if (!visible) {
        fila.style.display = '';
        btn.classList.add('activo');
        fila.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

// ── Alerta de sesión (toast) ──
<?php if (!empty($_SESSION['alert'])): ?>
Swal.fire({
    icon:             '<?= $_SESSION['alert']['icon'] ?>',
    title:            '<?= addslashes($_SESSION['alert']['title']) ?>',
    text:             '<?= addslashes($_SESSION['alert']['text']) ?>',
    timer:            2800,
    showConfirmButton: false,
    toast:            true,
    position:         'top-end'
});
<?php unset($_SESSION['alert']); ?>
<?php endif; ?>
</script>
<style>.swal-rounded { border-radius: 20px !important; }</style>
</body>
</html>
