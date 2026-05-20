<?php

/**
 * Enrutador principal de acciones web.
 * Se invoca desde public/index.php y recibe todas las dependencias por parámetro
 * para evitar variables "mágicas" y errores de análisis estático.
 */
function dispatchWebRoutes(
    string $action,
    ?string &$vista,
    ?int $id_usuario,
    UsuarioController $userController,
    ProductController $productController,
    CategoryController $categoryController,
    CartController $cartController,
    ClienteController $clienteCtrl,
    PagoController $pagoCtrl,
    DevolucionController $devCtrl,
    EnvioController $envCtrl,
    OfertaController $ofCtrl,
    ProveedorController $provCtrl,
    VentaController $ventaCtrl
): void {
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

    case 'eliminar_categoria':
        $categoryController->eliminar();
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
        $clienteCtrl->ofertas($vista);
        break;

    // =========================
    // ── Factura ──
    case 'factura':
        // Esta vista es un documento HTML completo, por eso se renderiza y termina aquí.
        $clienteCtrl->factura($vista, $id_usuario);
        $pedidoFac  = $GLOBALS['pedidoFac']  ?? null;
        $detalleFac = $GLOBALS['detalleFac'] ?? [];
        $clienteFac = $GLOBALS['clienteFac'] ?? [];
        $usuarioFac = $GLOBALS['usuarioFac'] ?? [];
        include __DIR__ . '/../views/cliente/factura.php';
        exit;

    // ── Detalle producto ──
    case 'detalle_producto':
        $clienteCtrl->detalleProducto($vista);
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
        require_once __DIR__ . '/../controllers/OrderController.php';
        (new OrderController())->guardar();
        break;
    case 'pedidos':
        require_once __DIR__ . '/../views/admin/pedidos/pedidos.php';
        exit;

    // ── Vistas admin ──
    case 'dashboard':
        require_once __DIR__ . '/../views/admin/dashboard.php';
        exit;

    case 'productos':
        require_once __DIR__ . '/../views/admin/productos/productos.php';
        exit;

    case 'categorias':
        require_once __DIR__ . '/../views/admin/categoria/categorias.php';
        exit;

    case 'inventario':
        require_once __DIR__ . '/../views/admin/inventario/inventario.php';
        exit;

    case 'envios':
        require_once __DIR__ . '/../views/admin/envios/envios.php';
        exit;

    case 'ventas':
        require_once __DIR__ . '/../views/admin/pago/ventas.php';
        exit;

    case 'devoluciones':
        require_once __DIR__ . '/../views/admin/pago/devolucion.php';
        exit;

    case 'pagos':
        require_once __DIR__ . '/../views/admin/pago/pago.php';
        exit;

    case 'guardar_metodo_config':
    case 'editar_metodo_config':
    case 'eliminar_metodo_config':
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 1) {
            header("Location: index.php"); exit;
        }
        $configPath = __DIR__ . '/../views/admin/pago/MetodosPago.php';
        $metodos    = require $configPath;

        if ($action === 'guardar_metodo_config') {
            $metodos[] = [
                'id'            => 'metodo_' . time(),
                'nombre'        => trim($_POST['nombre']        ?? ''),
                'icono'         => trim($_POST['icono']         ?? '💰'),
                'descripcion'   => trim($_POST['descripcion']   ?? ''),
                'instrucciones' => trim($_POST['instrucciones'] ?? ''),
                'activo'        => isset($_POST['activo']),
            ];
        } elseif ($action === 'editar_metodo_config') {
            $idx = (int)($_POST['indice'] ?? -1);
            if (isset($metodos[$idx])) {
                $metodos[$idx]['nombre']        = trim($_POST['nombre']        ?? '');
                $metodos[$idx]['icono']         = trim($_POST['icono']         ?? '💰');
                $metodos[$idx]['descripcion']   = trim($_POST['descripcion']   ?? '');
                $metodos[$idx]['instrucciones'] = trim($_POST['instrucciones'] ?? '');
                $metodos[$idx]['activo']        = isset($_POST['activo']);
            }
        } elseif ($action === 'eliminar_metodo_config') {
            $idx = (int)($_GET['indice'] ?? -1);
            if (isset($metodos[$idx])) {
                array_splice($metodos, $idx, 1);
            }
        }

        // Reescribir el archivo de config
        $php = "<?php\nreturn " . var_export($metodos, true) . ";\n";
        file_put_contents($configPath, $php);

        $_SESSION['alert'] = ['icon'=>'success','title'=>'Guardado','text'=>'Método de pago actualizado'];
        header("Location: index.php?action=pagos&tab=metodos"); exit;

    case 'proveedores':
        require_once __DIR__ . '/../views/admin/proveedores/proveedores.php';
        exit;

    case 'ofertas_admin':
        require_once __DIR__ . '/../views/admin/ofertas/ofertas.php';
        exit;

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
    case 'toggle_proveedor':
        match($action) {
            'guardar_proveedor'  => $provCtrl->guardar(),
            'editar_proveedor'   => $provCtrl->editar(),
            'eliminar_proveedor' => $provCtrl->eliminar(),
            'toggle_proveedor'   => $provCtrl->toggle(),
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
}

 