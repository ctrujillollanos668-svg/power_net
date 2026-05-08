<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 1) {
    header("Location: /power-net/public/index.php"); exit;
}
require_once __DIR__ . '/../../../models/Proveedor.php';
$provModel   = new Proveedor();
$proveedores = $provModel->obtenerTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Proveedores</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="d-flex">
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
<div class="flex-grow-1 p-4" style="margin-left:250px;margin-top:70px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">🏭 Proveedores</h2>
        <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modalNuevo">
            + Nuevo Proveedor
        </button>
    </div>

    <!-- BUSCADOR -->
    <input type="text" id="buscarProv" class="form-control mb-3 w-25" placeholder="Buscar proveedor...">

    <!-- TABLA -->
    <div class="card border-0 shadow-sm">
    <div class="card-body p-0">
    <table class="table table-hover align-middle mb-0" id="tablaProv">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Nombre</th>
                <th>Correo</th>
                <th>Teléfono</th>
                <th class="text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($proveedores as $p): ?>
        <tr>
            <td class="fw-bold">#<?= $p['id_proveedor'] ?></td>
            <td class="fw-semibold"><?= htmlspecialchars($p['nombre_proveedor']) ?></td>
            <td><?= htmlspecialchars($p['correo'] ?? '—') ?></td>
            <td><?= htmlspecialchars($p['telefono'] ?? '—') ?></td>
            <td class="text-center">
                <div class="d-flex gap-2 justify-content-center">
                    <button class="btn btn-sm btn-outline-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#modalEditar"
                            data-id="<?= $p['id_proveedor'] ?>"
                            data-nombre="<?= htmlspecialchars($p['nombre_proveedor'], ENT_QUOTES) ?>"
                            data-correo="<?= htmlspecialchars($p['correo'] ?? '', ENT_QUOTES) ?>"
                            data-telefono="<?= htmlspecialchars($p['telefono'] ?? '', ENT_QUOTES) ?>">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <a href="#"
                       class="btn btn-sm btn-outline-danger"
                       onclick="confirmarEliminar('/power-net/public/index.php?action=eliminar_proveedor&id=<?= $p['id_proveedor'] ?>')">
                        <i class="bi bi-trash"></i>
                    </a>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($proveedores)): ?>
        <tr><td colspan="5" class="text-center text-muted py-4">No hay proveedores registrados</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
    </div>

</div>
</div>

<!-- MODAL NUEVO -->
<div class="modal fade" id="modalNuevo" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
<form method="POST" action="/power-net/public/index.php?action=guardar_proveedor">
<div class="modal-header bg-dark text-white border-0">
    <h5 class="modal-title fw-bold">🏭 Nuevo Proveedor</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body p-4">
    <div class="mb-3">
        <label class="form-label fw-semibold">Nombre *</label>
        <input type="text" name="nombre" class="form-control" placeholder="Nombre del proveedor" required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Correo</label>
        <input type="email" name="correo" class="form-control" placeholder="correo@proveedor.com">
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Teléfono</label>
        <input type="text" name="telefono" class="form-control" placeholder="300 000 0000">
    </div>
</div>
<div class="modal-footer border-0 bg-light">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button type="submit" class="btn btn-dark fw-bold px-4">💾 Guardar</button>
</div>
</form>
</div>
</div>
</div>

<!-- MODAL EDITAR -->
<div class="modal fade" id="modalEditar" tabindex="-1">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
<form method="POST" action="/power-net/public/index.php?action=editar_proveedor">
<div class="modal-header bg-dark text-white border-0">
    <h5 class="modal-title fw-bold">✏️ Editar Proveedor</h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body p-4">
    <input type="hidden" name="id" id="ep_id">
    <div class="mb-3">
        <label class="form-label fw-semibold">Nombre *</label>
        <input type="text" name="nombre" id="ep_nombre" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Correo</label>
        <input type="email" name="correo" id="ep_correo" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Teléfono</label>
        <input type="text" name="telefono" id="ep_telefono" class="form-control">
    </div>
</div>
<div class="modal-footer border-0 bg-light">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button type="submit" class="btn btn-dark fw-bold px-4">💾 Guardar cambios</button>
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
document.getElementById('buscarProv').addEventListener('keyup', function() {
    const f = this.value.toLowerCase();
    document.querySelectorAll('#tablaProv tbody tr').forEach(r => {
        r.style.display = r.innerText.toLowerCase().includes(f) ? '' : 'none';
    });
});

function confirmarEliminar(url) {
    Swal.fire({
        title: '¿Eliminar proveedor?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (result.isConfirmed) window.location.href = url;
    });
}
document.getElementById('modalEditar').addEventListener('show.bs.modal', function(e) {
    const b = e.relatedTarget;
    document.getElementById('ep_id').value       = b.dataset.id;
    document.getElementById('ep_nombre').value   = b.dataset.nombre;
    document.getElementById('ep_correo').value   = b.dataset.correo;
    document.getElementById('ep_telefono').value = b.dataset.telefono;
});
</script>
</body>
</html>
