<?php
session_start(); // 🔥 IMPORTANTE: necesario para el carrito

// =========================
// CONTROLADORES
// =========================
require_once __DIR__ . '/../controllers/UsuarioController.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../controllers/CategoryController.php';
require_once __DIR__ . '/../controllers/CartController.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/MetodoPago.php';

// =========================
// 🔥 FIX IMPORTANTE: action primero
// =========================
$action = $_GET['action'] ?? '';

$id_usuario = $_SESSION['usuario']['id'] ?? null;

$clienteModel = new Cliente();
$metodoModel = new MetodoPago();

$cliente = null;
$id_cliente = null;

// =========================
// ROUTER: el switch maneja todo (GET y POST por action)
// =========================


// =========================
// INSTANCIAS DE CONTROLADORES
// =========================
$userController = new UsuarioController();
$productController = new ProductController();
$categoryController = new CategoryController();
$cartController = new CartController();

// =========================
// MODELOS
// =========================
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Product.php';

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

        if (!isset($_SESSION['usuario'])) {
            $_SESSION['open_login'] = true;
            header("Location: index.php");
            exit;
        }

        require_once __DIR__ . '/../models/MetodoPago.php';

        $clienteMedios = $clienteModel->obtenerPorUsuario($id_usuario);
        $id_cli_medios = $clienteMedios['id_cliente'] ?? null;
        $metodosMedios = $id_cli_medios ? (new MetodoPago())->obtenerPorCliente($id_cli_medios) : [];

        $vista = 'medios_pago';
        break;

    // =========================
    // FAVORITOS
    // =========================
    case 'toggle_favorito':

        if (!isset($_SESSION['usuario'])) {
            // Si no está logueado, responder con JSON para el JS
            header('Content-Type: application/json');
            echo json_encode(['error' => 'no_auth']);
            exit;
        }

        require_once __DIR__ . '/../models/Favorito.php';

        $id_prod_fav = (int)($_POST['id_producto'] ?? 0);
        $id_usr_fav  = $_SESSION['usuario']['id'];

        if ($id_prod_fav) {
            $favModel  = new Favorito();
            $esFav     = $favModel->toggle($id_usr_fav, $id_prod_fav);
            $total     = $favModel->contarPorProducto($id_prod_fav);

            header('Content-Type: application/json');
            echo json_encode([
                'favorito' => $esFav,
                'total'    => $total
            ]);
        }
        exit;

    case 'mis_pedidos':

        // Validar login antes de imprimir HTML
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['open_login'] = true;
            header("Location: index.php");
            exit;
        }

        require_once __DIR__ . '/../models/Pedido.php';

        $pedidoMisModel  = new Pedido();
        $clienteMisModel = new Cliente();

        $clienteMis  = $clienteMisModel->obtenerPorUsuario($id_usuario);
        $id_cli_mis  = $clienteMis['id_cliente'] ?? null;
        $pedidosMis  = $id_cli_mis ? $pedidoMisModel->obtenerPorCliente($id_cli_mis) : [];

        $vista = 'mis_pedidos';
        break;

    case 'mis_favoritos':

        if (!isset($_SESSION['usuario'])) {
            $_SESSION['open_login'] = true;
            header("Location: index.php");
            exit;
        }

        require_once __DIR__ . '/../models/Favorito.php';
        require_once __DIR__ . '/../models/Product.php';

        $favMisModel      = new Favorito();
        $prodFavModel     = new Product();
        $favoritosMis     = $favMisModel->obtenerProductosPorUsuario($id_usuario);
        $favoritoIdsMis   = array_column($favoritosMis, 'id_producto');

        $vista = 'mis_favoritos';
        break;

    // =========================
    // FACTURA DE UN PEDIDO
    // Solo disponible si el pedido está enviado o entregado
    // =========================
    case 'factura':

        if (!isset($_SESSION['usuario'])) {
            $_SESSION['open_login'] = true;
            header("Location: index.php");
            exit;
        }

        $id_pedido_fac = (int)($_GET['id'] ?? 0);

        if (!$id_pedido_fac) {
            header("Location: index.php?action=mis_pedidos");
            exit;
        }

        require_once __DIR__ . '/../models/Pedido.php';

        $pedidoFacModel = new Pedido();
        $clienteFacModel = new Cliente();

        $clienteFac  = $clienteFacModel->obtenerPorUsuario($_SESSION['usuario']['id']);
        $id_cli_fac  = $clienteFac['id_cliente'] ?? null;

        // Obtener pedidos del cliente y verificar que este pedido le pertenece
        $pedidosFac  = $id_cli_fac ? $pedidoFacModel->obtenerPorCliente($id_cli_fac) : [];
        $pedidoFac   = null;

        foreach ($pedidosFac as $pf) {
            if ($pf['id_pedido'] == $id_pedido_fac) {
                $pedidoFac = $pf;
                break;
            }
        }

        if (!$pedidoFac) {
            $_SESSION['alert'] = ['icon' => 'error', 'title' => 'No autorizado', 'text' => 'Pedido no encontrado'];
            header("Location: index.php?action=mis_pedidos");
            exit;
        }

        // Solo permitir si el estado es enviado o entregado
        $estadosPermitidos = ['enviado', 'entregado'];
        if (!in_array(strtolower($pedidoFac['estado_pedido']), $estadosPermitidos)) {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => 'Factura no disponible',
                'text'  => 'La factura solo está disponible cuando el pedido ha sido enviado o entregado'
            ];
            header("Location: index.php?action=mis_pedidos");
            exit;
        }

        $detalleFac = $pedidoFacModel->obtenerDetalle($id_pedido_fac);

        // Obtener datos del usuario para la factura
        require_once __DIR__ . '/../models/User.php';
        $userFac = new User();
        $usuarioFac = $userFac->findById($_SESSION['usuario']['id']);

        // Renderizar la factura directamente (sin layout del sitio)
        include __DIR__ . '/../views/cliente/factura.php';
        exit;

    // =========================
    // SOLICITAR DEVOLUCIÓN
    // Solo disponible si el pedido está entregado
    // =========================
    case 'solicitar_devolucion':

        if (!isset($_SESSION['usuario'])) {
            $_SESSION['open_login'] = true;
            header("Location: index.php"); exit;
        }

        $id_pedido_dev = (int)($_GET['id'] ?? 0);
        if (!$id_pedido_dev) {
            header("Location: index.php?action=mis_pedidos"); exit;
        }

        require_once __DIR__ . '/../models/Pedido.php';
        require_once __DIR__ . '/../models/Devolucion.php';

        $pedidoDevModel  = new Pedido();
        $devModel        = new Devolucion();
        $clienteDevModel = new Cliente();

        $clienteDev  = $clienteDevModel->obtenerPorUsuario($_SESSION['usuario']['id']);
        $id_cli_dev  = $clienteDev['id_cliente'] ?? null;

        // Verificar que el pedido pertenece al cliente
        $pedidosDev = $id_cli_dev ? $pedidoDevModel->obtenerPorCliente($id_cli_dev) : [];
        $pedidoDev  = null;
        foreach ($pedidosDev as $pd) {
            if ($pd['id_pedido'] == $id_pedido_dev) { $pedidoDev = $pd; break; }
        }

        if (!$pedidoDev) {
            $_SESSION['alert'] = ['icon' => 'error', 'title' => 'No autorizado', 'text' => 'Pedido no encontrado'];
            header("Location: index.php?action=mis_pedidos"); exit;
        }

        // Solo permitir devolución si está entregado
        if (strtolower($pedidoDev['estado_pedido']) !== 'entregado') {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => 'No disponible',
                'text'  => 'Solo puedes solicitar devolución de pedidos entregados'
            ];
            header("Location: index.php?action=mis_pedidos"); exit;
        }

        // Verificar que no tenga ya una devolución
        if ($devModel->existePorPedido($id_pedido_dev)) {
            $_SESSION['alert'] = [
                'icon'  => 'info',
                'title' => 'Ya existe',
                'text'  => 'Ya tienes una solicitud de devolución para este pedido'
            ];
            header("Location: index.php?action=mis_pedidos"); exit;
        }

        $detalleDev = $pedidoDevModel->obtenerDetalle($id_pedido_dev);

        include __DIR__ . '/../views/cliente/solicitar_devolucion.php';
        exit;

    // =========================
    // PROCESAR DEVOLUCIÓN (guardar en BD)
    // =========================
    case 'procesar_devolucion':

        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php"); exit;
        }

        require_once __DIR__ . '/../models/Devolucion.php';
        require_once __DIR__ . '/../models/Pedido.php';

        $id_pedido_proc  = (int)($_POST['id_pedido'] ?? 0);
        $motivo_general  = trim($_POST['motivo_general'] ?? '');
        $productos_dev   = $_POST['productos'] ?? [];

        // Filtrar solo los productos seleccionados
        $items_dev = [];
        foreach ($productos_dev as $id_prod => $data) {
            if (!empty($data['seleccionado'])) {
                $items_dev[] = [
                    'id_producto' => (int)$id_prod,
                    'cantidad'    => (int)($data['cantidad'] ?? 1),
                    'motivo'      => $data['motivo'] ?? 'Sin motivo'
                ];
            }
        }

        if (empty($items_dev)) {
            $_SESSION['alert'] = ['icon' => 'error', 'title' => 'Sin productos', 'text' => 'Selecciona al menos un producto'];
            header("Location: index.php?action=solicitar_devolucion&id=" . $id_pedido_proc); exit;
        }

        // Calcular monto de devolución basado en los productos seleccionados
        $pedidoProcModel = new Pedido();
        $detalleProcDev  = $pedidoProcModel->obtenerDetalle($id_pedido_proc);
        $monto_dev       = 0;

        foreach ($detalleProcDev as $dp) {
            foreach ($items_dev as $item) {
                if ($item['id_producto'] == $dp['id_producto']) {
                    $monto_dev += $dp['precio_unitario'] * $item['cantidad'];
                }
            }
        }

        $devProcModel = new Devolucion();
        $id_dev = $devProcModel->crear($id_pedido_proc, $monto_dev, $items_dev, $motivo_general);

        $_SESSION['alert'] = [
            'icon'  => 'success',
            'title' => '¡Solicitud enviada!',
            'text'  => 'Tu solicitud de devolución #' . $id_dev . ' fue registrada. La revisaremos en 3-5 días hábiles.'
        ];

        header("Location: index.php?action=mis_pedidos");
        exit;

    case 'detalle_producto':
        // Preparar el producto desde el router para que la vista no tenga que hacer redirects
        require_once __DIR__ . '/../models/Product.php';
        $productDetalle = new Product();
        $id_det         = (int)($_GET['id'] ?? 0);
        $productoDetalle = $id_det ? $productDetalle->obtenerPorId($id_det) : null;
        $imagenesDetalle = $id_det ? $productDetalle->obtenerImagenes($id_det) : [];
        $relacionadosDetalle = $productoDetalle
            ? $productDetalle->obtenerRelacionados($id_det, $productoDetalle['id_categoria'] ?? null)
            : [];
        $vista = 'detalle_producto';
        break;

    // =========================
    // 🛒 CARRITO (CORREGIDO)
    // =========================

    // 🔥 MOSTRAR CARRITO (ESTO ESTABA MAL EN TU CÓDIGO)
    case 'carrito':
    $vista = 'carrito';
    break;

    // 🛒 AGREGAR PRODUCTO AL CARRITO
    case 'agregar_carrito':
        $cartController->agregar();
        break;

    // ➕ AUMENTAR CANTIDAD
    case 'aumentar_carrito':
        $cartController->aumentar();
        break;

    // ➖ DISMINUIR CANTIDAD
    case 'disminuir_carrito':
        $cartController->disminuir();
        break;

    // 🗑 ELIMINAR PRODUCTO
    case 'eliminar_carrito':
        $cartController->eliminar();
        break;

    // 🧹 VACIAR CARRITO
    case 'vaciar_carrito':
        $cartController->vaciar();
        break;

    // =========================
    // IMÁGENES
    // =========================
    case 'eliminar_imagen':
        $productController->eliminarImagen();
        break;

    // =========================
    // PEDIDOS
    // =========================
    case 'guardar_pedido':
        require_once '../controllers/OrderController.php';
        $controller = new OrderController();
        $controller->guardar();
        break;

    case 'pedidos':
        require_once '../views/admin/pedidos/pedidos.php';
        break;

    // =========================
    // OFERTAS (ADMIN)
    // =========================
    case 'guardar_oferta':
        require_once __DIR__ . '/../controllers/OfertaController.php';
        (new OfertaController())->guardar();
        break;

    case 'desactivar_oferta':
        require_once __DIR__ . '/../controllers/OfertaController.php';
        (new OfertaController())->desactivar();
        break;
        

    case 'guardar_metodo':

        require_once __DIR__ . '/../models/Cliente.php';
        require_once __DIR__ . '/../models/MetodoPago.php';

        if (!isset($_SESSION['usuario'])) {
            $_SESSION['open_login'] = true;
            header("Location: index.php");
            exit;
        }

        $clienteModel = new Cliente();
        $metodoModel  = new MetodoPago();

        $id_usuario = $_SESSION['usuario']['id'];
        $cliente    = $clienteModel->obtenerPorUsuario($id_usuario);

        $id_cliente = $cliente['id_cliente'] ?? null;

        // Si no tiene cliente, crearlo automáticamente
        if (!$id_cliente) {
            $clienteModel->crearSiNoExiste($id_usuario);
            $cliente    = $clienteModel->obtenerPorUsuario($id_usuario);
            $id_cliente = $cliente['id_cliente'] ?? null;
        }

        if (!$id_cliente) {
            $_SESSION['alert'] = [
                'icon'  => 'error',
                'title' => 'Error de cuenta',
                'text'  => 'No se encontró tu perfil de cliente. ID usuario: ' . $id_usuario
            ];
            header("Location: index.php?action=procesar_pago");
            exit;
        }

        $tipo         = $_POST['tipo']    ?? '';
        $numero       = trim($_POST['numero']  ?? '');
        $titular      = trim($_POST['titular'] ?? '');

        // Validar campos uno por uno para dar mensaje preciso
        if (empty($tipo)) {
            $_SESSION['alert'] = ['icon' => 'error', 'title' => 'Falta el tipo', 'text' => 'Selecciona el tipo de pago'];
            header("Location: index.php?action=procesar_pago"); exit;
        }
        if (empty($numero)) {
            $_SESSION['alert'] = ['icon' => 'error', 'title' => 'Falta el número', 'text' => 'Ingresa el número de tarjeta o cuenta'];
            header("Location: index.php?action=procesar_pago"); exit;
        }
        if (empty($titular)) {
            $_SESSION['alert'] = ['icon' => 'error', 'title' => 'Falta el titular', 'text' => 'Ingresa el nombre del titular'];
            header("Location: index.php?action=procesar_pago"); exit;
        }

        // Guardar método de pago
        $ok = $metodoModel->guardar($id_cliente, $tipo, $numero, $titular);

        if (!$ok) {
            $_SESSION['alert'] = ['icon' => 'error', 'title' => 'Error al guardar', 'text' => 'No se pudo guardar el método. Intenta de nuevo.'];
            header("Location: index.php?action=procesar_pago"); exit;
        }

        $_SESSION['alert'] = [
            'icon'  => 'success',
            'title' => 'Guardado',
            'text'  => 'Método de pago guardado correctamente'
        ];

        // Redirige según de dónde vino
        $redirect = $_POST['redirect'] ?? 'procesar_pago';
        $allowed  = ['medios_pago', 'procesar_pago', 'mi_perfil_metodos'];
        $redirect = in_array($redirect, $allowed) ? $redirect : 'procesar_pago';

        if ($redirect === 'mi_perfil_metodos') {
            header("Location: index.php?action=mi_perfil&tab=metodos");
        } else {
            header("Location: index.php?action=" . $redirect);
        }
        exit;

    // =========================
    // ELIMINAR MÉTODO DE PAGO
    // =========================
    case 'eliminar_metodo':

        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php"); exit;
        }

        $id_metodo_del  = (int)($_GET['id'] ?? 0);
        $metodoDelModel = new MetodoPago();

        if ($id_metodo_del) {
            $clienteDel     = $clienteModel->obtenerPorUsuario($id_usuario);
            $id_cliente_del = $clienteDel['id_cliente'] ?? null;
            $metodoDel      = $metodoDelModel->obtenerPorId($id_metodo_del);

            // Solo elimina si el método pertenece al cliente logueado
            if ($metodoDel && $metodoDel['id_cliente'] == $id_cliente_del) {
                $metodoDelModel->eliminar($id_metodo_del);
                $_SESSION['alert'] = ['icon' => 'success', 'title' => 'Eliminado', 'text' => 'Método de pago eliminado'];
            } else {
                $_SESSION['alert'] = ['icon' => 'error', 'title' => 'Error', 'text' => 'No tienes permiso para eliminar este método'];
            }
        }

        $redirectDel = $_GET['redirect'] ?? 'procesar_pago';
        if ($redirectDel === 'mi_perfil_metodos') {
            header("Location: index.php?action=mi_perfil&tab=metodos");
        } else {
            header("Location: index.php?action=procesar_pago");
        }
        exit;

    // =========================
    // EDITAR MÉTODO DE PAGO
    // =========================
    case 'editar_metodo':

        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php"); exit;
        }

        $id_metodo_edit  = (int)($_POST['id_metodo'] ?? 0);
        $tipo_edit       = trim($_POST['tipo']    ?? '');
        $numero_edit     = trim($_POST['numero']  ?? '');
        $titular_edit    = trim($_POST['titular'] ?? '');
        $metodoEditModel = new MetodoPago();

        if ($id_metodo_edit && $numero_edit && $titular_edit) {
            $clienteEdit     = $clienteModel->obtenerPorUsuario($id_usuario);
            $id_cliente_edit = $clienteEdit['id_cliente'] ?? null;
            $metodoEdit      = $metodoEditModel->obtenerPorId($id_metodo_edit);

            if ($metodoEdit && $metodoEdit['id_cliente'] == $id_cliente_edit) {
                $metodoEditModel->actualizar($id_metodo_edit, $tipo_edit, $numero_edit, $titular_edit);
                $_SESSION['alert'] = ['icon' => 'success', 'title' => 'Actualizado', 'text' => 'Método de pago actualizado'];
            } else {
                $_SESSION['alert'] = ['icon' => 'error', 'title' => 'Error', 'text' => 'No tienes permiso para editar este método'];
            }
        }

        $redirectEdit = $_POST['redirect'] ?? 'procesar_pago';
        if ($redirectEdit === 'mi_perfil_metodos') {
            header("Location: index.php?action=mi_perfil&tab=metodos");
        } else {
            header("Location: index.php?action=procesar_pago");
        }
        exit;

    // =========================
    // GUARDAR / ACTUALIZAR DIRECCIÓN
    // Usa el campo direccion que ya existe en la tabla cliente
    // =========================
    case 'guardar_direccion':

        if (!isset($_SESSION['usuario'])) { header("Location: index.php"); exit; }

        $id_usuario_dir = $_SESSION['usuario']['id'];
        $clienteDir     = $clienteModel->obtenerPorUsuario($id_usuario_dir);

        if (!$clienteDir) {
            $clienteModel->crearSiNoExiste($id_usuario_dir);
            $clienteDir = $clienteModel->obtenerPorUsuario($id_usuario_dir);
        }

        $id_cliente_dir = $clienteDir['id_cliente'] ?? null;
        $direccion_dir  = trim($_POST['direccion']    ?? '');
        $ciudad_dir     = trim($_POST['ciudad']       ?? '');
        $depto_dir      = trim($_POST['departamento'] ?? '');
        $telefono_dir   = trim($_POST['telefono']     ?? '');

        // Solo requiere la dirección, ciudad y departamento son opcionales
        if ($id_cliente_dir && $direccion_dir) {

            // Construir dirección completa con los campos que vengan
            $partes = array_filter([$direccion_dir, $ciudad_dir, $depto_dir]);
            $direccionCompleta = implode(', ', $partes);

            $clienteModel->actualizarDireccion($id_cliente_dir, $direccionCompleta, $telefono_dir);
            $_SESSION['alert'] = ['icon' => 'success', 'title' => 'Guardada', 'text' => 'Dirección guardada correctamente'];
        } else {
            $_SESSION['alert'] = ['icon' => 'error', 'title' => 'Incompleto', 'text' => 'El campo dirección es obligatorio'];
        }

        // Redirigir al perfil si viene desde ahí, si no a procesar_pago
        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, 'tab=direccion') !== false || strpos($referer, 'mi_perfil') !== false) {
            header("Location: index.php?action=mi_perfil&tab=direccion");
        } else {
            header("Location: index.php?action=procesar_pago");
        }
        exit;

    // =========================
    // EDITAR DIRECCIÓN (misma lógica, actualiza cliente.direccion)
    // =========================
    case 'editar_direccion':

        if (!isset($_SESSION['usuario'])) { header("Location: index.php"); exit; }

        $id_usuario_edit2 = $_SESSION['usuario']['id'];
        $clienteEdit2     = $clienteModel->obtenerPorUsuario($id_usuario_edit2);
        $id_cliente_edit2 = $clienteEdit2['id_cliente'] ?? null;

        $direccion_e2 = trim($_POST['direccion']    ?? '');
        $ciudad_e2    = trim($_POST['ciudad']       ?? '');
        $depto_e2     = trim($_POST['departamento'] ?? '');
        $telefono_e2  = trim($_POST['telefono']     ?? '');

        if ($id_cliente_edit2 && $direccion_e2) {
            $direccionCompleta2 = $direccion_e2 . ', ' . $ciudad_e2 . ', ' . $depto_e2;
            $clienteModel->actualizarDireccion($id_cliente_edit2, $direccionCompleta2, $telefono_e2);
            $_SESSION['alert'] = ['icon' => 'success', 'title' => 'Actualizada', 'text' => 'Dirección actualizada correctamente'];
        }

        header("Location: index.php?action=procesar_pago");
        exit;

    // =========================
    // ELIMINAR DIRECCIÓN (limpia el campo en cliente)
    // =========================
    case 'eliminar_direccion':

        if (!isset($_SESSION['usuario'])) { header("Location: index.php"); exit; }

        $clienteDel3     = $clienteModel->obtenerPorUsuario($_SESSION['usuario']['id']);
        $id_cliente_del3 = $clienteDel3['id_cliente'] ?? null;

        if ($id_cliente_del3) {
            $clienteModel->actualizarDireccion($id_cliente_del3, '', '');
            $_SESSION['alert'] = ['icon' => 'success', 'title' => 'Eliminada', 'text' => 'Dirección eliminada'];
        }

        $redirectDir = $_GET['redirect'] ?? 'procesar_pago';
        if ($redirectDir === 'mi_perfil_direccion') {
            header("Location: index.php?action=mi_perfil&tab=direccion");
        } else {
            header("Location: index.php?action=procesar_pago");
        }
        exit;

    case 'procesar_pago':

        require_once __DIR__ . '/../models/Cart.php';
        require_once __DIR__ . '/../models/Product.php';
        require_once __DIR__ . '/../models/MetodoPago.php';

        // ── Validar login ──
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['open_login'] = true;
            header("Location: index.php");
            exit;
        }

        // ── Obtener / crear cliente ──
        $clientePago = $clienteModel->obtenerPorUsuario($id_usuario);
        if (!$clientePago) {
            $clienteModel->crearSiNoExiste($id_usuario);
            $clientePago = $clienteModel->obtenerPorUsuario($id_usuario);
        }
        $id_cliente_pago = $clientePago['id_cliente'] ?? null;

        // ── Cargar métodos de pago ──
        $metodoModelPago  = new MetodoPago();
        $metodosPago      = $id_cliente_pago ? $metodoModelPago->obtenerPorCliente($id_cliente_pago) : [];

        // ── Dirección ──
        $direccionPago    = trim($clientePago['direccion'] ?? '');
        $tieneDireccion   = !empty($direccionPago);

        // ── Construir resumen del carrito ──
        $productModelPago = new Product();
        $carritoDataPago  = Cart::obtener();
        $itemsCarritoPago = [];
        $totalCarritoPago = 0;

        foreach ($carritoDataPago as $id_prod => $cantidad) {
            $p = $productModelPago->obtenerPorId($id_prod);
            if (!$p) continue;
            $imagenes          = $productModelPago->obtenerImagenes($id_prod);
            $img               = $imagenes[0]['imagen'] ?? null;
            $subtotal          = $p['precio'] * $cantidad;
            $totalCarritoPago += $subtotal;
            $itemsCarritoPago[] = [
                'nombre'   => $p['nombre'],
                'precio'   => $p['precio'],
                'cantidad' => $cantidad,
                'subtotal' => $subtotal,
                'imagen'   => $img
            ];
        }

        // ── Si carrito vacío, redirigir ──
        if (empty($itemsCarritoPago)) {
            $_SESSION['alert'] = ['icon' => 'warning', 'title' => 'Carrito vacío', 'text' => 'Agrega productos antes de pagar'];
            header("Location: index.php");
            exit;
        }

        $vista = 'procesar_pago';
        break;

    // =========================
    // CONFIRMAR PAGO (guarda pedido + pago en BD)
    // =========================
    case 'confirmar_pago':

        if (!isset($_SESSION['usuario'])) {
            $_SESSION['open_login'] = true;
            header("Location: index.php");
            exit;
        }

        require_once __DIR__ . '/../models/Cart.php';
        require_once __DIR__ . '/../models/Product.php';
        require_once __DIR__ . '/../models/MetodoPago.php';
        require_once __DIR__ . '/../models/Inventario.php';
        require_once __DIR__ . '/../models/Venta.php';

        $id_usuario   = $_SESSION['usuario']['id'];
        $clienteModel = new Cliente();
        $cliente      = $clienteModel->obtenerPorUsuario($id_usuario);

        // Auto-crear cliente si no existe
        if (!$cliente || empty($cliente['id_cliente'])) {
            $clienteModel->crearSiNoExiste($id_usuario);
            $cliente = $clienteModel->obtenerPorUsuario($id_usuario);
        }

        if (!$cliente || empty($cliente['id_cliente'])) {
            $_SESSION['alert'] = [
                'icon'  => 'error',
                'title' => 'Error de cuenta',
                'text'  => 'No se pudo vincular tu cuenta. Intenta de nuevo.'
            ];
            header("Location: index.php?action=procesar_pago");
            exit;
        }

        $id_cliente = $cliente['id_cliente'];
        $id_metodo  = $_POST['metodo_guardado'] ?? null;

        // Verificar que tiene dirección de envío registrada
        if (empty(trim($cliente['direccion'] ?? ''))) {
            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => 'Dirección requerida',
                'text'  => 'Debes agregar una dirección de envío antes de pagar'
            ];
            header("Location: index.php?action=procesar_pago");
            exit;
        }

        if (!$id_metodo) {
            $_SESSION['alert'] = [
                'icon'  => 'error',
                'title' => 'Sin método de pago',
                'text'  => 'Selecciona un método de pago para continuar'
            ];
            header("Location: index.php?action=procesar_pago");
            exit;
        }

        $cart = Cart::obtener();

        if (empty($cart)) {
            $_SESSION['alert'] = [
                'icon'  => 'error',
                'title' => 'Carrito vacío',
                'text'  => 'No hay productos en el carrito'
            ];
            header("Location: index.php?action=carrito");
            exit;
        }

        try {
            // Conexión exclusiva para esta transacción
            $db = new PDO(
                "mysql:host=localhost;dbname=powernet;charset=utf8",
                "root",
                "",
                [
                    PDO::ATTR_ERRMODE          => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            // Obtener tipo del método seleccionado
            $metodoModel    = new MetodoPago();
            $metodosCliente = $metodoModel->obtenerPorCliente($id_cliente);
            $tipoMetodo     = 'tarjeta';
            foreach ($metodosCliente as $m) {
                if ($m['id_metodo'] == $id_metodo) {
                    $tipoMetodo = $m['tipo'];
                    break;
                }
            }

            // Calcular total e ítems + VALIDAR STOCK antes de la transacción
            $total = 0;
            $items = [];
            $sinStock = [];

            foreach ($cart as $id_prod => $cantidad) {
                $stmtP = $db->prepare(
                    "SELECT p.id_producto, p.nombre, p.precio, p.stock,
                            o.precio_oferta
                     FROM productos p
                     LEFT JOIN oferta o ON o.id_producto = p.id_producto
                                       AND o.estado = 1
                                       AND o.fecha_inicio <= CURDATE()
                                       AND o.fecha_fin    >= CURDATE()
                     WHERE p.id_producto = ? LIMIT 1"
                );
                $stmtP->execute([$id_prod]);
                $p = $stmtP->fetch(PDO::FETCH_ASSOC);
                if (!$p) continue;

                // Verificar stock suficiente
                if ($p['stock'] < $cantidad) {
                    $sinStock[] = $p['nombre'] . ' (disponible: ' . $p['stock'] . ')';
                    continue;
                }

                // Usar precio_oferta si existe, si no el precio normal
                $precioFinal = !empty($p['precio_oferta'])
                    ? $p['precio_oferta']
                    : $p['precio'];

                $subtotal = $precioFinal * $cantidad;
                $total   += $subtotal;

                $items[] = [
                    'id'       => $id_prod,
                    'nombre'   => $p['nombre'],
                    'cantidad' => $cantidad,
                    'precio'   => $precioFinal,
                    'subtotal' => $subtotal
                ];
            }

            // Si algún producto no tiene stock suficiente, abortar
            if (!empty($sinStock)) {
                throw new Exception('Stock insuficiente para: ' . implode(', ', $sinStock));
            }

            if (empty($items)) {
                throw new Exception('No se encontraron productos válidos en el carrito');
            }

            // ── TRANSACCIÓN: todo o nada ──
            $db->beginTransaction();

            // 1. Insertar pedido
            $stmtPedido = $db->prepare(
                "INSERT INTO pedido (fecha_pedido, total_pedido, estado_pedido, id_cliente)
                 VALUES (NOW(), ?, 'pendiente', ?)"
            );
            $stmtPedido->execute([$total, $id_cliente]);
            $id_pedido = $db->lastInsertId();

            // 2. Insertar detalle + descontar stock
            $stmtDetalle = $db->prepare(
                "INSERT INTO detalle_pedido (id_pedido, id_producto, precio_unitario, cantidad, subtotal)
                 VALUES (?, ?, ?, ?, ?)"
            );

            // Descuenta stock solo si hay suficiente (stock >= cantidad pedida)
            $stmtStock = $db->prepare(
                "UPDATE productos SET stock = stock - ? WHERE id_producto = ? AND stock >= ?"
            );

            // Si el stock llega a 0, pone disponibilidad = 0 → desaparece del catálogo
            $stmtOcultar = $db->prepare(
                "UPDATE productos SET disponibilidad = 0 WHERE id_producto = ? AND stock = 0"
            );

            foreach ($items as $item) {

                // Guardar línea de detalle del pedido
                $stmtDetalle->execute([
                    $id_pedido,
                    $item['id'],
                    $item['precio'],
                    $item['cantidad'],
                    $item['subtotal']
                ]);

                // Descontar del stock
                $stmtStock->execute([
                    $item['cantidad'],
                    $item['id'],
                    $item['cantidad']
                ]);

                // Si quedó en 0, ocultarlo del catálogo automáticamente
                $stmtOcultar->execute([$item['id']]);
            }

            // 3. Insertar pago
            $factura   = 'FAC-' . strtoupper(uniqid());
            $stmtPago  = $db->prepare(
                "INSERT INTO pago (monto, metodo_pago, fecha_pago, factura, estado_pago, id_pedido)
                 VALUES (?, ?, NOW(), ?, 'pagado', ?)"
            );
            $stmtPago->execute([$total, $tipoMetodo, $factura, $id_pedido]);

            $db->commit();

            // ── Registrar movimientos de inventario y venta ──
            $invModel   = new Inventario();
            $ventaModel = new Venta();

            // Registrar salida de stock por cada producto vendido
            foreach ($items as $item) {
                $invModel->salida($item['id'], $item['cantidad'], $id_pedido);
            }

            // Registrar la venta en la tabla venta
            $ventaModel->registrar($id_pedido, $id_cliente, $total);

            // Vaciar carrito
            Cart::vaciar();

            $_SESSION['alert'] = [
                'icon'  => 'success',
                'title' => '¡Pago exitoso!',
                'text'  => 'Pedido #' . $id_pedido . ' registrado. Factura: ' . $factura
            ];

            header("Location: index.php?action=mis_pedidos");
            exit;

        } catch (Exception $e) {
            if (isset($db) && $db->inTransaction()) {
                $db->rollBack();
            }
            $_SESSION['alert'] = [
                'icon'  => 'error',
                'title' => 'Error al procesar el pago',
                'text'  => $e->getMessage()
            ];
            header("Location: index.php?action=procesar_pago");
            exit;
        }
        break;

    // =========================
    // DEFAULT
    // =========================
    default:
        // carga home por defecto
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
    <title>Power Net</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- TAILWIND -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 d-flex flex-column min-vh-100" <?= isset($_SESSION['usuario']) ? 'data-logueado="1"' : '' ?>>
    

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

        <?php include __DIR__ . '/../views/cliente/medios_pago.php'; ?>

    <?php elseif ($vista === 'carrito'): ?>

        <?php include __DIR__ . '/../views/cliente/carrito.php'; ?>

        <?php elseif ($vista === 'detalle_producto'): ?>

    <?php include __DIR__ . '/../views/cliente/detalle_producto.php'; ?>
    
    <?php elseif ($vista === 'procesar_pago'): ?>
        <?php include __DIR__ . '/../views/cliente/procesar_pago.php'; ?>

    <?php elseif ($vista === 'mis_pedidos'): ?>
        <?php include __DIR__ . '/../views/cliente/mis_pedidos.php'; ?>

    <?php elseif ($vista === 'mis_favoritos'): ?>
        <?php include __DIR__ . '/../views/cliente/mis_favoritos.php'; ?>

  <?php else: ?>

    <!-- ==============================
         SECCIÓN HERO / BANNER
    ============================== -->
    <div class="hero-banner">
        <div class="hero-content">
            <p class="hero-sub">Tecnología para tu hogar y negocio</p>
            <h1 class="hero-title">Los mejores productos<br>al mejor precio</h1>
            <a href="#productos" class="btn-hero">Ver catálogo</a>
        </div>
    </div>

    <!-- ==============================
         LAYOUT: FILTROS + PRODUCTOS
    ============================== -->
    <div class="container-xl py-5" id="productos">
    <div class="row g-4">

        <!-- =====================
             PRODUCTOS (ancho completo)
        ===================== -->
        <div class="col-12">

            <!-- Barra superior: título + búsqueda + orden -->
            <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
                <div class="section-header" style="flex:1;">
                    <h2 class="section-title">
                        <?php if (!empty($filtroOfertas)): ?>
                            🔥 Ofertas
                        <?php elseif (!empty($filtroCategoria)): ?>
                            <?php
                            $catActual = array_filter($categorias ?? [], fn($c) => $c['id_categoria'] == $filtroCategoria);
                            $catActual = reset($catActual);
                            echo '📂 ' . htmlspecialchars($catActual['nombre_categoria'] ?? 'Categoría');
                            ?>
                        <?php elseif (!empty($filtroBuscar)): ?>
                            🔍 "<?= htmlspecialchars($filtroBuscar) ?>"
                        <?php else: ?>
                            Productos destacados
                        <?php endif; ?>
                    </h2>
                    <span class="section-line"></span>
                    <span class="text-muted ms-2" style="font-size:13px;white-space:nowrap;">
                        <?= count($productos) ?> <?= count($productos) === 1 ? 'resultado' : 'resultados' ?>
                    </span>
                </div>

                <!-- Controles: búsqueda + orden + precio -->
                <form method="GET" action="index.php"
                      class="d-flex align-items-center gap-2 flex-wrap">

                    <?php if (!empty($filtroCategoria)): ?>
                        <input type="hidden" name="categoria" value="<?= htmlspecialchars($filtroCategoria) ?>">
                    <?php endif; ?>

                    <!-- Búsqueda -->
                    <input type="text" name="buscar"
                           class="form-control form-control-sm"
                           style="width:180px;border-radius:20px;"
                           placeholder="Buscar..."
                           value="<?= htmlspecialchars($filtroBuscar ?? '') ?>">

                    <!-- Precio mín/máx -->
                    <input type="number" name="precio_min"
                           class="form-control form-control-sm"
                           style="width:100px;border-radius:20px;"
                           placeholder="$ Mín"
                           value="<?= htmlspecialchars($filtroPrecioMin ?? '') ?>">

                    <input type="number" name="precio_max"
                           class="form-control form-control-sm"
                           style="width:100px;border-radius:20px;"
                           placeholder="$ Máx"
                           value="<?= htmlspecialchars($filtroPrecioMax ?? '') ?>">

                    <!-- Ordenar -->
                    <select name="orden" class="form-select form-select-sm"
                            style="width:160px;border-radius:20px;">
                        <option value="nuevo"       <?= ($filtroOrden === 'nuevo'       || !$filtroOrden) ? 'selected' : '' ?>>🆕 Más recientes</option>
                        <option value="precio_asc"  <?= ($filtroOrden === 'precio_asc')  ? 'selected' : '' ?>>💲 Menor precio</option>
                        <option value="precio_desc" <?= ($filtroOrden === 'precio_desc') ? 'selected' : '' ?>>💲 Mayor precio</option>
                        <option value="nombre_asc"  <?= ($filtroOrden === 'nombre_asc')  ? 'selected' : '' ?>>🔤 A → Z</option>
                    </select>

                    <button type="submit" class="btn btn-sm btn-dark" style="border-radius:20px;padding:6px 16px;">
                        Filtrar
                    </button>

                    <?php if ($filtroCategoria || $filtroBuscar || $filtroPrecioMin || $filtroPrecioMax || $filtroOrden): ?>
                        <a href="index.php" class="btn btn-sm btn-outline-secondary" style="border-radius:20px;">
                            ✕ Limpiar
                        </a>
                    <?php endif; ?>

                </form>
            </div>

            <?php if (!empty($productos)): ?>

            <div class="products-grid">
                <?php foreach ($productos as $p): ?>
                    <?php
                    $imagenes = $product->obtenerImagenes($p['id_producto']);
                    $img      = !empty($imagenes) ? $imagenes[0]['imagen'] : null;
                    $agotado  = $p['stock'] <= 0;
                    ?>

                    <div class="pcard <?= $agotado ? 'pcard--agotado' : '' ?>">

                        <div class="pcard__badge">
                            <?= htmlspecialchars($p['nombre_categoria'] ?? 'General') ?>
                        </div>

                        <!-- BOTÓN FAVORITO -->
                        <?php
                        $esFavorito = in_array($p['id_producto'], $favoritoIds ?? []);
                        ?>
                        <button class="btn-favorito <?= $esFavorito ? 'activo' : '' ?>"
                                onclick="toggleFavorito(this, <?= $p['id_producto'] ?>)"
                                title="<?= $esFavorito ? 'Quitar de favoritos' : 'Agregar a favoritos' ?>">
                            <span class="corazon"><?= $esFavorito ? '♥' : '♡' ?></span>
                        </button>

                        <a href="index.php?action=detalle_producto&id=<?= $p['id_producto'] ?>"
                           class="pcard__img-wrap">
                            <?php if ($img): ?>
                                <img src="/power-net/public/uploads/<?= htmlspecialchars($img) ?>"
                                     alt="<?= htmlspecialchars($p['nombre']) ?>"
                                     class="pcard__img">
                            <?php else: ?>
                                <img src="/power-net/img/logo.jpg" alt="Sin imagen" class="pcard__img">
                            <?php endif; ?>
                            <?php if ($agotado): ?>
                                <div class="pcard__agotado-overlay">Agotado</div>
                            <?php endif; ?>
                            <?php if (!empty($p['precio_oferta'])): ?>
                                <?php $desc = $p['descuento'] ?: round((1 - $p['precio_oferta'] / $p['precio']) * 100); ?>
                                <div style="position:absolute;top:10px;right:10px;background:#dc2626;color:#fff;font-size:11px;font-weight:800;padding:3px 8px;border-radius:20px;">
                                    -<?= $desc ?>%
                                </div>
                            <?php endif; ?>
                        </a>

                        <div class="pcard__body">
                            <a href="index.php?action=detalle_producto&id=<?= $p['id_producto'] ?>"
                               class="pcard__name">
                                <?= htmlspecialchars($p['nombre']) ?>
                            </a>
                            <p class="pcard__desc"><?= htmlspecialchars($p['descripcion']) ?></p>

                            <?php if (!empty($p['precio_oferta'])): ?>
                                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                                    <span style="font-size:20px;font-weight:900;color:#dc2626;">
                                        $<?= number_format($p['precio_oferta'], 0, ',', '.') ?>
                                    </span>
                                    <span style="font-size:13px;color:#9ca3af;text-decoration:line-through;">
                                        $<?= number_format($p['precio'], 0, ',', '.') ?>
                                    </span>
                                </div>
                            <?php else: ?>
                                <div class="pcard__price">$<?= number_format($p['precio'], 0, ',', '.') ?></div>
                            <?php endif; ?>

                            <?php if (!$agotado): ?>
                                <div class="pcard__qty" onclick="event.stopPropagation()">
                                    <button type="button" class="pcard__qty-btn"
                                            onclick="cambiarCantidad(this, -1)">−</button>
                                    <input type="number" value="1" min="1"
                                           max="<?= $p['stock'] ?>"
                                           class="pcard__qty-input cantidad-input" readonly>
                                    <button type="button" class="pcard__qty-btn"
                                            onclick="cambiarCantidad(this, 1)">+</button>
                                </div>

                                <div class="pcard__actions">
                                    <?php if (!isset($_SESSION['usuario'])): ?>
                                        <button class="pcard__btn pcard__btn--buy" onclick="abrirLogin()">
                                            Comprar
                                        </button>
                                    <?php else: ?>
                                        <form method="POST" action="index.php?action=agregar_carrito" style="flex:1">
                                            <input type="hidden" name="id_producto" value="<?= $p['id_producto'] ?>">
                                            <input type="hidden" name="cantidad" value="1" class="cantidad-hidden-buy">
                                            <input type="hidden" name="ir_a_pago" value="1">
                                            <button type="submit" class="pcard__btn pcard__btn--buy w-100">
                                                Comprar
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <form method="POST" action="index.php?action=agregar_carrito">
                                        <input type="hidden" name="id_producto" value="<?= $p['id_producto'] ?>">
                                        <input type="hidden" name="cantidad" value="1" class="cantidad-hidden">
                                        <button type="submit" class="pcard__btn pcard__btn--cart" title="Agregar al carrito">
                                            🛒
                                        </button>
                                    </form>
                                </div>

                                <div class="pcard__stock"><?= $p['stock'] ?> disponibles</div>
                            <?php else: ?>
                                <button class="pcard__btn pcard__btn--disabled w-100 mt-2" disabled>Sin stock</button>
                            <?php endif; ?>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

            <?php else: ?>
                <div class="text-center py-5">
                    <div style="font-size:4rem;">🔍</div>
                    <h5 class="mt-3 text-muted">No se encontraron productos</h5>
                    <p class="text-muted">Intenta con otros filtros o
                        <a href="index.php" class="text-decoration-none" style="color:#7c3aed;">ver todos</a>
                    </p>
                </div>
            <?php endif; ?>

        </div><!-- /col productos -->
    </div><!-- /row -->
    </div><!-- /container -->

<?php endif; ?>  

<!-- MODAL LOGIN -->
<div class="modal fade" id="loginModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <div class="modal-body">
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
<style>
/* ============================================
   RESET Y BASE
============================================ */
*, *::before, *::after { box-sizing: border-box; }

body {
    font-family: 'Inter', 'Segoe UI', sans-serif;
    background: #f0f2f5;
    color: #1a1a2e;
}

/* ============================================
   HERO BANNER
============================================ */
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
    color: #ffffff;
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

/* ============================================
   SECCIÓN TÍTULO
============================================ */
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

/* ============================================
   GRID DE PRODUCTOS
   Auto-fill: mínimo 220px, máximo 1fr
   Se adapta solo a cualquier pantalla
============================================ */
.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 24px;
}

/* ============================================
   CARD DE PRODUCTO
============================================ */
.pcard {
    background: #ffffff;
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

/* Badge de categoría */
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

/* Contenedor imagen */
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

/* Overlay agotado */
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

/* Cuerpo de la card */
.pcard__body {
    padding: 16px;
    display: flex;
    flex-direction: column;
    flex: 1;
    gap: 6px;
}

/* Nombre del producto */
.pcard__name {
    font-size: 14px;
    font-weight: 700;
    color: #1a1a2e;
    text-decoration: none;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.2s;
}

.pcard__name:hover {
    color: #7c3aed;
}

/* Descripción corta */
.pcard__desc {
    font-size: 12px;
    color: #6b7280;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin: 0;
}

/* Precio */
.pcard__price {
    font-size: 22px;
    font-weight: 800;
    color: #7c3aed;
    margin-top: 4px;
}

/* Stock disponible */
.pcard__stock {
    font-size: 11px;
    color: #10b981;
    font-weight: 600;
    margin-top: 2px;
}

/* ============================================
   SELECTOR DE CANTIDAD
============================================ */
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

/* ============================================
   BOTONES DE ACCIÓN
============================================ */
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

/* Botón Comprar */
.pcard__btn--buy {
    background: #1a1a2e;
    color: #fff;
}

.pcard__btn--buy:hover {
    background: #7c3aed;
    color: #fff;
}

/* Botón Carrito */
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

/* Botón deshabilitado */
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

/* ============================================
   RESPONSIVE
============================================ */
@media (max-width: 576px) {
    .hero-banner { padding: 40px 20px; min-height: 220px; }
    .products-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
    .pcard__img-wrap { height: 150px; }
    .pcard__price { font-size: 18px; }
}

/* ============================================
   BOTÓN FAVORITO
============================================ */
.btn-favorito {
    position: absolute;
    top: 10px;
    left: 10px;
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 50%;
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(4px);
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
    font-style: normal;
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
.filter-card {
    background: #fff;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.filter-title {
    font-size: 13px;
    font-weight: 800;
    color: #1a1a2e;
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f3f4f6;
}

.filter-cat-item {
    display: block;
    padding: 9px 12px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    text-decoration: none;
    transition: all .15s;
    margin-bottom: 4px;
}

.filter-cat-item:hover {
    background: #f3f4f6;
    color: #7c3aed;
}

.filter-cat-item.active {
    background: #7c3aed;
    color: #fff;
    font-weight: 700;
}

.filter-label {
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    display: block;
    margin-bottom: 4px;
}

.filter-input {
    width: 100%;
    padding: 8px 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    outline: none;
    transition: border-color .2s;
}

.filter-input:focus { border-color: #7c3aed; }

.filter-btn-apply {
    width: 100%;
    padding: 10px;
    background: #7c3aed;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    transition: background .2s;
    margin-bottom: 8px;
}

.filter-btn-apply:hover { background: #6d28d9; }

.filter-btn-clear {
    display: block;
    text-align: center;
    font-size: 13px;
    color: #9ca3af;
    text-decoration: none;
    transition: color .2s;
}

.filter-btn-clear:hover { color: #dc2626; }
</style>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_SESSION['alert'])): ?>
<script>
    Swal.fire({
        icon: '<?= $_SESSION['alert']['icon'] ?>',
        title: '<?= $_SESSION['alert']['title'] ?>',
        text: '<?= $_SESSION['alert']['text'] ?>'
    });
</script>
<?php unset($_SESSION['alert']); ?>
<?php endif; ?>

<script>
document.getElementById('abrirRecuperar')?.addEventListener('click', function(e) {
    e.preventDefault();

    const loginElement = document.getElementById('loginModal');
    const recuperarElement = document.getElementById('recuperarModal');

    const loginModal = bootstrap.Modal.getInstance(loginElement) || new bootstrap.Modal(loginElement);

    loginModal.hide();

    loginElement.addEventListener('hidden.bs.modal', function () {
        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';

        const recuperarModal = new bootstrap.Modal(recuperarElement);
        recuperarModal.show();
    }, { once: true });
});
</script>
<script>
function cambiarCantidad(btn, cambio) {
    const box   = btn.closest('.pcard__qty');
    const input = box.querySelector('.cantidad-input');
    const body  = btn.closest('.pcard__body');

    let cantidad = parseInt(input.value);
    let min      = parseInt(input.min);
    let max      = parseInt(input.max);

    cantidad += cambio;
    if (cantidad < min) cantidad = min;
    if (cantidad > max) cantidad = max;

    input.value = cantidad;

    // Sincroniza TODOS los inputs hidden de cantidad dentro de la card
    // .cantidad-hidden  → botón 🛒 agregar al carrito
    // .cantidad-hidden-buy → botón Comprar (ir directo al pago)
    body.querySelectorAll('.cantidad-hidden, .cantidad-hidden-buy').forEach(h => {
        h.value = cantidad;
    });
}
</script>
<script>
function abrirLogin(){
    new bootstrap.Modal(document.getElementById('loginModal')).show();
}

// Toggle favorito con fetch (sin recargar la página)
function toggleFavorito(btn, idProducto) {

    // Si no está logueado, abrir modal de login
    if (!document.querySelector('[data-logueado]')) {
        abrirLogin();
        return;
    }

    const formData = new FormData();
    formData.append('id_producto', idProducto);

    fetch('index.php?action=toggle_favorito', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.error === 'no_auth') {
            abrirLogin();
            return;
        }
        const corazon = btn.querySelector('.corazon');
        if (data.favorito) {
            corazon.textContent = '♥';
            btn.classList.add('activo');
            btn.title = 'Quitar de favoritos';
        } else {
            corazon.textContent = '♡';
            btn.classList.remove('activo');
            btn.title = 'Agregar a favoritos';
        }
    })
    .catch(() => abrirLogin());
}
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
