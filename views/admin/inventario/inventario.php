<?php
if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 1) {
    header("Location: index.php"); exit;
}
require_once __DIR__ . '/../../../models/Inventario.php';
require_once __DIR__ . '/../../../models/Product.php';
require_once __DIR__ . '/../../../models/Category.php';

$invModel  = new Inventario();
$product   = new Product();
$category  = new Category();

// Procesar entrada de stock desde el admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_stock'])) {
    $id_prod  = (int)$_POST['id_producto'];
    $cantidad = (int)$_POST['cantidad'];
    $motivo   = trim($_POST['motivo'] ?? 'ajuste_admin');
    if ($id_prod && $cantidad > 0) {
        $invModel->entrada($id_prod, $cantidad, $motivo);
    }
    header("Location: index.php?action=inventario"); exit;
}

$resumen     = $invModel->resumenStock();
$movimientos = $invModel->obtenerMovimientos(50);
$categorias  = $category->obtenerTodas();
$productos   = $product->obtenerTodosAdmin();

// Métricas
$totalStock    = array_sum(array_column($resumen, 'stock'));
$valorTotal    = array_sum(array_column($resumen, 'valor_inventario'));
$criticos      = count(array_filter($resumen, fn($p) => $p['stock'] > 0 && $p['stock'] <= 5));
$agotados      = count(array_filter($resumen, fn($p) => $p['stock'] == 0));
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Inventario</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="d-flex">
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
<div class="flex-grow-1 p-4" style="margin-left:250px;margin-top:70px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">📊 Inventario</h2>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalEntrada">
            ➕ Agregar stock
        </button>
    </div>

    <!-- MÉTRICAS -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Total unidades</div>
                    <div class="fw-bold fs-3"><?= number_format($totalStock) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Valor inventario</div>
                    <div class="fw-bold fs-3 text-success">$<?= number_format($valorTotal, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-warning">
                <div class="card-body">
                    <div class="text-muted small">Stock crítico (≤5)</div>
                    <div class="fw-bold fs-3 text-warning"><?= $criticos ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-danger">
                <div class="card-body">
                    <div class="text-muted small">Agotados</div>
                    <div class="fw-bold fs-3 text-danger"><?= $agotados ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <!-- STOCK ACTUAL -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white fw-bold d-flex justify-content-between">
                    <span>📦 Stock actual por producto</span>
                    <input type="text" id="buscarProd" class="form-control form-control-sm w-50"
                           placeholder="Buscar...">
                </div>
                <div class="card-body p-0" style="max-height:450px;overflow-y:auto;">
                <table class="table table-hover align-middle mb-0" id="tablaStock">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th class="text-center">Stock</th>
                            <th class="text-end">Valor</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($resumen as $p): ?>
                    <?php
                    $cls = '';
                    if ($p['stock'] == 0)     $cls = 'table-danger';
                    elseif ($p['stock'] <= 5) $cls = 'table-warning';
                    ?>
                    <tr class="<?= $cls ?>">
                        <td class="fw-semibold"><?= htmlspecialchars($p['nombre']) ?></td>
                        <td class="text-muted small"><?= htmlspecialchars($p['nombre_categoria'] ?? '—') ?></td>
                        <td class="text-center">
                            <span class="badge <?= $p['stock'] == 0 ? 'bg-danger' : ($p['stock'] <= 5 ? 'bg-warning text-dark' : 'bg-success') ?>">
                                <?= $p['stock'] ?>
                            </span>
                        </td>
                        <td class="text-end">$<?= number_format($p['valor_inventario'], 0, ',', '.') ?></td>
                        <td class="text-center">
                            <?= $p['disponibilidad']
                                ? '<span class="badge bg-success">Activo</span>'
                                : '<span class="badge bg-secondary">Inactivo</span>' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <!-- ÚLTIMOS MOVIMIENTOS -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white fw-bold">🔄 Últimos movimientos</div>
                <div class="card-body p-0" style="max-height:450px;overflow-y:auto;">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Cant.</th>
                            <th>Motivo</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($movimientos as $m): ?>
                    <tr>
                        <td class="small fw-semibold"><?= htmlspecialchars($m['nombre_producto']) ?></td>
                        <td class="text-center">
                            <span class="badge <?= $m['tipo'] === 'entrada' ? 'bg-success' : 'bg-danger' ?>">
                                <?= $m['tipo'] === 'entrada' ? '↑' : '↓' ?> <?= ucfirst($m['tipo']) ?>
                            </span>
                        </td>
                        <td class="text-center fw-bold"><?= $m['cantidad'] ?></td>
                        <td class="small text-muted"><?= htmlspecialchars($m['motivo'] ?? '—') ?></td>
                        <td class="small"><?= date('d/m H:i', strtotime($m['fecha'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($movimientos)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-3">Sin movimientos</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

    </div>
</div>
</div>

<!-- MODAL AGREGAR STOCK -->
<div class="modal fade" id="modalEntrada" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
            <div class="modal-header bg-dark text-white border-0">
                <h5 class="modal-title fw-bold">➕ Agregar stock</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="agregar_stock" value="1">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Producto</label>
                        <select name="id_producto" class="form-select" required>
                            <option value="">Selecciona un producto</option>
                            <?php foreach ($productos as $p): ?>
                            <option value="<?= $p['id_producto'] ?>">
                                <?= htmlspecialchars($p['nombre']) ?> (stock actual: <?= $p['stock'] ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Cantidad a agregar</label>
                        <input type="number" name="cantidad" class="form-control"
                               min="1" placeholder="Ej: 50" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Motivo</label>
                        <select name="motivo" class="form-select">
                            <option value="ajuste_admin">Ajuste de inventario</option>
                            <option value="compra_proveedor">Compra a proveedor</option>
                            <option value="devolucion">Devolución de cliente</option>
                            <option value="correccion">Corrección de error</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success fw-bold px-4">✅ Guardar entrada</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('buscarProd').addEventListener('keyup', function() {
    const f = this.value.toLowerCase();
    document.querySelectorAll('#tablaStock tbody tr').forEach(r => {
        r.style.display = r.innerText.toLowerCase().includes(f) ? '' : 'none';
    });
});
</script>
</body>
</html>
