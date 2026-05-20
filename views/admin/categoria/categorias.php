<?php
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 1) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/../../../models/Category.php';

$category = new Category();
$categorias = $category->obtenerTodas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Categorías</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
.content-admin {
    margin-left: 250px;
    margin-top: 70px;
}
.card-custom {
    border-radius: 16px;
    border: none;
    box-shadow: 0 6px 18px rgba(0,0,0,0.05);
}
.btn-black {
    background: #000;
    color: #fff;
    border-radius: 10px;
}
.badge-id {
    background: #e2e8f0;
    color: #475569;
    font-size: 11px;
}
</style>
</head>

<body>

<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="d-flex">
<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<div class="flex-grow-1 p-4 content-admin">

<h2 class="mb-4 fw-bold">📂 Gestión de Categorías</h2>

<button class="btn btn-black mb-3" data-bs-toggle="modal" data-bs-target="#modalCategoria">
    + Nueva Categoría
</button>

<div class="card card-custom">
<div class="card-body">
<input type="text" id="buscarCategoria" class="form-control mb-3" placeholder="Buscar categoría...">
<table class="table table-hover align-middle">

<thead>
<tr>
    <th>ID</th>
    <th>Nombre</th>
    <th>Descripción</th>
    <th>Estado</th>
    <th class="text-center">Acciones</th>
</tr>
</thead>

<tbody>

<?php foreach ($categorias as $c): ?>
<tr>

<td>
    <span class="badge badge-id">
        #<?= $c['id_categoria'] ?>
    </span>
</td>

<td class="fw-semibold">
    <?= $c['nombre_categoria'] ?>
</td>

<td class="text-muted">
    <?= $c['descripcion'] ?>
</td>

<td>
    <?php if ($c['estado'] == 1): ?>
        <span class="badge bg-success">Activo</span>
    <?php else: ?>
        <span class="badge bg-secondary">Inactivo</span>
    <?php endif; ?>
</td>

<td class="text-center">

<div class="d-flex justify-content-center gap-2">

    <!-- SWITCH -->
    <form method="GET" action="index.php">
        <input type="hidden" name="action" value="toggle_categoria">
        <input type="hidden" name="id" value="<?= $c['id_categoria'] ?>">

        <div class="form-check form-switch">
            <input class="form-check-input"
                   type="checkbox"
                   onchange="this.form.submit()"
                   <?= ($c['estado'] == 1) ? 'checked' : '' ?>>
        </div>
    </form>

    <!-- EDITAR -->
    <button class="btn btn-sm btn-outline-dark"
        data-bs-toggle="modal"
        data-bs-target="#modalEditar"
        data-id="<?= $c['id_categoria'] ?>"
        data-nombre="<?= htmlspecialchars($c['nombre_categoria']) ?>"
        data-descripcion="<?= htmlspecialchars($c['descripcion']) ?>"
    >
        <i class="bi bi-pencil"></i>
    </button>

    <!-- ELIMINAR -->
    <button type="button" class="btn btn-sm btn-outline-danger"
        onclick="confirmarEliminar('index.php?action=eliminar_categoria&id=<?= $c['id_categoria'] ?>')">
        <i class="bi bi-trash"></i>
    </button>

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

<!-- MODAL CREAR -->
<div class="modal fade" id="modalCategoria">
<div class="modal-dialog">
<div class="modal-content">

<form method="POST" action="index.php?action=guardar_categoria">

<div class="modal-header">
    <h5>Nueva Categoría</h5>
    <button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <div class="mb-3">
        <label class="form-label fw-semibold">Nombre de la categoría</label>
        <input type="text" name="nombre_categoria" class="form-control mb-3" 
               placeholder="Ej: Electrónica, Redes, Cables..." required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Descripción</label>
        <textarea name="descripcion" class="form-control" rows="3"
                  placeholder="Describe brevemente esta categoría..."></textarea>
    </div>
</div>

<div class="modal-footer">
    <button class="btn btn-black w-100">Guardar</button>
</div>

</form>

</div>
</div>
</div>

<!-- MODAL EDITAR -->
<div class="modal fade" id="modalEditar">
<div class="modal-dialog">
<div class="modal-content">

<form method="POST" action="index.php?action=editar_categoria">

<input type="hidden" name="id" id="edit_id">

<div class="modal-header">
    <h5>Editar Categoría</h5>
    <button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
    <div class="mb-3">
        <label class="form-label fw-semibold">Nombre de la categoría</label>
        <input type="text" name="nombre_categoria" id="edit_nombre" class="form-control" 
               placeholder="Nombre de la categoría" required>
    </div>
    <div class="mb-3">
        <label class="form-label fw-semibold">Descripción</label>
        <textarea name="descripcion" id="edit_descripcion" class="form-control" rows="3"
                  placeholder="Descripción de la categoría..."></textarea>
    </div>
</div>

<div class="modal-footer">
    <button class="btn btn-black w-100">Guardar</button>
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
        icon:  '<?= $_SESSION['alert']['icon'] ?>',
        title: '<?= addslashes($_SESSION['alert']['title']) ?>',
        text:  '<?= addslashes($_SESSION['alert']['text']) ?>'
    });
</script>
<?php unset($_SESSION['alert']); ?>
<?php endif; ?>

<script>
document.getElementById('modalEditar').addEventListener('show.bs.modal', function (e) {
    let b = e.relatedTarget;

    edit_id.value = b.dataset.id;
    edit_nombre.value = b.dataset.nombre;
    edit_descripcion.value = b.dataset.descripcion;
});
</script>
<script>
document.getElementById("buscarCategoria").addEventListener("keyup", function() {
    let filtro = this.value.toLowerCase();
    let filas = document.querySelectorAll("tbody tr");

    filas.forEach(fila => {
        let texto = fila.innerText.toLowerCase();
        fila.style.display = texto.includes(filtro) ? "" : "none";
    });
});

function confirmarEliminar(url) {
    Swal.fire({
        title: '¿Eliminar categoría?',
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
</script>
</body>
</html>