<?php
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/MetodoPago.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Inventario.php';
require_once __DIR__ . '/../models/Venta.php';
require_once __DIR__ . '/../config/Database.php';

class PagoController {

    private $clienteModel;

    public function __construct() {
        $this->clienteModel = new Cliente();
    }

    // ── Guardar método de pago ──
    public function guardarMetodo() {
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['open_login'] = true;
            header("Location: index.php"); exit;
        }

        $id_usuario = $_SESSION['usuario']['id'];
        $cliente    = $this->clienteModel->obtenerPorUsuario($id_usuario);

        if (!$cliente) {
            $this->clienteModel->crearSiNoExiste($id_usuario);
            $cliente = $this->clienteModel->obtenerPorUsuario($id_usuario);
        }

        $id_cliente = $cliente['id_cliente'] ?? null;

        if (!$id_cliente) {
            $_SESSION['alert'] = ['icon'=>'error','title'=>'Error de cuenta','text'=>'No se pudo vincular tu cuenta.'];
            header("Location: index.php?action=procesar_pago"); exit;
        }

        $tipo    = $_POST['tipo']    ?? '';
        $numero  = trim($_POST['numero']  ?? '');
        $titular = trim($_POST['titular'] ?? '');

        if (empty($numero)) {
            $_SESSION['alert'] = ['icon'=>'error','title'=>'Falta el número','text'=>'Ingresa el número de tarjeta o cuenta'];
            header("Location: index.php?action=procesar_pago"); exit;
        }
        if (empty($titular)) {
            $_SESSION['alert'] = ['icon'=>'error','title'=>'Falta el titular','text'=>'Ingresa el nombre del titular'];
            header("Location: index.php?action=procesar_pago"); exit;
        }

        $metodoModel = new MetodoPago();
        $ok = $metodoModel->guardar($id_cliente, $tipo, $numero, $titular);

        if (!$ok) {
            $_SESSION['alert'] = ['icon'=>'error','title'=>'Error al guardar','text'=>'No se pudo guardar el método. Intenta de nuevo.'];
            header("Location: index.php?action=procesar_pago"); exit;
        }

        $_SESSION['alert'] = ['icon'=>'success','title'=>'Guardado','text'=>'Método de pago guardado correctamente'];

        $redirect = $_POST['redirect'] ?? 'procesar_pago';
        $allowed  = ['medios_pago','procesar_pago','mi_perfil_metodos'];
        $redirect = in_array($redirect, $allowed) ? $redirect : 'procesar_pago';

        if ($redirect === 'mi_perfil_metodos') {
            header("Location: index.php?action=mi_perfil&tab=metodos");
        } else {
            header("Location: index.php?action=" . $redirect);
        }
        exit;
    }

    // ── Editar método de pago ──
    public function editarMetodo() {
        if (!isset($_SESSION['usuario'])) { header("Location: index.php"); exit; }

        $id_metodo_edit  = (int)($_POST['id_metodo'] ?? 0);
        $tipo_edit       = trim($_POST['tipo']    ?? '');
        $numero_edit     = trim($_POST['numero']  ?? '');
        $titular_edit    = trim($_POST['titular'] ?? '');
        $metodoEditModel = new MetodoPago();

        if ($id_metodo_edit && $numero_edit && $titular_edit) {
            $clienteEdit     = $this->clienteModel->obtenerPorUsuario($_SESSION['usuario']['id']);
            $id_cliente_edit = $clienteEdit['id_cliente'] ?? null;
            $metodoEdit      = $metodoEditModel->obtenerPorId($id_metodo_edit);

            if ($metodoEdit && $metodoEdit['id_cliente'] == $id_cliente_edit) {
                $metodoEditModel->actualizar($id_metodo_edit, $tipo_edit, $numero_edit, $titular_edit);
                $_SESSION['alert'] = ['icon'=>'success','title'=>'Actualizado','text'=>'Método de pago actualizado'];
            }
        }

        $redirectEdit = $_POST['redirect'] ?? 'procesar_pago';
        if ($redirectEdit === 'mi_perfil_metodos') {
            header("Location: index.php?action=mi_perfil&tab=metodos");
        } else {
            header("Location: index.php?action=procesar_pago");
        }
        exit;
    }

    // ── Eliminar método de pago ──
    public function eliminarMetodo() {
        if (!isset($_SESSION['usuario'])) { header("Location: index.php"); exit; }

        $id_metodo_del  = (int)($_GET['id'] ?? 0);
        $metodoDelModel = new MetodoPago();

        if ($id_metodo_del) {
            $clienteDel     = $this->clienteModel->obtenerPorUsuario($_SESSION['usuario']['id']);
            $id_cliente_del = $clienteDel['id_cliente'] ?? null;
            $metodoDel      = $metodoDelModel->obtenerPorId($id_metodo_del);

            if ($metodoDel && $metodoDel['id_cliente'] == $id_cliente_del) {
                $metodoDelModel->eliminar($id_metodo_del);
                $_SESSION['alert'] = ['icon'=>'success','title'=>'Eliminado','text'=>'Método de pago eliminado'];
            }
        }

        $redirectDel = $_GET['redirect'] ?? 'procesar_pago';
        if ($redirectDel === 'mi_perfil_metodos') {
            header("Location: index.php?action=mi_perfil&tab=metodos");
        } else {
            header("Location: index.php?action=procesar_pago");
        }
        exit;
    }

    // ── Guardar dirección ──
    public function guardarDireccion() {
        if (!isset($_SESSION['usuario'])) { header("Location: index.php"); exit; }

        $id_usuario = $_SESSION['usuario']['id'];
        $clienteDir = $this->clienteModel->obtenerPorUsuario($id_usuario);

        if (!$clienteDir) {
            $this->clienteModel->crearSiNoExiste($id_usuario);
            $clienteDir = $this->clienteModel->obtenerPorUsuario($id_usuario);
        }

        $id_cliente_dir = $clienteDir['id_cliente'] ?? null;
        $direccion_dir  = trim($_POST['direccion']    ?? '');
        $ciudad_dir     = trim($_POST['ciudad']       ?? '');
        $depto_dir      = trim($_POST['departamento'] ?? '');
        $telefono_dir   = trim($_POST['telefono']     ?? '');

        if ($id_cliente_dir && $direccion_dir) {
            $partes = array_filter([$direccion_dir, $ciudad_dir, $depto_dir]);
            $this->clienteModel->actualizarDireccion($id_cliente_dir, implode(', ', $partes), $telefono_dir);
            $_SESSION['alert'] = ['icon'=>'success','title'=>'Guardada','text'=>'Dirección guardada correctamente'];
        } else {
            $_SESSION['alert'] = ['icon'=>'error','title'=>'Incompleto','text'=>'El campo dirección es obligatorio'];
        }

        $referer = $_SERVER['HTTP_REFERER'] ?? '';
        if (strpos($referer, 'tab=direccion') !== false || strpos($referer, 'mi_perfil') !== false) {
            header("Location: index.php?action=mi_perfil&tab=direccion");
        } else {
            header("Location: index.php?action=procesar_pago");
        }
        exit;
    }

    // ── Editar dirección ──
    public function editarDireccion() {
        if (!isset($_SESSION['usuario'])) { header("Location: index.php"); exit; }

        $clienteE     = $this->clienteModel->obtenerPorUsuario($_SESSION['usuario']['id']);
        $id_cliente_e = $clienteE['id_cliente'] ?? null;
        $direccion_e  = trim($_POST['direccion']    ?? '');
        $ciudad_e     = trim($_POST['ciudad']       ?? '');
        $depto_e      = trim($_POST['departamento'] ?? '');
        $telefono_e   = trim($_POST['telefono']     ?? '');

        if ($id_cliente_e && $direccion_e) {
            $partes = array_filter([$direccion_e, $ciudad_e, $depto_e]);
            $this->clienteModel->actualizarDireccion($id_cliente_e, implode(', ', $partes), $telefono_e);
            $_SESSION['alert'] = ['icon'=>'success','title'=>'Actualizada','text'=>'Dirección actualizada correctamente'];
        }

        header("Location: index.php?action=procesar_pago"); exit;
    }

    // ── Eliminar dirección ──
    public function eliminarDireccion() {
        if (!isset($_SESSION['usuario'])) { header("Location: index.php"); exit; }

        $clienteDel3     = $this->clienteModel->obtenerPorUsuario($_SESSION['usuario']['id']);
        $id_cliente_del3 = $clienteDel3['id_cliente'] ?? null;

        if ($id_cliente_del3) {
            $this->clienteModel->actualizarDireccion($id_cliente_del3, '', '');
            $_SESSION['alert'] = ['icon'=>'success','title'=>'Eliminada','text'=>'Dirección eliminada'];
        }

        $redirectDir = $_GET['redirect'] ?? 'procesar_pago';
        if ($redirectDir === 'mi_perfil_direccion') {
            header("Location: index.php?action=mi_perfil&tab=direccion");
        } else {
            header("Location: index.php?action=procesar_pago");
        }
        exit;
    }

    // ── Confirmar pago (transacción completa) ──
    public function confirmarPago() {
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['open_login'] = true;
            header("Location: index.php"); exit;
        }

        require_once __DIR__ . '/../models/Cart.php';

        $id_usuario = $_SESSION['usuario']['id'];
        $cliente    = $this->clienteModel->obtenerPorUsuario($id_usuario);

        if (!$cliente || empty($cliente['id_cliente'])) {
            $this->clienteModel->crearSiNoExiste($id_usuario);
            $cliente = $this->clienteModel->obtenerPorUsuario($id_usuario);
        }

        if (!$cliente || empty($cliente['id_cliente'])) {
            $_SESSION['alert'] = ['icon'=>'error','title'=>'Error de cuenta','text'=>'No se pudo vincular tu cuenta.'];
            header("Location: index.php?action=procesar_pago"); exit;
        }

        $id_cliente = $cliente['id_cliente'];

        // Validar dirección
        if (empty(trim($cliente['direccion'] ?? ''))) {
            $_SESSION['alert'] = ['icon'=>'warning','title'=>'Dirección requerida','text'=>'Debes agregar una dirección de envío antes de pagar'];
            header("Location: index.php?action=procesar_pago"); exit;
        }

        $id_metodo = $_POST['metodo_guardado'] ?? null;
        if (!$id_metodo) {
            $_SESSION['alert'] = ['icon'=>'error','title'=>'Sin método de pago','text'=>'Selecciona un método de pago'];
            header("Location: index.php?action=procesar_pago"); exit;
        }

        $cart = Cart::obtener();
        if (empty($cart)) {
            $_SESSION['alert'] = ['icon'=>'error','title'=>'Carrito vacío','text'=>'No hay productos en el carrito'];
            header("Location: index.php?action=carrito"); exit;
        }

        try {
            $db = new PDO(
                "mysql:host=localhost;dbname=powernet;charset=utf8",
                "root", "",
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]
            );

            $metodoModel    = new MetodoPago();
            $metodosCliente = $metodoModel->obtenerPorCliente($id_cliente);
            $tipoMetodo     = 'tarjeta';
            foreach ($metodosCliente as $m) {
                if ($m['id_metodo'] == $id_metodo) { $tipoMetodo = $m['tipo']; break; }
            }

            $total = 0; $items = []; $sinStock = [];

            foreach ($cart as $id_prod => $cantidad) {
                $stmtP = $db->prepare(
                    "SELECT p.id_producto, p.nombre, p.precio, p.stock, o.precio_oferta
                     FROM productos p
                     LEFT JOIN oferta o ON o.id_producto = p.id_producto
                                       AND o.estado = 1
                                       AND DATE(o.fecha_inicio) <= CURDATE()
                                       AND DATE(o.fecha_fin)    >= CURDATE()
                     WHERE p.id_producto = ? LIMIT 1"
                );
                $stmtP->execute([$id_prod]);
                $p = $stmtP->fetch(PDO::FETCH_ASSOC);
                if (!$p) continue;

                if ($p['stock'] < $cantidad) { $sinStock[] = $p['nombre']; continue; }

                $precioFinal = !empty($p['precio_oferta']) ? $p['precio_oferta'] : $p['precio'];
                $subtotal    = $precioFinal * $cantidad;
                $total      += $subtotal;
                $items[]     = ['id'=>$id_prod,'nombre'=>$p['nombre'],'cantidad'=>$cantidad,'precio'=>$precioFinal,'subtotal'=>$subtotal];
            }

            if (!empty($sinStock)) throw new Exception('Stock insuficiente para: ' . implode(', ', $sinStock));
            if (empty($items))     throw new Exception('No se encontraron productos válidos');

            $db->beginTransaction();

            // 1. Pedido
            $db->prepare("INSERT INTO pedido (fecha_pedido, total_pedido, estado_pedido, id_cliente) VALUES (NOW(), ?, 'pendiente', ?)")
               ->execute([$total, $id_cliente]);
            $id_pedido = $db->lastInsertId();

            // 2. Detalle + stock
            $stmtDet  = $db->prepare("INSERT INTO detalle_pedido (id_pedido, id_producto, precio_unitario, cantidad, subtotal) VALUES (?, ?, ?, ?, ?)");
            $stmtStk  = $db->prepare("UPDATE productos SET stock = stock - ? WHERE id_producto = ? AND stock >= ?");
            $stmtOcul = $db->prepare("UPDATE productos SET disponibilidad = 0 WHERE id_producto = ? AND stock = 0");

            foreach ($items as $item) {
                $stmtDet->execute([$id_pedido, $item['id'], $item['precio'], $item['cantidad'], $item['subtotal']]);
                $stmtStk->execute([$item['cantidad'], $item['id'], $item['cantidad']]);
                $stmtOcul->execute([$item['id']]);
            }

            // 3. Pago
            $factura = 'FAC-' . strtoupper(uniqid());
            $db->prepare("INSERT INTO pago (monto, metodo_pago, fecha_pago, factura, estado_pago, id_pedido) VALUES (?, ?, NOW(), ?, 'pagado', ?)")
               ->execute([$total, $tipoMetodo, $factura, $id_pedido]);

            $db->commit();

            // 4. Inventario + Venta
            $invModel   = new Inventario();
            $ventaModel = new Venta();
            foreach ($items as $item) { $invModel->salida($item['id'], $item['cantidad'], $id_pedido); }
            $ventaModel->registrar($id_pedido, $id_cliente, $total);

            Cart::vaciar();

            $_SESSION['pago_exitoso'] = ['id_pedido'=>$id_pedido,'factura'=>$factura,'total'=>$total];
            header("Location: index.php?action=pago_exitoso"); exit;

        } catch (Exception $e) {
            if (isset($db) && $db->inTransaction()) $db->rollBack();
            $_SESSION['alert'] = ['icon'=>'error','title'=>'Error al procesar el pago','text'=>$e->getMessage()];
            header("Location: index.php?action=procesar_pago"); exit;
        }
    }
}
