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
    require_once __DIR__ . '/../../../models/Envio.php';

    $pdo       = (new Database())->getConnection();
    $idPedido  = (int)$_GET['id'];
    $nuevoEst  = $_GET['estado'];

    $pdo->prepare("UPDATE pedido SET estado_pedido = ? WHERE id_pedido = ?")
        ->execute([$nuevoEst, $idPedido]);

    if ($nuevoEst === 'enviado') {
        $envioModel = new Envio();
        if (!$envioModel->existePorPedido($idPedido)) {
            $stmtDir = $pdo->prepare(
                "SELECT c.direccion FROM pedido p
                 INNER JOIN cliente c ON p.id_cliente = c.id_cliente
                 WHERE p.id_pedido = ? LIMIT 1"
            );
            $stmtDir->execute([$idPedido]);
            $dirRow    = $stmtDir->fetch(PDO::FETCH_ASSOC);
            $direccion = $dirRow['direccion'] ?? 'Sin dirección registrada';
            $envioModel->crear($idPedido, 'Power Net Envíos', $direccion, 0);
        } else {
            $envioModel->actualizarEstado($idPedido, 'en_camino');
        }
    }

    if ($nuevoEst === 'entregado') {
        $envioModel = new Envio();
        $envioModel->actualizarEstado($idPedido, 'entregado');
    }

    $_SESSION['alert'] = ['icon'=>'success','title'=>'Estado actualizado','text'=>'El pedido fue marcado como ' . ucfirst($nuevoEst)];
    header("Location: /power-net/views/admin/pedidos/pedidos.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Pedidos — Power Net</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
/* ── Estado badges ── */
.estado-badge {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 5px 12px; border-radius: 20px;
    font-size: 12px; font-weight: 700; letter-spacing: .3px;
}
.estado-pendiente  { background: #fef3c7; color: #92400e; }
.estado-enviado    { background: #dbeafe; color: #1e40af; }
.estado-entregado  { background: #d1fae5; color: #065f46; }
.estado-cancelado  { background: #fee2e2; color: #991b1b; }

/* ── Botones cambiar estado ── */
.btn-estado {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 12px; border-radius: 20px; font-size: 11px;
    font-weight: 700; border: 2px solid; cursor: pointer;
    transition: all .18s; background: transparent; white-space: nowrap;
}
.btn-estado-pendiente  { border-color: #f59e0b; color: #92400e; }
.btn-estado-pendiente:hover  { background: #f59e0b; color: #fff; }
.btn-estado-enviado    { border-color: #3b82f6; color: #1e40af; }
.btn-estado-enviado:hover    { background: #3b82f6; color: #fff; }
.btn-estado-entregado  { border-color: #10b981; color: #065f46; }
.btn-estado-entregado:hover  { background: #10b981; color: #fff; }
.btn-estado-cancelado  { border-color: #ef4444; color: #991b1b; }
.btn-estado-cancelado:hover  { background: #ef4444; color: #fff; }

/* ── Fila detalle ── */
.fila-detalle td { background: #f8fafc; border-top: none !important; }
.detalle-inner { border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; }
.detalle-inner table thead th { background: #1e293b; color: #fff; font-size: 12px; padding: 10px 14px; }
.detalle-inner table tbody td { font-size: 13px; padding: 10px 14px; }

/* ── Métricas ── */
.metric-card { border-radius: 14px; border: none; box-shadow: 0 2px 10px rgba(0,0,0,.06); transition: transform .2s; }
.metric-card:hover { transform: translateY(-3px); }
.metric-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }

/* ── Tabla ── */
#tablaPedidos thead th { font-size: 11px; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; }
#tablaPedidos tbody tr:not(.fila-detalle):hover { background: #f5f3ff; }
.btn-ver { width: 34px; height: 34px; border-radius: 10px; border: 2px solid #e5e7eb; background: #fff; color: #374151; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all .18s; }
.btn-ver:hover { border-color: #7c3aed; color: #7c3aed; background: #faf5ff; }
.btn-ver.activo { background: #7c3aed; border-color: #7c3aed; color: #fff; }
</style>
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="d-flex">
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
<div class="flex-grow-1 p-4" style="margin-left:250px;margin-top:70px;">

    <div class="d-flex align-items-center gap-3 mb-4">
        <div style="width:44px;height:44px;background:linear-gradient(135deg,#7c3aed,#6d28d9);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;">📦</div>
        <div>
            <h2 class="fw-bold mb-0" style="font-size:22px;">Gestión de Pedidos</h2>
            <p class="text-muted mb-0" style="font-size:13px;">Administra y actualiza el estado de los pedidos</p>
        </div>
    </div>

    <!-- MÉTRICAS -->
    <?php
    $total      = count($pedidos);
    $pendientes = count(array_filter($pedidos, fn($p) => strtolower($p['estado_pedido']) === 'pendiente'));
    $enviados   = count(array_filter($pedidos, fn($p) => strtolower($p['estado_pedido']) === 'enviado'));
    $entregados = count(array_filter($pedidos, fn($p) => strtolower($p['estado_pedido']) === 'entregado'));
    $ingresos   = array_sum(array_column($pedidos, 'total_pedido'));
    ?>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card metric-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon" style="background:#f3f4f6;">📋</div>
                    <div>
                        <div class="text-muted" style="font-size:12px;">Total pedidos</div>
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
                    <div class="metric-icon" style="background:#dbeafe;">🚚</div>
                    <div>
                        <div class="text-muted" style="font-size:12px;">Enviados</div>
                        <div class="fw-bold" style="font-size:26px;color:#2563eb;"><?= $enviados ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card metric-card p-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="metric-icon" style="background:#d1fae5;">💰</div>
                    <div>
                        <div class="text-muted" style="font-size:12px;">Ingresos totales</div>
                        <div class="fw-bold" style="font-size:20px;color:#059669;">$<?= number_format($ingresos, 0, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="card border-0 shadow-sm mb-3" style="border-radius:12px;">
        <div class="card-body py-3 px-4">
            <div class="d-flex gap-3 flex-wrap align-items-center">
                <div class="position-relative" style="flex:1;min-width:200px;">
                    <i class="bi bi-search position-absolute" style="left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;"></i>
                    <input type="text" id="buscar" class="form-control ps-4"
                           style="border-radius:10px;border:2px solid #e5e7eb;"
                           placeholder="Buscar por cliente, #pedido...">
                </div>
                <select id="filtroEstado" class="form-select" style="width:auto;border-radius:10px;border:2px solid #e5e7eb;">
                    <option value="">Todos los estados</option>
                    <option value="pendiente">⏳ Pendiente</option>
                    <option value="enviado">🚚 Enviado</option>
                    <option value="entregado">✅ Entregado</option>
                    <option value="cancelado">❌ Cancelado</option>
                </select>
            </div>
        </div>
    </div>

    <!-- TABLA -->
    <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">
    <div class="card-body p-0">
    <table class="table table-hover align-middle mb-0" id="tablaPedidos">
        <thead style="background:#1e293b;">
            <tr>
                <th style="color:#94a3b8;padding:14px 16px;">#</th>
                <th style="color:#94a3b8;">Cliente</th>
                <th style="color:#94a3b8;">Fecha</th>
                <th style="color:#94a3b8;">Total</th>
                <th style="color:#94a3b8;">Pago</th>
                <th style="color:#94a3b8;">Estado actual</th>
                <th style="color:#94a3b8;text-align:center;">Cambiar estado</th>
                <th style="color:#94a3b8;text-align:center;">Detalle</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($pedidos as $p): ?>
        <?php
        $estado = strtolower($p['estado_pedido']);
        $iconos = ['pendiente'=>'⏳','enviado'=>'🚚','entregado'=>'✅','cancelado'=>'❌'];
        $fecha  = $p['fecha_pedido'] ?? null;
        ?>
        <tr data-id="<?= $p['id_pedido'] ?>">
            <td style="padding:14px 16px;">
                <span style="font-weight:800;color:#7c3aed;">#<?= $p['id_pedido'] ?></span>
            </td>
            <td>
                <div style="font-weight:600;font-size:14px;"><?= htmlspecialchars($p['nombre_cliente'] ?? '—') ?></div>
            </td>
            <td style="font-size:13px;color:#6b7280;">
                <?= $fecha ? date('d/m/Y', strtotime($fecha)) : '—' ?>
            </td>
            <td>
                <span style="font-weight:700;color:#059669;font-size:15px;">
                    $<?= number_format($p['total_pedido'], 0, ',', '.') ?>
                </span>
            </td>
            <td>
                <?php if ($p['estado_pago']): ?>
                    <span class="badge" style="background:#d1fae5;color:#065f46;font-size:11px;font-weight:700;padding:4px 10px;border-radius:20px;">
                        ✓ <?= ucfirst($p['estado_pago']) ?>
                    </span>
                    <div style="font-size:11px;color:#9ca3af;margin-top:2px;"><?= htmlspecialchars($p['metodo_pago'] ?? '') ?></div>
                <?php else: ?>
                    <span class="badge bg-secondary" style="font-size:11px;">Sin pago</span>
                <?php endif; ?>
            </td>
            <td>
                <span class="estado-badge estado-<?= $estado ?>">
                    <?= $iconos[$estado] ?? '📦' ?> <?= ucfirst($p['estado_pedido']) ?>
                </span>
            </td>
            <td style="text-align:center;">
                <div class="d-flex gap-1 justify-content-center flex-wrap">
                    <?php
                    $todos = ['pendiente','enviado','entregado','cancelado'];
                    foreach ($todos as $e):
                        if ($e === $estado) continue;
                        $iconoBtn = $iconos[$e] ?? '';
                    ?>
                    <button class="btn-estado btn-estado-<?= $e ?>"
                            onclick="cambiarEstado(<?= $p['id_pedido'] ?>, '<?= $e ?>', '<?= ucfirst($e) ?>')">
                        <?= $iconoBtn ?> <?= ucfirst($e) ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </td>
            <td style="text-align:center;">
                <button class="btn-ver" id="btnver_<?= $p['id_pedido'] ?>"
                        onclick="verDetalle(<?= $p['id_pedido'] ?>)" title="Ver detalle">
                    <i class="bi bi-eye" style="font-size:15px;"></i>
                </button>
            </td>
        </tr>
        <!-- Fila detalle oculta -->
        <tr id="det_<?= $p['id_pedido'] ?>" class="fila-detalle" style="display:none;">
            <td colspan="8" style="padding:0 16px 16px;">
                <?php
                $det = $order->obtenerDetalle($p['id_pedido']);
                ?>
                <div class="detalle-inner mt-2">
                    <?php if (!empty($det)): ?>
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th style="text-align:center;">Cantidad</th>
                                <th style="text-align:right;">Precio unit.</th>
                                <th style="text-align:right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($det as $d): ?>
                        <tr>
                            <td style="font-weight:600;"><?= htmlspecialchars($d['nombre'] ?? '—') ?></td>
                            <td style="text-align:center;">
                                <span style="background:#f3f4f6;padding:3px 12px;border-radius:20px;font-weight:700;">
                                    <?= $d['cantidad'] ?>
                                </span>
                            </td>
                            <td style="text-align:right;color:#6b7280;">$<?= number_format($d['precio_unitario'], 0, ',', '.') ?></td>
                            <td style="text-align:right;font-weight:700;color:#059669;">$<?= number_format($d['subtotal'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr style="background:#f8fafc;">
                                <td colspan="3" style="text-align:right;font-weight:700;font-size:13px;color:#374151;padding:12px 14px;">Total del pedido:</td>
                                <td style="text-align:right;font-weight:900;font-size:16px;color:#7c3aed;padding:12px 14px;">
                                    $<?= number_format($p['total_pedido'], 0, ',', '.') ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <?php else: ?>
                        <div style="padding:20px;text-align:center;color:#9ca3af;">
                            <i class="bi bi-inbox" style="font-size:28px;"></i>
                            <p class="mt-2 mb-0">Sin detalle disponible</p>
                        </div>
                    <?php endif; ?>
                </div>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// ── Cambiar estado con SweetAlert2 ──
function cambiarEstado(id, estado, etiqueta) {
    const iconos = { pendiente: '⏳', enviado: '🚚', entregado: '✅', cancelado: '❌' };
    const colores = { pendiente: '#d97706', enviado: '#2563eb', entregado: '#059669', cancelado: '#dc2626' };

    Swal.fire({
        title: `¿Cambiar a <strong>${etiqueta}</strong>?`,
        html: `El pedido <strong>#${id}</strong> pasará al estado <span style="font-weight:800;color:${colores[estado]}">${iconos[estado]} ${etiqueta}</span>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: colores[estado] || '#7c3aed',
        cancelButtonColor: '#6b7280',
        confirmButtonText: `${iconos[estado]} Sí, cambiar`,
        cancelButtonText: 'Cancelar',
        borderRadius: '16px',
        customClass: { popup: 'swal-rounded' }
    }).then(result => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Actualizando...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
            window.location.href = `?cambiar_estado=1&id=${id}&estado=${estado}`;
        }
    });
}

// ── Ver / ocultar detalle ──
function verDetalle(id) {
    const fila = document.getElementById('det_' + id);
    const btn  = document.getElementById('btnver_' + id);
    const visible = fila.style.display !== 'none';

    // Cerrar todos los demás
    document.querySelectorAll('[id^="det_"]').forEach(f => f.style.display = 'none');
    document.querySelectorAll('[id^="btnver_"]').forEach(b => b.classList.remove('activo'));

    if (!visible) {
        fila.style.display = '';
        btn.classList.add('activo');
        // Scroll suave hacia el detalle
        fila.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

// ── Filtros ──
document.getElementById('buscar').addEventListener('input', filtrar);
document.getElementById('filtroEstado').addEventListener('change', filtrar);

function filtrar() {
    const txt    = document.getElementById('buscar').value.toLowerCase().trim();
    const estado = document.getElementById('filtroEstado').value.toLowerCase();

    document.querySelectorAll('#tablaPedidos tbody tr:not(.fila-detalle)').forEach(row => {
        const texto   = row.innerText.toLowerCase();
        const matchT  = txt    ? texto.includes(txt)    : true;
        const matchE  = estado ? texto.includes(estado) : true;
        const mostrar = matchT && matchE;

        row.style.display = mostrar ? '' : 'none';

        // Ocultar también la fila de detalle si la fila principal se oculta
        const id  = row.dataset.id;
        if (id) {
            const det = document.getElementById('det_' + id);
            if (det && !mostrar) det.style.display = 'none';
        }
    });
}

// ── Alerta de sesión ──
<?php if (!empty($_SESSION['alert'])): ?>
Swal.fire({
    icon:  '<?= $_SESSION['alert']['icon'] ?>',
    title: '<?= addslashes($_SESSION['alert']['title']) ?>',
    text:  '<?= addslashes($_SESSION['alert']['text']) ?>',
    timer: 2500,
    showConfirmButton: false,
    toast: true,
    position: 'top-end'
});
<?php unset($_SESSION['alert']); ?>
<?php endif; ?>
</script>
<style>
.swal-rounded { border-radius: 20px !important; }
</style>
</body>
</html>
