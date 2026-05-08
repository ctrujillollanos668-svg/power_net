<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 1) {
    header("Location: /power-net/public/index.php"); exit;
}
require_once __DIR__ . '/../../../models/Oferta.php';
require_once __DIR__ . '/../../../models/Product.php';

$ofertaModel = new Oferta();
$productModel = new Product();

$todasOfertas = $ofertaModel->obtenerTodas();
$productos    = $productModel->obtenerTodosAdmin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ofertas</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="d-flex">
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
<div class="flex-grow-1 p-4" style="margin-left:250px;margin-top:70px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">🏷️ Ofertas</h2>
        <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#modalNuevaOferta">
            + Nueva Oferta
        </button>
    </div>

    <!-- MÉTRICAS -->
    <?php
    $activas   = array_filter($todasOfertas, fn($o) => $o['estado'] == 1);
    $inactivas = array_filter($todasOfertas, fn($o) => $o['estado'] == 0);
    ?>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Total ofertas</div>
                    <div class="fw-bold fs-3"><?= count($todasOfertas) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Activas</div>
                    <div class="fw-bold fs-3 text-success"><?= count($activas) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Inactivas</div>
                    <div class="fw-bold fs-3 text-secondary"><?= count($inactivas) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- BUSCADOR -->
    <input type="text" id="buscarOferta" class="form-control mb-3 w-25" placeholder="Buscar oferta...">

    <!-- TABLA -->
    <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
    <table class="table table-hover align-middle mb-0" id="tablaOfertas">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Precio original</th>
                <th>Precio oferta</th>
                <th class="text-center">Descuento</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($todasOfertas as $o): ?>
        <?php
        $hoy    = date('Y-m-d');
        $vigente = $o['estado'] == 1 && $o['fecha_inicio'] <= $hoy && $o['fecha_fin'] >= $hoy;
        ?>
        <tr>
            <td class="fw-bold">#<?= $o['id_oferta'] ?></td>
            <td class="fw-semibold"><?= htmlspecialchars($o['nombre_producto']) ?></td>
            <td class="text-muted"><s>$<?= number_format($o['precio_original'], 0, ',', '.') ?></s></td>
            <td class="fw-bold text-danger">$<?= number_format($o['precio_oferta'], 0, ',', '.') ?></td>
            <td class="text-center">
                <span class="badge bg-danger">-<?= $o['descuento'] ?>%</span>
            </td>
            <td><?= date('d/m/Y', strtotime($o['fecha_inicio'])) ?></td>
            <td><?= date('d/m/Y', strtotime($o['fecha_fin'])) ?></td>
            <td class="text-center">
                <?php if ($vigente): ?>
                    <span class="badge bg-success">Activa</span>
                <?php elseif ($o['estado'] == 0): ?>
                    <span class="badge bg-secondary">Inactiva</span>
                <?php else: ?>
                    <span class="badge bg-warning text-dark">Vencida</span>
                <?php endif; ?>
            </td>
            <td class="text-center">
                <div class="d-flex gap-1 justify-content-center">
                    <!-- Editar -->
                    <button class="btn btn-sm btn-outline-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEditarOferta"
                            data-id="<?= $o['id_oferta'] ?>"
                            data-producto="<?= $o['id_producto'] ?>"
                            data-precio="<?= $o['precio_oferta'] ?>"
                            data-descuento="<?= $o['descuento'] ?>"
                            data-inicio="<?= $o['fecha_inicio'] ?>"
                            data-fin="<?= $o['fecha_fin'] ?>">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <!-- Desactivar / Activar -->
                    <?php if ($o['estado'] == 1): ?>
                    <a href="#"
                       class="btn btn-sm btn-outline-warning"
                       onclick="confirmarAccion('¿Desactivar esta oferta?', 'La oferta dejará de verse en la tienda.', '/power-net/public/index.php?action=desactivar_oferta&id=<?= $o['id_oferta'] ?>', 'warning')">
                        <i class="bi bi-pause-circle"></i>
                    </a>
                    <?php else: ?>
                    <a href="#"
                       class="btn btn-sm btn-outline-success"
                       onclick="confirmarAccion('¿Activar esta oferta?', 'La oferta será visible para los clientes.', '/power-net/public/index.php?action=activar_oferta&id=<?= $o['id_oferta'] ?>', 'success')">
                        <i class="bi bi-play-circle"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($todasOfertas)): ?>
        <tr><td colspan="9" class="text-center text-muted py-4">No hay ofertas registradas</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
    </div>

</div>
</div>

<!-- MODAL NUEVA OFERTA -->
<div class="modal fade" id="modalNuevaOferta" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
<form method="POST" action="/power-net/public/index.php?action=guardar_oferta">
<div class="modal-header bg-warning text-dark border-0">
    <h5 class="modal-title fw-bold">🏷️ Nueva Oferta</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body p-4">
    <div class="mb-3">
        <label class="form-label fw-semibold">Producto</label>
        <select name="id_producto" id="np_producto" class="form-select" required onchange="actualizarPrecioRef(this)">
            <option value="">Selecciona un producto</option>
            <?php foreach ($productos as $p): ?>
            <option value="<?= $p['id_producto'] ?>" data-precio="<?= $p['precio'] ?>">
                <?= htmlspecialchars($p['nombre']) ?> — $<?= number_format($p['precio'], 0, ',', '.') ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="alert alert-light border mb-3" id="np_ref" style="display:none;">
        Precio original: <strong id="np_precio_ref"></strong>
    </div>
    <div class="row g-3">
        <div class="col-6">
            <label class="form-label fw-semibold">Precio de oferta *</label>
            <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" name="precio_oferta" id="np_precio" class="form-control"
                       placeholder="0" min="1" oninput="calcDesc()" required>
            </div>
        </div>
        <div class="col-6">
            <label class="form-label fw-semibold">% Descuento</label>
            <div class="input-group">
                <input type="number" name="descuento" id="np_descuento" class="form-control"
                       placeholder="0" readonly>
                <span class="input-group-text">%</span>
            </div>
        </div>
        <div class="col-6">
            <label class="form-label fw-semibold">Fecha inicio *</label>
            <input type="date" name="fecha_inicio" class="form-control"
                   value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="col-6">
            <label class="form-label fw-semibold">Fecha fin *</label>
            <input type="date" name="fecha_fin" class="form-control"
                   value="<?= date('Y-m-d', strtotime('+30 days')) ?>" required>
        </div>
    </div>
</div>
<div class="modal-footer border-0 bg-light">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button type="submit" class="btn btn-warning fw-bold px-4">🏷️ Crear oferta</button>
</div>
</form>
</div>
</div>
</div>

<!-- MODAL EDITAR OFERTA -->
<div class="modal fade" id="modalEditarOferta" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
<form method="POST" action="/power-net/public/index.php?action=editar_oferta">
<div class="modal-header bg-primary text-white border-0">
    <h5 class="modal-title fw-bold">✏️ Editar Oferta</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body p-4">
    <input type="hidden" name="id_oferta" id="eo_id">
    <div class="row g-3">
        <div class="col-6">
            <label class="form-label fw-semibold">Precio de oferta *</label>
            <div class="input-group">
                <span class="input-group-text">$</span>
                <input type="number" name="precio_oferta" id="eo_precio" class="form-control" required>
            </div>
        </div>
        <div class="col-6">
            <label class="form-label fw-semibold">% Descuento</label>
            <div class="input-group">
                <input type="number" name="descuento" id="eo_descuento" class="form-control">
                <span class="input-group-text">%</span>
            </div>
        </div>
        <div class="col-6">
            <label class="form-label fw-semibold">Fecha inicio *</label>
            <input type="date" name="fecha_inicio" id="eo_inicio" class="form-control" required>
        </div>
        <div class="col-6">
            <label class="form-label fw-semibold">Fecha fin *</label>
            <input type="date" name="fecha_fin" id="eo_fin" class="form-control" required>
        </div>
    </div>
</div>
<div class="modal-footer border-0 bg-light">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button type="submit" class="btn btn-primary fw-bold px-4">💾 Guardar cambios</button>
</div>
</form>
</div>
</div>
</div>

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
// Buscador
document.getElementById('buscarOferta').addEventListener('keyup', function() {
    const f = this.value.toLowerCase();
    document.querySelectorAll('#tablaOfertas tbody tr').forEach(r => {
        r.style.display = r.innerText.toLowerCase().includes(f) ? '' : 'none';
    });
});

// Confirmación con SweetAlert
function confirmarAccion(titulo, texto, url, icono) {
    Swal.fire({
        title: titulo,
        text: texto,
        icon: icono,
        showCancelButton: true,
        confirmButtonColor: icono === 'warning' ? '#f59e0b' : '#10b981',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) window.location.href = url;
    });
}

// Mostrar precio de referencia al seleccionar producto
function actualizarPrecioRef(sel) {
    const opt   = sel.options[sel.selectedIndex];
    const precio = opt.dataset.precio;
    const ref   = document.getElementById('np_ref');
    if (precio) {
        document.getElementById('np_precio_ref').textContent =
            '$' + parseFloat(precio).toLocaleString('es-CO');
        ref.style.display = 'block';
        document.getElementById('np_precio').dataset.original = precio;
    } else {
        ref.style.display = 'none';
    }
}

// Calcular descuento automáticamente
function calcDesc() {
    const precio   = parseFloat(document.getElementById('np_precio').value);
    const original = parseFloat(document.getElementById('np_precio').dataset.original || 0);
    if (precio && original && precio < original) {
        document.getElementById('np_descuento').value = Math.round((1 - precio / original) * 100);
    } else {
        document.getElementById('np_descuento').value = 0;
    }
}

// Rellenar modal editar
document.getElementById('modalEditarOferta').addEventListener('show.bs.modal', function(e) {
    const b = e.relatedTarget;
    document.getElementById('eo_id').value       = b.dataset.id;
    document.getElementById('eo_precio').value   = b.dataset.precio;
    document.getElementById('eo_descuento').value= b.dataset.descuento;
    document.getElementById('eo_inicio').value   = b.dataset.inicio;
    document.getElementById('eo_fin').value      = b.dataset.fin;
});
</script>
</body>
</html>
