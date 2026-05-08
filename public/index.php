<?php
session_start();

// =========================
// CONTROLADORES
// =========================
require_once __DIR__ . '/../controllers/UsuarioController.php';
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
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/MetodoPago.php';
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

$clienteModel = new Cliente();

// =========================
// ROUTER PRINCIPAL
// =========================
switch ($action) {

    // =========================
    // USUARIO
    // =========================
    case 'register':
        $userController->register();
        break;

    case 'login':
        $userController->login();
        break;

    case 'logout':
        $userController->logout();
        break;

    case 'actualizar_perfil':
        $userController->actualizarPerfil();
        break;

    // =========================
    // PRODUCTOS
    // =========================
    case 'guardar_producto':
        $productController->guardar();
        break;

    case 'editar_producto':
        $productController->editar();
        break;

    case 'eliminar_producto':
        $productController->eliminar();
        break;

    case 'toggle_producto':
        $productController->toggle();
        break;

    // =========================
    // CATEGORÍAS
    // =========================
    case 'guardar_categoria':
        $categoryController->guardar();
        break;

    case 'editar_categoria':
        $categoryController->editar();
        break;

    case 'toggle_categoria':
        $categoryController->toggle();
        break;

    // =========================
    // USUARIO ROL
    // =========================
    case 'cambiar_rol':
        (new UsuarioController())->cambiarRol();
        break;

    // =========================
    // VISTAS USUARIO
    // =========================
    case 'mi_perfil':
        $vista = 'perfil';
        break;

    case 'datos_cuenta':
        $vista = 'datos_cuenta';
        break;

    case 'seguridad':
        $vista = 'seguridad';
        break;

    case 'recuperar_password':
        $vista = 'recuperar_password';
        break;

    case 'reset_password':
        $vista = 'reset_password';
        break;

    case 'medios_pago':
        $clienteCtrl->mediosPago($vista, $id_usuario);
        break;

    // =========================
    // FAVORITOS
    // =========================
    case 'toggle_favorito':
        $clienteCtrl->toggleFavorito($id_usuario);
        break;

    case 'pago_exitoso':
        $datos = $_SESSION['pago_exitoso'] ?? null;
        if (!$datos) {
            header("Location: index.php?action=mis_pedidos"); exit;
        }
        $id_pedido_ok = $datos['id_pedido'];
        $factura_ok   = $datos['factura'];
        $total_ok     = $datos['total'];
        unset($_SESSION['pago_exitoso']);
        $vista = 'pago_exitoso';
        break;

    case 'mis_pedidos':
        $clienteCtrl->misPedidos($vista, $id_usuario);
        break;

    case 'mis_favoritos':
        $clienteCtrl->misFavoritos($vista, $id_usuario);
        break;

    case 'mis_devoluciones':
        $clienteCtrl->misDevoluciones($vista, $id_usuario);
        break;

    // ── Envíos (Admin) ──
    case 'guardar_envio':
    case 'actualizar_estado_envio':
    case 'eliminar_envio':
        match($action) {
            'guardar_envio'           => $envCtrl->guardar(),
            'actualizar_estado_envio' => $envCtrl->actualizarEstado(),
            'eliminar_envio'          => $envCtrl->eliminar(),
        };
        exit;

    // ── Devoluciones Admin + Cliente ──
    case 'aprobar_devolucion':
    case 'rechazar_devolucion':
    case 'reembolso_devolucion':
    case 'solicitar_devolucion':
    case 'procesar_devolucion':
        match($action) {
            'aprobar_devolucion'   => $devCtrl->aprobar(),
            'rechazar_devolucion'  => $devCtrl->rechazar(),
            'reembolso_devolucion' => $devCtrl->reembolso(),
            'solicitar_devolucion' => $devCtrl->solicitar(),
            'procesar_devolucion'  => $devCtrl->procesar(),
        };
        exit;

    case 'ofertas':

        require_once __DIR__ . '/../models/Oferta.php';
        require_once __DIR__ . '/../models/Product.php';

        $ofertaVistaModel  = new Oferta();
        $productOfertaModel = new Product();
        $ofertasVista      = $ofertaVistaModel->obtenerActivas();

        // Agregar imágenes a cada producto en oferta
        foreach ($ofertasVista as &$ov) {
            $imgs = $productOfertaModel->obtenerImagenes($ov['id_producto']);
            $ov['imagen'] = $imgs[0]['imagen'] ?? null;
        }
        unset($ov);

        $vista = 'ofertas';
        break;

    // =========================
    // ── Factura ──
    case 'factura':
        if (!isset($_SESSION['usuario'])) { $_SESSION['open_login'] = true; header("Location: index.php"); exit; }
        $id_pedido_fac = (int)($_GET['id'] ?? 0);
        if (!$id_pedido_fac) { header("Location: index.php?action=mis_pedidos"); exit; }
        require_once __DIR__ . '/../models/Pedido.php';
        require_once __DIR__ . '/../models/User.php';
        $pedidoFacModel = new Pedido();
        $clienteFac     = $clienteModel->obtenerPorUsuario($_SESSION['usuario']['id']);
        $id_cli_fac     = $clienteFac['id_cliente'] ?? null;
        $pedidosFac     = $id_cli_fac ? $pedidoFacModel->obtenerPorCliente($id_cli_fac) : [];
        $pedidoFac      = null;
        foreach ($pedidosFac as $pf) { if ($pf['id_pedido'] == $id_pedido_fac) { $pedidoFac = $pf; break; } }
        if (!$pedidoFac) { $_SESSION['alert'] = ['icon'=>'error','title'=>'No autorizado','text'=>'Pedido no encontrado']; header("Location: index.php?action=mis_pedidos"); exit; }
        if (!in_array(strtolower($pedidoFac['estado_pedido']), ['enviado','entregado'])) {
            $_SESSION['alert'] = ['icon'=>'warning','title'=>'Factura no disponible','text'=>'Solo disponible cuando el pedido ha sido enviado o entregado'];
            header("Location: index.php?action=mis_pedidos"); exit;
        }
        $detalleFac = $pedidoFacModel->obtenerDetalle($id_pedido_fac);
        $userFac    = new User();
        $usuarioFac = $userFac->findById($_SESSION['usuario']['id']);
        include __DIR__ . '/../views/cliente/factura.php';
        exit;

    // ── Detalle producto ──
    case 'detalle_producto':
        require_once __DIR__ . '/../models/Product.php';
        $productDetalle      = new Product();
        $id_det              = (int)($_GET['id'] ?? 0);
        $productoDetalle     = $id_det ? $productDetalle->obtenerPorId($id_det) : null;
        $imagenesDetalle     = $id_det ? $productDetalle->obtenerImagenes($id_det) : [];
        $relacionadosDetalle = $productoDetalle
            ? $productDetalle->obtenerRelacionados($id_det, $productoDetalle['id_categoria'] ?? null)
            : [];
        $vista = 'detalle_producto';
        break;

    // ── Carrito ──
    case 'carrito':           $vista = 'carrito'; break;
    case 'agregar_carrito':   $cartController->agregar(); break;
    case 'aumentar_carrito':  $cartController->aumentar(); break;
    case 'disminuir_carrito': $cartController->disminuir(); break;
    case 'eliminar_carrito':  $cartController->eliminar(); break;
    case 'vaciar_carrito':    $cartController->vaciar(); break;
    case 'eliminar_imagen':   $productController->eliminarImagen(); break;

    // ── Pedidos admin ──
    case 'guardar_pedido':
        require_once '../controllers/OrderController.php';
        (new OrderController())->guardar();
        break;
    case 'pedidos':
        require_once '../views/admin/pedidos/pedidos.php';
        break;

    // ── Ofertas admin ──
    case 'guardar_oferta':
    case 'desactivar_oferta':
    case 'activar_oferta':
    case 'editar_oferta':
        match($action) {
            'guardar_oferta'    => $ofCtrl->guardar(),
            'desactivar_oferta' => $ofCtrl->desactivar(),
            'activar_oferta'    => $ofCtrl->activar(),
            'editar_oferta'     => $ofCtrl->editar(),
        };
        exit;

    // ── Ventas admin ──
    case 'eliminar_venta':
        $ventaCtrl->eliminar();
        break;

    case 'actualizar_estado_venta':
        $ventaCtrl->actualizarEstado();
        break;

    // ── Proveedores admin ──
    case 'guardar_proveedor':
    case 'editar_proveedor':
    case 'eliminar_proveedor':
        match($action) {
            'guardar_proveedor'  => $provCtrl->guardar(),
            'editar_proveedor'   => $provCtrl->editar(),
            'eliminar_proveedor' => $provCtrl->eliminar(),
        };
        exit;

    // ── Pago ──
    case 'guardar_metodo':
    case 'editar_metodo':
    case 'eliminar_metodo':
    case 'guardar_direccion':
    case 'editar_direccion':
    case 'eliminar_direccion':
    case 'confirmar_pago':
        match($action) {
            'guardar_metodo'     => $pagoCtrl->guardarMetodo(),
            'editar_metodo'      => $pagoCtrl->editarMetodo(),
            'eliminar_metodo'    => $pagoCtrl->eliminarMetodo(),
            'guardar_direccion'  => $pagoCtrl->guardarDireccion(),
            'editar_direccion'   => $pagoCtrl->editarDireccion(),
            'eliminar_direccion' => $pagoCtrl->eliminarDireccion(),
            'confirmar_pago'     => $pagoCtrl->confirmarPago(),
        };
        exit;

    case 'procesar_pago':
        $clienteCtrl->procesarPago($vista, $id_usuario);
        break;

    // =========================
    // DEFAULT
    // =========================
    default:
        break;
}

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
    <title>Power Net</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
/* =========================================
   POWER NET — ESTILOS TIENDA CLIENTE
========================================= */

/* =========================
   BASE
========================= */
*, *::before, *::after {
    box-sizing: border-box;
}

body {
    font-family: 'Inter', 'Segoe UI', sans-serif;
    background: #f0f2f5;
    color: #1a1a2e;
}

/* =========================
   HERO BANNER
========================= */
.hero-banner {
    background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
    min-height: 320px;
    display: flex;
    align-items: center;
    padding: 60px 40px;
    position: relative;
    overflow: hidden;
}

.hero-banner::after {
    content: '';
    position: absolute;
    width: 500px;
    height: 500px;
    background: rgba(255,255,255,0.03);
    border-radius: 50%;
    right: -100px;
    top: -150px;
}

.hero-content {
    max-width: 600px;
    position: relative;
    z-index: 1;
}

.hero-sub {
    color: #a78bfa;
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 12px;
}

.hero-title {
    color: #fff;
    font-size: clamp(28px, 4vw, 48px);
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 24px;
}

.btn-hero {
    display: inline-block;
    background: #7c3aed;
    color: #fff;
    padding: 14px 32px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 15px;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.btn-hero:hover {
    background: transparent;
    border-color: #7c3aed;
    color: #a78bfa;
}

/* =========================
   SECTION HEADER
========================= */
.section-header {
    display: flex;
    align-items: center;
    gap: 16px;
}

.section-title {
    font-size: 22px;
    font-weight: 800;
    color: #1a1a2e;
    white-space: nowrap;
    margin: 0;
}

.section-line {
    flex: 1;
    height: 2px;
    background: linear-gradient(to right, #7c3aed33, transparent);
    border-radius: 2px;
}

/* =========================
   PRODUCTS GRID
========================= */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 24px;
}

/* =========================
   PRODUCT CARD
========================= */
.pcard {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    position: relative;
}

.pcard:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 32px rgba(124,58,237,0.12);
}

/* Badge */
.pcard__badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: rgba(124,58,237,0.9);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    padding: 3px 10px;
    border-radius: 20px;
    z-index: 2;
}

/* Imagen */
.pcard__img-wrap {
    display: block;
    width: 100%;
    height: 200px;
    background: #f8f8fb;
    overflow: hidden;
    position: relative;
}

.pcard__img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.pcard:hover .pcard__img {
    transform: scale(1.05);
}

/* Agotado */
.pcard__agotado-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.45);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 800;
    font-size: 18px;
    letter-spacing: 1px;
}

.pcard--agotado {
    opacity: 0.75;
}

/* =========================
   PRODUCT BODY
========================= */
.pcard__body {
    padding: 16px;
    display: flex;
    flex-direction: column;
    flex: 1;
    gap: 6px;
}

.pcard__name {
    font-size: 14px;
    font-weight: 700;
    color: #1a1a2e;
    text-decoration: none;
    line-height: 1.3;

    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;

    transition: color 0.2s ease;
}

.pcard__name:hover {
    color: #7c3aed;
}

.pcard__desc {
    font-size: 12px;
    color: #6b7280;
    line-height: 1.4;   

    display: -webkit-box;
    -webkit-line-clamp: 2;
     line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;

    margin: 0;
}

.pcard__price {
    font-size: 22px;
    font-weight: 800;
    color: #7c3aed;
    margin-top: 4px;
}

.pcard__stock {
    font-size: 11px;
    color: #10b981;
    font-weight: 600;
    margin-top: 2px;
}

/* =========================
   QUANTITY
========================= */
.pcard__qty {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f3f4f6;
    border-radius: 10px;
    padding: 4px 8px;
    width: fit-content;
    margin-top: 4px;
}

.pcard__qty-btn {
    width: 28px;
    height: 28px;
    border: none;
    background: #fff;
    border-radius: 8px;
    font-size: 18px;
    font-weight: 700;
    color: #374151;
    cursor: pointer;
    transition: all 0.15s;
    line-height: 1;
}

.pcard__qty-btn:hover {
    background: #7c3aed;
    color: #fff;
}

.pcard__qty-input {
    width: 32px;
    text-align: center;
    border: none;
    background: transparent;
    font-size: 14px;
    font-weight: 700;
    color: #1a1a2e;
}

/* =========================
   ACTIONS
========================= */
.pcard__actions {
    display: flex;
    gap: 8px;
    margin-top: 8px;
}

.pcard__btn {
    flex: 1;
    padding: 10px 0;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pcard__btn--buy {
    background: #1a1a2e;
    color: #fff;
}

.pcard__btn--buy:hover {
    background: #7c3aed;
    color: #fff;
}

.pcard__btn--cart {
    background: #f3f4f6;
    color: #1a1a2e;
    flex: 0 0 44px;
    font-size: 18px;
}

.pcard__btn--cart:hover {
    background: #7c3aed;
    color: #fff;
}

.pcard__btn--disabled {
    background: #e5e7eb;
    color: #9ca3af;
    cursor: not-allowed;
    border-radius: 10px;
    padding: 10px;
    font-size: 13px;
    font-weight: 600;
    border: none;
    width: 100%;
}

/* =========================
   FAVORITO
========================= */
.btn-favorito {
    position: absolute;
    top: 10px;
    left: 10px;
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 50%;
    background: rgba(255,255,255,0.92);
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    transition: all .2s;
    z-index: 3;
    padding: 0;
}

.btn-favorito .corazon {
    font-size: 20px;
    line-height: 1;
    color: #d1d5db;
    transition: all .2s;
}

.btn-favorito.activo .corazon {
    color: #ef4444;
}

.btn-favorito:hover {
    transform: scale(1.15);
    box-shadow: 0 4px 12px rgba(239,68,68,0.3);
}

.btn-favorito:hover .corazon {
    color: #ef4444;
}

.btn-favorito.activo {
    background: #fff0f0;
}

/* =========================
   RESPONSIVE
========================= */
@media (max-width: 576px) {

    .hero-banner {
        padding: 40px 20px;
        min-height: 220px;
    }

    .products-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .pcard__img-wrap {
        height: 150px;
    }

    .pcard__price {
        font-size: 18px;
    }
}
    </style>
</head>
<body class="d-flex flex-column min-vh-100" <?= isset($_SESSION['usuario']) ? 'data-logueado="1"' : '' ?>>
    

<!-- 🔥 HEADER -->
<?php include __DIR__ . '/../views/cliente/partials/header.php'; ?>

<main class="flex-grow-1">

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
        <?php include __DIR__ . '/../views/cliente/ofertas.php'; ?>

  <?php else: ?>
    <?php include __DIR__ . '/../views/cliente/home.php'; ?>
<?php endif; ?>  

<!-- MODAL LOGIN -->
<div class="modal fade" id="loginModal">
  <div class="modal-dialog modal-dialog-centered" style="max-width:500px;">
    <div class="modal-content border-0 shadow-lg" style="border-radius:20px;overflow:hidden;">
      <div class="modal-body p-0">
        <?php include __DIR__ . '/../views/auth/login.php'; ?>
      </div>
    </div>
  </div>
</div>

<!-- MODAL RECUPERAR CONTRASEÑA -->
<div class="modal fade" id="recuperarModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content bg-white p-4">

      <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>

      <h4 class="mb-3">Recuperar contraseña</h4>

      <form method="POST" action="index.php?action=enviar_recuperacion">
        <div class="mb-3">
          <label class="form-label">Correo electrónico</label>
          <input type="email" name="email" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">
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
<script>
function abrirLogin() {
    new bootstrap.Modal(document.getElementById('loginModal')).show();
}
function cambiarCantidad(btn, cambio) {
    const box   = btn.closest('.pcard__qty');
    const input = box.querySelector('.cantidad-input');
    const body  = btn.closest('.pcard__body');
    let cantidad = parseInt(input.value);
    const min = parseInt(input.min);
    const max = parseInt(input.max);
    cantidad += cambio;
    if (cantidad < min) cantidad = min;
    if (cantidad > max) cantidad = max;
    input.value = cantidad;
    body.querySelectorAll('.cantidad-hidden, .cantidad-hidden-buy').forEach(h => { h.value = cantidad; });
}
function toggleFavorito(btn, idProducto) {
    if (!document.querySelector('[data-logueado]')) { abrirLogin(); return; }
    const formData = new FormData();
    formData.append('id_producto', idProducto);
    fetch('index.php?action=toggle_favorito', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.error === 'no_auth') { abrirLogin(); return; }
            const corazon = btn.querySelector('.corazon');
            if (data.favorito) { corazon.textContent = '♥'; btn.classList.add('activo'); btn.title = 'Quitar de favoritos'; }
            else { corazon.textContent = '♡'; btn.classList.remove('activo'); btn.title = 'Agregar a favoritos'; }
        })
        .catch(() => abrirLogin());
}
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('abrirRecuperar')?.addEventListener('click', function (e) {
        e.preventDefault();
        const loginEl = document.getElementById('loginModal');
        const recuperarEl = document.getElementById('recuperarModal');
        const loginModal = bootstrap.Modal.getInstance(loginEl) || new bootstrap.Modal(loginEl);
        loginModal.hide();
        loginEl.addEventListener('hidden.bs.modal', function () {
            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            new bootstrap.Modal(recuperarEl).show();
        }, { once: true });
    });
});
</script>

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
