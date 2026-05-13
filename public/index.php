<?php
session_start();

// =========================
// CONTROLADORES
// =========================
require_once __DIR__. '/../controllers/UsuarioController.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../controllers/CategoryController.php';
require_once __DIR__ . '/../controllers/CartController.php';
require_once __DIR__ . '/../controllers/ClienteController.php';
require_once __DIR__ . '/../controllers/PagoController.php';
require_once __DIR__ . '/../controllers/DevolucionController.php';
require_once __DIR__ . '/../controllers/EnvioController.php';
require_once __DIR__ . '/../controllers/OfertaController.php';
require_once __DIR__ . '/../controllers/ProveedorController.php';
require_once __DIR__ . '/../controllers/VentaController.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Product.php';

// =========================
// ACTION Y SESIÓN
// =========================
$action     = $_GET['action'] ?? '';
$id_usuario = $_SESSION['usuario']['id'] ?? null;

// =========================
// INSTANCIAS DE CONTROLADORES
// =========================
$userController     = new UsuarioController();
$productController  = new ProductController();
$categoryController = new CategoryController();
$cartController     = new CartController();
$clienteCtrl        = new ClienteController();
$pagoCtrl           = new PagoController();
$devCtrl            = new DevolucionController();
$envCtrl            = new EnvioController();
$ofCtrl             = new OfertaController();
$provCtrl           = new ProveedorController();
$ventaCtrl          = new VentaController();

// =========================
// ROUTER PRINCIPAL
// =========================
// El enrutamiento se delega a routes/web.php para mantener este archivo limpio.
require_once __DIR__ . '/../routes/web.php';
// Ejecuta el despachador de rutas con dependencias explícitas.
dispatchWebRoutes(
    $action,
    $vista,
    $id_usuario,
    $userController,
    $productController,
    $categoryController,
    $cartController,
    $clienteCtrl,
    $pagoCtrl,
    $devCtrl,
    $envCtrl,
    $ofCtrl,
    $provCtrl,
    $ventaCtrl
);

// =========================
// PRODUCTOS PARA HOME — con filtros
// =========================
$product = new Product();

// Solo cargar si estamos en el home
if (!isset($vista) || $vista === 'home') {

    require_once __DIR__ . '/../models/Category.php';
    require_once __DIR__ . '/../models/Favorito.php';

    $categoryModel = new Category();
    $categorias    = $categoryModel->obtenerActivas();

    // IDs de favoritos del usuario logueado (para marcar el corazón)
    $favoritoIds = [];
    if ($id_usuario) {
        $favModel    = new Favorito();
        $favoritoIds = $favModel->obtenerIdsPorUsuario($id_usuario);
    }

    // Leer parámetros de filtro desde GET
    $filtroCategoria = $_GET['categoria']  ?? null;
    $filtroBuscar    = $_GET['buscar']     ?? null;
    $filtroOrden     = $_GET['orden']      ?? null;
    $filtroPrecioMin = $_GET['precio_min'] ?? null;
    $filtroPrecioMax = $_GET['precio_max'] ?? null;
    $filtroOfertas   = isset($_GET['oferta']) && $_GET['oferta'] == '1';

    $productos = $product->filtrar(
        $filtroCategoria ?: null,
        $filtroBuscar    ?: null,
        $filtroOrden     ?: null,
        $filtroPrecioMin !== '' ? $filtroPrecioMin : null,
        $filtroPrecioMax !== '' ? $filtroPrecioMax : null,
        $filtroOfertas
    );

} elseif (!isset($vista) || $vista !== 'procesar_pago') {
    $productos = $product->obtenerActivos();
}
// =========================
// VISTA ACTUAL
// =========================
// Si ninguna ruta asigna vista, el home es la vista por defecto.
$vista = $vista ?? 'home';

// Calcular datos del carrito solo cuando se necesita (vista carrito)
if ($vista === 'carrito') {
    $productosCarrito = [];
    $totalGeneral     = 0;
    $carritoData      = Cart::obtener();

    foreach ($carritoData as $id => $cantidad) {
        $p = (new Product())->obtenerPorId($id);
        if (!$p) continue;

        $subtotal      = $p['precio'] * $cantidad;
        $totalGeneral += $subtotal;
        $img           = (new Product())->obtenerImagenes($id)[0]['imagen'] ?? null;

        $productosCarrito[] = [
            'id'       => $id,
            'nombre'   => $p['nombre'],
            'precio'   => $p['precio'],
            'cantidad' => $cantidad,
            'subtotal' => $subtotal,
            'imagen'   => $img
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>power Net</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            300: '#c4b5fd',
                            400: '#a78bfa',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                            800: '#5b21b6'
                        },
                        accent: {
                            100: '#e0f2fe',
                            300: '#7dd3fc',
                            500: '#0ea5e9',
                            700: '#0369a1'
                        }
                    }
                }
            }
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/power-net/public/assets/css/store.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100 text-slate-900 antialiased selection:bg-brand-200 selection:text-brand-800" <?= isset($_SESSION['usuario']) ? 'data-logueado="1"' : '' ?>>
    

<!-- 🔥 HEADER -->
<?php include __DIR__ . '/../views/cliente/partials/header.php';?>

<main class="flex-grow-1 py-4 md:py-8 app-shell">
<div class="content-shell ring-1 ring-white/70 shadow-2xl shadow-slate-900/5">

    <?php if ($vista === 'perfil'): ?>

        <?php include __DIR__ . '/../views/cliente/perfil.php'; ?>

    <?php elseif ($vista === 'datos_cuenta'): ?>

        <?php include __DIR__ . '/../views/cliente/datos_cuenta.php'; ?>

    <?php elseif ($vista === 'seguridad'): ?>

        <?php include __DIR__ . '/../views/cliente/seguridad.php'; ?>

    <?php elseif ($vista === 'medios_pago'): ?>
        <?php
        $metodosMedios = $GLOBALS['metodosMedios'] ?? [];
        $clienteMedios = $GLOBALS['clienteMedios'] ?? [];
        ?>
        <?php include __DIR__ . '/../views/cliente/medios_pago.php'; ?>

    <?php elseif ($vista === 'carrito'): ?>

        <?php include __DIR__ . '/../views/cliente/carrito.php'; ?>

    <?php elseif ($vista === 'detalle_producto'): ?>
    <?php
    // Datos preparados previamente por ClienteController::detalleProducto()
    $productDetalle      = $GLOBALS['productDetalle']      ?? new Product();
    $productoDetalle     = $GLOBALS['productoDetalle']     ?? null;
    $imagenesDetalle     = $GLOBALS['imagenesDetalle']     ?? [];
    $relacionadosDetalle = $GLOBALS['relacionadosDetalle'] ?? [];
    ?>
    <?php include __DIR__ . '/../views/cliente/detalle_producto.php'; ?>
    
    <?php elseif ($vista === 'procesar_pago'): ?>
        <?php
        // Variables preparadas por ClienteController::procesarPago() via $GLOBALS
        $itemsCarritoPago = $GLOBALS['itemsCarritoPago'] ?? [];
        $totalCarritoPago = $GLOBALS['totalCarritoPago'] ?? 0;
        $metodosPago      = $GLOBALS['metodosPago']      ?? [];
        $direccionPago    = $GLOBALS['direccionPago']    ?? '';
        $tieneDireccion   = $GLOBALS['tieneDireccion']   ?? false;
        $clientePago      = $GLOBALS['clientePago']      ?? [];
        ?>
        <?php include __DIR__ . '/../views/cliente/procesar_pago.php'; ?>

    <?php elseif ($vista === 'pago_exitoso'): ?>
        <?php include __DIR__ . '/../views/cliente/pago_exitoso.php'; ?>

    <?php elseif ($vista === 'mis_pedidos'): ?>
        <?php
        $pedidosMis     = $GLOBALS['pedidosMis']     ?? [];
        $pedidoMisModel = $GLOBALS['pedidoMisModel'] ?? null;
        ?>
        <?php include __DIR__ . '/../views/cliente/mis_pedidos.php'; ?>

    <?php elseif ($vista === 'mis_favoritos'): ?>
        <?php
        $favoritosMis   = $GLOBALS['favoritosMis']   ?? [];
        $favoritoIdsMis = $GLOBALS['favoritoIdsMis'] ?? [];
        $prodFavModel   = $GLOBALS['prodFavModel']   ?? null;
        ?>
        <?php include __DIR__ . '/../views/cliente/mis_favoritos.php'; ?>

    <?php elseif ($vista === 'mis_devoluciones'): ?>
        <?php
        $devolucionesCli = $GLOBALS['devolucionesCli'] ?? [];
        $devModelCli     = $GLOBALS['devModelCli']     ?? null;
        ?>
        <?php include __DIR__ . '/../views/cliente/mis_devoluciones.php'; ?>

    <?php elseif ($vista === 'ofertas'): ?>
        <?php $ofertasVista = $GLOBALS['ofertasVista'] ?? []; ?>
        <?php include __DIR__ . '/../views/cliente/ofertas.php'; ?>

  <?php else: ?>
    <?php include __DIR__ . '/../views/cliente/home.php'; ?>
<?php endif; ?>  
</div>

<!-- MODAL LOGIN -->
<div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered max-w-[520px]">
    <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden bg-white/95 backdrop-blur-md ring-1 ring-brand-100">
      <div class="modal-body p-0">
        <?php include __DIR__ . '/../views/auth/login.php'; ?>
      </div>
    </div>
  </div>
</div>

<!-- MODAL RECUPERAR CONTRASEÑA -->
<div class="modal fade" id="recuperarModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered max-w-[460px]">
    <div class="modal-content bg-white/95 backdrop-blur-md p-5 rounded-3xl shadow-2xl border-0 ring-1 ring-brand-100">

      <button type="button" class="btn-close ms-auto mb-1" data-bs-dismiss="modal"></button>

      <h4 class="mb-1 text-xl font-extrabold text-slate-900">Recuperar contraseña</h4>
      <p class="mb-4 text-sm text-slate-500">Te enviamos un enlace seguro a tu correo.</p>

      <form method="POST" action="index.php?action=enviar_recuperacion">
        <div class="mb-4">
          <label class="mb-2 block text-sm font-semibold text-slate-700">Correo electrónico</label>
          <input type="email" name="email" class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-slate-800 outline-none transition focus:border-brand-400 focus:ring-4 focus:ring-brand-100" required>
        </div>

        <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-brand-700 to-brand-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg shadow-brand-600/25 transition hover:from-brand-600 hover:to-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-200">
          Enviar enlace
        </button>
      </form>

    </div>
  </div>
</div>
<!-- FOOTER -->
<?php include __DIR__ . '/../views/cliente/partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="/power-net/public/assets/js/store.js"></script>

<?php if (!empty($_SESSION['open_login'])): ?>
<script>
document.addEventListener("DOMContentLoaded", function(){
    new bootstrap.Modal(document.getElementById('loginModal')).show();
});
</script>
<?php unset($_SESSION['open_login']); ?>
<?php endif; ?>
</body>
</html>
