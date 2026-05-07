<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 1) {
    header("Location: /power-net/public/index.php");
    exit;
}

require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models/Order.php';
require_once __DIR__ . '/../../models/Venta.php';
require_once __DIR__ . '/../../models/Inventario.php';

// Usuarios
$user     = new User();
$usuarios = $user->obtenerTodos();

// Productos
$product  = new Product();
$productos = $product->obtenerTodosAdmin();

// Métricas productos
$totalProductos  = count($productos);
$stockBajo = $activos = $inactivos = $valorInventario = 0;
foreach ($productos as $p) {
    if ($p['stock'] <= 5)       $stockBajo++;
    if ($p['disponibilidad'])   $activos++;
    else                        $inactivos++;
    $valorInventario += $p['precio'] * $p['stock'];
}

// Pedidos
$order   = new Order();
$pedidos = $order->obtenerPedidos();
$totalPedidos   = count($pedidos);
$pedidosPendientes = count(array_filter($pedidos, fn($p) => strtolower($p['estado_pedido']) === 'pendiente'));

// Ventas reales desde tabla venta
$ventaModel  = new Venta();
$totalesVenta = $ventaModel->totales();
$ventasHoy   = 0;
$ventasDia   = $ventaModel->ventasPorDia(1); // hoy
if (!empty($ventasDia)) {
    $ventasHoy = $ventasDia[0]['monto_total'] ?? 0;
}
$ventasTotales = $totalesVenta['monto_total'] ?? 0;
$totalVentas   = $totalesVenta['total_ventas'] ?? 0;

// Inventario: últimos movimientos
$invModel    = new Inventario();
$movimientos = $invModel->obtenerMovimientos(5);
$topProductos = $ventaModel->topProductos(5);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ICONOS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

<!-- ✅ HEADER -->
<?php include __DIR__ . '/partials/header.php'; ?>

<div class="d-flex">

    <!-- ✅ SIDEBAR -->
    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <!-- CONTENIDO -->
    <div class="flex-grow-1 p-4" style="margin-left: 250px; margin-top: 70px;">

        <h2 class="mb-4">📊 Panel Administrador</h2>

        <!-- 🔥 TARJETAS DASHBOARD -->
   <div class="row mb-4 g-3">

    <div class="col-md-3">
        <div class="card shadow border-0">
            <div class="card-body">
                <h6 class="text-muted">Productos</h6>
                <h3><?= $totalProductos ?></h3>
                <small class="text-muted"><?= $activos ?> activos · <?= $inactivos ?> inactivos</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow border-0">
            <div class="card-body">
                <h6 class="text-muted">Valor inventario</h6>
                <h3>$<?= number_format($valorInventario, 0, ',', '.') ?></h3>
                <small class="text-warning"><?= $stockBajo ?> con stock crítico</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow border-0 bg-success text-white">
            <div class="card-body">
                <h6>Ventas totales</h6>
                <h3>$<?= number_format($ventasTotales, 0, ',', '.') ?></h3>
                <small><?= $totalVentas ?> ventas realizadas</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow border-0 bg-primary text-white">
            <div class="card-body">
                <h6>Pedidos</h6>
                <h3><?= $totalPedidos ?></h3>
                <small><?= $pedidosPendientes ?> pendientes</small>
            </div>
        </div>
    </div>

    </div>

    <div class="row mb-4 g-3">

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Stock Bajo</h6>
                    <h2 class="fw-bold text-warning mb-0"><?= $stockBajo ?></h2>
                    <small class="text-muted">Productos críticos (≤5)</small>
                </div>
                <div class="bg-warning bg-opacity-25 p-3 rounded-circle">
                    <i class="bi bi-exclamation-triangle fs-4 text-warning"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Activos</h6>
                    <h2 class="fw-bold text-success mb-0"><?= $activos ?></h2>
                    <small class="text-muted">Disponibles en tienda</small>
                </div>
                <div class="bg-success bg-opacity-25 p-3 rounded-circle">
                    <i class="bi bi-check-circle fs-4 text-success"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted mb-1">Inactivos</h6>
                    <h2 class="fw-bold text-secondary mb-0"><?= $inactivos ?></h2>
                    <small class="text-muted">No visibles</small>
                </div>
                <div class="bg-secondary bg-opacity-25 p-3 rounded-circle">
                    <i class="bi bi-x-circle fs-4 text-secondary"></i>
                </div>
            </div>
        </div>
    </div>

    </div>

    <!-- TOP PRODUCTOS + ÚLTIMOS MOVIMIENTOS -->
    <div class="row g-4 mb-4">

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white fw-bold">🏆 Top 5 productos más vendidos</div>
                <div class="card-body p-0">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light"><tr><th>#</th><th>Producto</th><th class="text-center">Unidades</th><th class="text-end">Ingresos</th></tr></thead>
                    <tbody>
                    <?php foreach ($topProductos as $i => $tp): ?>
                    <tr>
                        <td><span class="badge bg-dark"><?= $i+1 ?></span></td>
                        <td><?= htmlspecialchars($tp['nombre']) ?></td>
                        <td class="text-center fw-bold"><?= $tp['unidades'] ?></td>
                        <td class="text-end text-success">$<?= number_format($tp['ingresos'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($topProductos)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-3">Sin ventas aún</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-dark text-white fw-bold">🔄 Últimos movimientos de inventario</div>
                <div class="card-body p-0">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light"><tr><th>Producto</th><th class="text-center">Tipo</th><th class="text-center">Cant.</th><th>Fecha</th></tr></thead>
                    <tbody>
                    <?php foreach ($movimientos as $m): ?>
                    <tr>
                        <td class="small"><?= htmlspecialchars($m['nombre_producto']) ?></td>
                        <td class="text-center">
                            <span class="badge <?= $m['tipo'] === 'entrada' ? 'bg-success' : 'bg-danger' ?>">
                                <?= $m['tipo'] === 'entrada' ? '↑' : '↓' ?>
                            </span>
                        </td>
                        <td class="text-center"><?= $m['cantidad'] ?></td>
                        <td class="small text-muted"><?= date('d/m H:i', strtotime($m['fecha'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($movimientos)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-3">Sin movimientos</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>

    </div>

        <!-- 🔥 TABLA -->
     <div class="d-flex justify-content-between align-items-center mb-3">

    <h5 class="fw-bold mb-0">
        <i class="bi bi-people"></i> Usuarios Registrados
    </h5>

    <!-- 🔍 BUSCADOR -->
    <input 
        type="text" 
        id="buscarUsuario" 
        class="form-control w-25" 
        placeholder="Buscar usuario..."
    >

</div>

       <table class="table table-hover align-middle" id="tablaUsuarios" >
        
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Cambiar</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= $u['nombre'] ?></td>
                        <td><?= $u['email'] ?></td>

                        <td>
                            <?php if ($u['id_rol'] == 1): ?>
                                <span class="badge bg-danger">Admin</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Cliente</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <a 
                                href="/power-net/public/index.php?action=cambiar_rol&id=<?= $u['id'] ?>" 
                                class="btn btn-sm btn-outline-primary"
                            >
                                <i class="bi bi-arrow-repeat"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById("buscarUsuario").addEventListener("keyup", function() {

    let filtro = this.value.toLowerCase();
    let filas = document.querySelectorAll("#tablaUsuarios tbody tr");

    filas.forEach(function(fila) {

        let texto = fila.innerText.toLowerCase();

        if (texto.includes(filtro)) {
            fila.style.display = "";
        } else {
            fila.style.display = "none";
        }

    });

});
</script>
</body>
</html>
