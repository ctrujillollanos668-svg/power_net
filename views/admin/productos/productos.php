<?php
session_start();

// 🔐 VALIDAR ADMIN
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 1) {
    header("Location: /power-net/public/index.php");
    exit;
}

// 📦 PRODUCTOS
require_once __DIR__ . '/../../../models/Product.php';
$product = new Product();
$productos = $product->obtenerTodosAdmin();

// 📂 CATEGORÍAS
require_once __DIR__ . '/../../../models/Category.php';
$category = new Category();
$categorias = $category->obtenerTodas();
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Productos</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="d-flex">

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<div class="flex-grow-1 p-4" style="margin-left:250px; margin-top:70px;">

<h2 class="mb-4">Gestión de Productos</h2>

<!-- BOTONES + BUSCADOR -->
<div class="d-flex gap-2 mb-3 align-items-center justify-content-between">
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalProducto">
        + Añadir Producto
    </button>
    <input type="text" id="buscarProducto"
           class="form-control w-25"
           placeholder="🔍 Buscar producto...">
</div>

<!-- TABLA -->
<table class="table table-hover align-middle" id="tablaProductos">

<thead>
<tr>
    <th>Imagen</th>
    <th>Nombre</th>
    <th>Precio</th>
    <th>Stock</th>
    <th>Categoría</th>
    <th>Estado</th>
    <th>Acciones</th>
</tr>
</thead>

<tbody>

<?php foreach ($productos as $p): ?>

<?php
// 🖼 Obtener primera imagen del producto
$imagenes = $product->obtenerImagenes($p['id_producto']);
$imgPrincipal = !empty($imagenes) ? $imagenes[0]['imagen'] : '';
?>

<tr>

<td>
    <!-- 🖼 Mostrar todas las imágenes del producto -->
    <div class="d-flex gap-1 flex-wrap">

        <?php if (!empty($imagenes)): ?>

           <?php foreach ($imagenes as $img): ?>
    
    <div style="position:relative;">

        <img src="/power-net/public/uploads/<?= $img['imagen'] ?>"
             style="width:40px; height:40px; object-fit:cover;"
             class="rounded border">

        <!-- ❌ BOTÓN ELIMINAR -->
        <a href="/power-net/public/index.php?action=eliminar_imagen&id_imagen=<?= $img['id'] ?>&id_producto=<?= $p['id_producto'] ?>"
   style="
        position:absolute;
        top:-6px;
        right:-6px;
        background:red;
        color:white;
        width:18px;
        height:18px;
        font-size:12px;
        border-radius:50%;
        text-align:center;
        line-height:18px;
        text-decoration:none;
   ">
   ×
</a>

    </div>

<?php endforeach; ?>

        <?php else: ?>

            <span class="text-muted">Sin imagen</span>

        <?php endif; ?>

    </div>
</td>

<td><?= $p['nombre'] ?></td>
<td>$<?= number_format($p['precio'],0,',','.') ?></td>
<td><?= $p['stock'] ?></td>
<td><?= $p['nombre_categoria'] ?? 'Sin categoría' ?></td>

<td>
    <?php if ($p['disponibilidad'] == 1): ?>
        <span class="badge bg-success">Activo</span>
    <?php else: ?>
        <span class="badge bg-secondary">Inactivo</span>
    <?php endif; ?>
</td>

<td>
<div class="d-flex gap-2 align-items-center">

    <!-- SWITCH ACTIVAR / DESACTIVAR -->
    <form method="GET" action="/power-net/public/index.php" class="m-0">
        <input type="hidden" name="action" value="toggle_producto">
        <input type="hidden" name="id" value="<?= $p['id_producto'] ?>">

        <div class="form-check form-switch d-flex justify-content-center">
            <input 
                class="form-check-input" 
                type="checkbox" 
                role="switch"
                style="cursor: pointer; width: 3em; height: 1.5em;" 
                onchange="this.form.submit()"
                <?= ($p['disponibilidad'] == 1) ? 'checked' : '' ?>
            >
        </div>
    </form>

    <!-- EDITAR -->
    <button class="btn btn-sm btn-outline-primary"
        data-bs-toggle="modal"
        data-bs-target="#modalEditar"
        data-id="<?= $p['id_producto'] ?>"
        data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
        data-descripcion="<?= htmlspecialchars($p['descripcion']) ?>"
        data-precio="<?= $p['precio'] ?>"
        data-stock="<?= $p['stock'] ?>"
        data-categoria="<?= $p['id_categoria'] ?>"
        data-imagen="<?= $imgPrincipal ?>"
    >
        <i class="bi bi-pencil"></i>
    </button>

    <!-- ELIMINAR -->
    <button class="btn btn-sm btn-outline-danger"
        data-bs-toggle="modal"
        data-bs-target="#modalEliminar"
        data-id="<?= $p['id_producto'] ?>"
        data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
        data-imagen="<?= $imgPrincipal ?>"
    >
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

<!-- ================= MODAL CREAR PRODUCTO ================= -->
<div class="modal fade" id="modalProducto">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

<form method="POST" enctype="multipart/form-data"
action="/power-net/public/index.php?action=guardar_producto">

<!-- HEADER -->
<div class="modal-header bg-dark text-white">
    <h5 class="mb-0">🛒 Nuevo Producto</h5>
    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>

<!-- BODY -->
<div class="modal-body p-4">

    <input type="text" name="nombre" class="form-control mb-3 rounded-3" placeholder="Nombre" required>

    <textarea name="descripcion" class="form-control mb-3 rounded-3" placeholder="Descripción"></textarea>

    <input type="number" name="precio" class="form-control mb-3 rounded-3" placeholder="Precio" required>

    <input type="number" name="stock" class="form-control mb-3 rounded-3" placeholder="Stock" required>

    <select name="categoria" class="form-select mb-3 rounded-3">
        <?php foreach ($categorias as $c): ?>
            <option value="<?= $c['id_categoria'] ?>">
                <?= $c['nombre_categoria'] ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- 🖼 IMÁGENES MÚLTIPLES -->
    <div class="border rounded-3 p-3 text-center bg-light">
        <i class="bi bi-cloud-arrow-up fs-2 text-secondary"></i>
        <p class="mb-2 text-muted">Sube imágenes del producto</p>
        <input type="file" name="imagenes[]" class="form-control" multiple>
    </div>

</div>

<!-- FOOTER -->
<div class="modal-footer bg-light">
    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button class="btn btn-success px-4">Guardar</button>
</div>

</form>

</div>
</div>
</div>

<!-- ================= MODAL EDITAR ================= -->
<div class="modal fade" id="modalEditar">
<div class="modal-dialog modal-xl">
<div class="modal-content">

<form method="POST" enctype="multipart/form-data"
action="/power-net/public/index.php?action=editar_producto">

<div class="modal-header">
    <h5 class="fw-bold">Editar Producto</h5>
    <button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

<div class="row">

<!-- 🔹 IZQUIERDA -->
<div class="col-md-6">

    <input type="hidden" name="id" id="edit_id">

    <label class="fw-semibold">Nombre del Producto</label>
    <input type="text" name="nombre" id="edit_nombre" class="form-control mb-3">

    <label class="fw-semibold">Descripción</label>
    <textarea name="descripcion" id="edit_descripcion" class="form-control mb-3" rows="4"></textarea>

    <label class="fw-semibold">Categoría</label>
    <select name="categoria" id="edit_categoria" class="form-control mb-3">
        <?php foreach ($categorias as $c): ?>
            <option value="<?= $c['id_categoria'] ?>"><?= $c['nombre_categoria'] ?></option>
        <?php endforeach; ?>
    </select>

    <div class="row">
        <div class="col">
            <label>Precio</label>
            <input type="number" name="precio" id="edit_precio" class="form-control">
        </div>

        <div class="col">
            <label>Stock</label>
            <input type="number" name="stock" id="edit_stock" class="form-control">
        </div>
    </div>

</div>

<!-- 🔹 DERECHA -->
<div class="col-md-6">

    <label class="fw-semibold">Imágenes del Producto</label>

    <!-- PREVIEW -->
    <div class="d-flex gap-2 mb-3">
        <img id="preview_imagen" width="90" class="rounded border">
    </div>

    <!-- DROP ZONE -->
    <div class="border rounded p-4 text-center mb-3" style="border-style:dashed;">
        <i class="bi bi-cloud-arrow-up fs-1 text-muted"></i>
        <p class="text-muted mb-1">Puedes agregar más imágenes</p>

        <!-- 🖼 NUEVAS IMÁGENES -->
        <input type="file" name="imagenes[]" id="edit_imagenes" class="form-control mt-2" multiple>
    </div>
</div>

</div>

</div>

<div class="modal-footer">
    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
    <button class="btn btn-success">Guardar Cambios</button>
</div>

</form>

</div>
</div>
</div>

<!-- ================= MODAL ELIMINAR ================= -->
<div class="modal fade" id="modalEliminar">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content rounded-4 shadow">

<form method="GET" action="/power-net/public/index.php">

<input type="hidden" name="action" value="eliminar_producto">
<input type="hidden" name="id" id="delete_id">

<div class="modal-header border-0 pb-0">
    <h5 class="fw-bold">Confirmar Eliminación</h5>
    <button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body text-center">

    <p class="text-muted mb-3">
        ¿Está seguro de que desea eliminar permanentemente este producto?
    </p>

    <!-- 🔥 TARJETA PRODUCTO -->
    <div class="d-flex align-items-center gap-3 border rounded p-3 mb-3">

        <img id="delete_imagen" width="60" class="rounded border">

        <div class="text-start">
            <strong id="delete_nombre"></strong><br>
            <small class="text-muted">ID: <span id="delete_codigo"></span></small>
        </div>

    </div>

</div>

<div class="modal-footer border-0">
    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
        Cancelar
    </button>

    <button class="btn btn-danger px-4">
        Sí, Eliminar
    </button>
</div>

</form>

</div>
</div>
</div>
<!-- ================= SCRIPT ================= -->
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
// Buscador de productos en tiempo real
document.getElementById('buscarProducto').addEventListener('keyup', function() {
    const f = this.value.toLowerCase();
    document.querySelectorAll('#tablaProductos tbody tr').forEach(r => {
        r.style.display = r.innerText.toLowerCase().includes(f) ? '' : 'none';
    });
});
// EDITAR
document.getElementById('modalEditar').addEventListener('show.bs.modal', function (e) {

    let b = e.relatedTarget;

    edit_id.value = b.dataset.id;
    edit_nombre.value = b.dataset.nombre;
    edit_descripcion.value = b.dataset.descripcion;
    edit_precio.value = b.dataset.precio;
    edit_stock.value = b.dataset.stock;
    edit_categoria.value = b.dataset.categoria;

    let imagen = b.dataset.imagen;

    if (imagen && imagen !== "") {
        preview_imagen.src = "/power-net/public/uploads/" + imagen;
    } else {
        preview_imagen.src = "https://via.placeholder.com/120";
    }
});

// PREVIEW NUEVA IMAGEN EN EDITAR
document.getElementById('edit_imagenes').addEventListener('change', function(e) {

    let file = e.target.files[0];

    if (file) {
        let reader = new FileReader();

        reader.onload = function(e) {
            preview_imagen.src = e.target.result;
        }

        reader.readAsDataURL(file);
    }

});

// ELIMINAR
document.getElementById('modalEliminar').addEventListener('show.bs.modal', function (e) {

    let b = e.relatedTarget;

    delete_id.value = b.dataset.id;
    delete_nombre.innerText = b.dataset.nombre;
    delete_codigo.innerText = b.dataset.id;

    let imagen = b.dataset.imagen;

    if (imagen && imagen !== "") {
        delete_imagen.src = "/power-net/public/uploads/" + imagen;
    } else {
        delete_imagen.src = "https://via.placeholder.com/60";
    }

});
</script>

</body>
</html>