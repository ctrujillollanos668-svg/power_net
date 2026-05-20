<?php
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 1) {
    header("Location: index.php"); exit;
}
require_once __DIR__ . '/../../../config/Database.php';
require_once __DIR__ . '/../../../models/Envio.php';

$db  = (new Database())->getConnection();
$env = new Envio();

// Traer todos los envíos con datos del pedido y cliente
$stmt = $db->query(
    "SELECT e.*, p.total_pedido, p.estado_pedido,
            pe.nombre_persona AS nombre_cliente
     FROM envio e
     INNER JOIN pedido  p   ON e.id_pedido  = p.id_pedido
     INNER JOIN cliente c   ON p.id_cliente = c.id_cliente
     INNER JOIN persona pe  ON c.id_persona = pe.id_persona
     ORDER BY e.id_envio DESC"
);
$envios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pedidos sin envío (para crear nuevo)
$stmtPed = $db->query(
    "SELECT p.id_pedido, p.total_pedido, pe.nombre_persona AS nombre_cliente
     FROM pedido p
     INNER JOIN cliente c  ON p.id_cliente = c.id_cliente
     INNER JOIN persona pe ON c.id_persona = pe.id_persona
     WHERE p.estado_pedido IN ('enviado','entregado')
       AND p.id_pedido NOT IN (SELECT id_pedido FROM envio)
     ORDER BY p.id_pedido DESC"
);
$pedidosSinEnvio = $stmtPed->fetchAll(PDO::FETCH_ASSOC);

$total     = count($envios);
$enCamino  = count(array_filter($envios, fn($e) => $e['estado'] === 'en_camino'));
$entregados= count(array_filter($envios, fn($e) => $e['estado'] === 'entregado'));
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><title>Envíos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="d-flex">
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
<div class="flex-grow-1 p-4" style="margin-left:250px;margin-top:70px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">🚚 Envíos</h2>
        <?php if (!empty($pedidosSinEnvio)): ?>
        <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modalNuevoEnvio">
            + Registrar envío
        </button>
        <?php endif; ?>
    </div>

    <!-- MÉTRICAS -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body"><div class="text-muted small">Total envíos</div>
                <div class="fw-bold fs-3"><?= $total ?></div></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body"><div class="text-muted small">En camino</div>
                <div class="fw-bold fs-3 text-primary"><?= $enCamino ?></div></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body"><div class="text-muted small">Entregados</div>
                <div class="fw-bold fs-3 text-success"><?= $entregados ?></div></div>
            </div>
        </div>
    </div>

    <!-- BUSCADOR -->
    <input type="text" id="buscarEnvio" class="form-control mb-3 w-25" placeholder="Buscar envío...">

    <!-- TABLA -->
    <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
    <table class="table table-hover align-middle mb-0" id="tablaEnvios">
        <thead class="table-dark">
            <tr>
                <th>#Envío</th><th>#Pedido</th><th>Cliente</th>
                <th>Empresa</th><th>Dirección</th><th>Fecha</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($envios as $e): ?>
        <?php
        $badge = match($e['estado']) {
            'en_camino'  => 'bg-primary',
            'entregado'  => 'bg-success',
            'devuelto'   => 'bg-warning text-dark',
            default      => 'bg-secondary'
        };
        $label = match($e['estado']) {
            'en_camino'  => '🚚 En camino',
            'entregado'  => '✅ Entregado',
            'devuelto'   => '↩️ Devuelto',
            default      => ucfirst($e['estado'])
        };
        ?>
        <tr>
            <td class="fw-bold">#<?= $e['id_envio'] ?></td>
            <td>#<?= $e['id_pedido'] ?></td>
            <td><?= htmlspecialchars($e['nombre_cliente'] ?? '—') ?></td>
            <td><?= htmlspecialchars($e['empresa_envios'] ?? '—') ?></td>
            <td class="small"><?= htmlspecialchars($e['direccion_envio'] ?? '—') ?></td>
            <td class="small"><?= $e['fecha_hora'] ? date('d/m/Y H:i', strtotime($e['fecha_hora'])) : '—' ?></td>
            <td class="text-center"><span class="badge <?= $badge ?>"><?= $label ?></span></td>
            <td class="text-center">
                <div class="d-flex gap-1 justify-content-center">
                    <!-- Ver detalle -->
                    <button class="btn btn-sm btn-outline-dark" onclick="verDetEnvio(<?= $e['id_envio'] ?>)" title="Ver detalle">
                        <i class="bi bi-eye"></i>
                    </button>
                    <!-- Cambiar estado -->
                    <button class="btn btn-sm btn-outline-primary"
                            data-bs-toggle="modal" data-bs-target="#modalEstadoEnvio"
                            data-id="<?= $e['id_envio'] ?>" data-estado="<?= $e['estado'] ?>"
                            title="Actualizar estado">
                        <i class="bi bi-arrow-repeat"></i>
                    </button>
                    <!-- Eliminar -->
                    <button class="btn btn-sm btn-outline-danger"
                            onclick="eliminarEnvio(<?= $e['id_envio'] ?>)" title="Eliminar">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
        <!-- Detalle oculto -->
        <tr id="envdet_<?= $e['id_envio'] ?>" style="display:none;">
            <td colspan="8" class="bg-light p-3">
                <div class="row g-3">
                    <div class="col-md-3"><strong>Empresa:</strong><br><?= htmlspecialchars($e['empresa_envios'] ?? '—') ?></div>
                    <div class="col-md-3"><strong>Dirección:</strong><br><?= htmlspecialchars($e['direccion_envio'] ?? '—') ?></div>
                    <div class="col-md-3"><strong>Costo:</strong><br>$<?= number_format($e['costo'] ?? 0, 0, ',', '.') ?></div>
                    <div class="col-md-3"><strong>Fecha despacho:</strong><br><?= $e['fecha_hora'] ? date('d/m/Y H:i', strtotime($e['fecha_hora'])) : '—' ?></div>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($envios)): ?>
        <tr><td colspan="8" class="text-center text-muted py-4">No hay envíos registrados</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
    </div>

</div>
</div>

<!-- MODAL NUEVO ENVÍO -->
<div class="modal fade" id="modalNuevoEnvio" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
<form method="POST" action="index.php?action=guardar_envio">
<div class="modal-header bg-dark text-white border-0">
    <h5 class="modal-title fw-bold">🚚 Registrar Envío</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body p-4">
    <div class="mb-3">
        <label class="form-label fw-semibold">Pedido *</label>
        <select name="id_pedido" class="form-select" required>
            <option value="">Selecciona un pedido</option>
            <?php foreach ($pedidosSinEnvio as $ped): ?>
            <option value="<?= $ped['id_pedido'] ?>">
                #<?= $ped['id_pedido'] ?> — <?= htmlspecialchars($ped['nombre_cliente']) ?> — $<?= number_format($ped['total_pedido'], 0, ',', '.') ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Empresa de envíos *</label>
        <input type="text" name="empresa" class="form-control" placeholder="Ej: Servientrega, Coordinadora..." required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Dirección de entrega *</label>
        <input type="text" name="direccion" class="form-control" placeholder="Dirección del cliente" required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Costo de envío</label>
        <div class="input-group">
            <span class="input-group-text">$</span>
            <input type="number" name="costo" class="form-control" value="0" min="0">
        </div>
    </div>
</div>
<div class="modal-footer border-0 bg-light">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button type="submit" class="btn btn-dark fw-bold px-4">🚚 Registrar</button>
</div>
</form>
</div>
</div>
</div>

<!-- MODAL ACTUALIZAR ESTADO -->
<div class="modal fade" id="modalEstadoEnvio" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
<form method="POST" action="index.php?action=actualizar_estado_envio">
<div class="modal-header bg-dark text-white border-0">
    <h5 class="modal-title fw-bold">🔄 Actualizar estado del envío</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body p-4">
    <input type="hidden" name="id_envio" id="ee_id">
    <label class="form-label fw-semibold">Nuevo estado</label>
    <select name="estado" id="ee_estado" class="form-select" required>
        <option value="en_camino">🚚 En camino</option>
        <option value="entregado">✅ Entregado</option>
        <option value="devuelto">↩️ Devuelto</option>
    </select>
</div>
<div class="modal-footer border-0 bg-light">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button type="submit" class="btn btn-dark fw-bold px-4">💾 Guardar</button>
</div>
</form>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (isset($_SESSION['alert'])): ?>
<script>Swal.fire({icon:'<?= $_SESSION['alert']['icon'] ?>',title:'<?= addslashes($_SESSION['alert']['title']) ?>',text:'<?= addslashes($_SESSION['alert']['text']) ?>'});</script>
<?php unset($_SESSION['alert']); ?>
<?php endif; ?>
<script>
document.getElementById('buscarEnvio').addEventListener('keyup', function() {
    const f = this.value.toLowerCase();
    document.querySelectorAll('#tablaEnvios tbody tr:not([id^="envdet_"])').forEach(r => {
        r.style.display = r.innerText.toLowerCase().includes(f) ? '' : 'none';
    });
});
function verDetEnvio(id) {
    const r = document.getElementById('envdet_' + id);
    r.style.display = r.style.display === 'none' ? '' : 'none';
}
document.getElementById('modalEstadoEnvio').addEventListener('show.bs.modal', function(e) {
    const b = e.relatedTarget;
    document.getElementById('ee_id').value     = b.dataset.id;
    document.getElementById('ee_estado').value = b.dataset.estado;
});
function eliminarEnvio(id) {
    Swal.fire({title:'¿Eliminar envío?',text:'Esta acción no se puede deshacer.',icon:'warning',
        showCancelButton:true,confirmButtonColor:'#dc2626',cancelButtonColor:'#6b7280',
        confirmButtonText:'Sí, eliminar',cancelButtonText:'Cancelar'
    }).then(r => { if (r.isConfirmed) window.location.href='index.php?action=eliminar_envio&id='+id; });
}
</script>
</body>
</html>
