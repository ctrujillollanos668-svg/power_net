<?php
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/MetodoPago.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Favorito.php';
require_once __DIR__ . '/../models/Devolucion.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Product.php';

class ClienteController {

    private $clienteModel;

    public function __construct() {
        $this->clienteModel = new Cliente();
    }

    // Prepara datos para la vista medios_pago
    public function mediosPago(&$vista, $id_usuario) {
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['open_login'] = true;
            header("Location: index.php"); exit;
        }
        $clienteMedios = $this->clienteModel->obtenerPorUsuario($id_usuario);
        $id_cli_medios = $clienteMedios['id_cliente'] ?? null;
        $GLOBALS['metodosMedios'] = $id_cli_medios ? (new MetodoPago())->obtenerPorCliente($id_cli_medios) : [];
        $GLOBALS['clienteMedios'] = $clienteMedios;
        $vista = 'medios_pago';
    }

    // Prepara datos para la vista mis_pedidos
    public function misPedidos(&$vista, $id_usuario) {
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['open_login'] = true;
            header("Location: index.php"); exit;
        }
        $pedidoModel = new Pedido();
        $clienteMis  = $this->clienteModel->obtenerPorUsuario($id_usuario);
        $id_cli_mis  = $clienteMis['id_cliente'] ?? null;
        $GLOBALS['pedidosMis']    = $id_cli_mis ? $pedidoModel->obtenerPorCliente($id_cli_mis) : [];
        $GLOBALS['pedidoMisModel']= $pedidoModel;
        $vista = 'mis_pedidos';
    }

    // Prepara datos para la vista mis_favoritos
    public function misFavoritos(&$vista, $id_usuario) {
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['open_login'] = true;
            header("Location: index.php"); exit;
        }
        $favModel = new Favorito();
        $GLOBALS['favoritosMis']   = $favModel->obtenerProductosPorUsuario($id_usuario);
        $GLOBALS['favoritoIdsMis'] = array_column($GLOBALS['favoritosMis'], 'id_producto');
        $GLOBALS['prodFavModel']   = new Product();
        $vista = 'mis_favoritos';
    }

    // Prepara datos para la vista mis_devoluciones
    public function misDevoluciones(&$vista, $id_usuario) {
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['open_login'] = true;
            header("Location: index.php"); exit;
        }
        $devModel     = new Devolucion();
        $clienteDevCli= $this->clienteModel->obtenerPorUsuario($id_usuario);
        $id_cli       = $clienteDevCli['id_cliente'] ?? null;
        $GLOBALS['devolucionesCli'] = $id_cli ? $devModel->obtenerPorClienteConEstado($id_cli) : [];
        $GLOBALS['devModelCli']     = $devModel;
        $vista = 'mis_devoluciones';
    }

    // Toggle favorito (responde JSON)
    public function toggleFavorito($id_usuario) {
        if (!isset($_SESSION['usuario'])) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'no_auth']); exit;
        }
        $id_prod = (int)($_POST['id_producto'] ?? 0);
        if ($id_prod) {
            $favModel = new Favorito();
            $esFav    = $favModel->toggle($id_usuario, $id_prod);
            $total    = $favModel->contarPorProducto($id_prod);
            header('Content-Type: application/json');
            echo json_encode(['favorito' => $esFav, 'total' => $total]);
        }
        exit;
    }

    // Prepara datos para la vista procesar_pago
    public function procesarPago(&$vista, $id_usuario) {
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['open_login'] = true;
            header("Location: index.php"); exit;
        }
        $clientePago = $this->clienteModel->obtenerPorUsuario($id_usuario);
        if (!$clientePago) {
            $this->clienteModel->crearSiNoExiste($id_usuario);
            $clientePago = $this->clienteModel->obtenerPorUsuario($id_usuario);
        }
        $id_cliente_pago = $clientePago['id_cliente'] ?? null;
        $metodoModelPago = new MetodoPago();

        $GLOBALS['metodosPago']    = $id_cliente_pago ? $metodoModelPago->obtenerPorCliente($id_cliente_pago) : [];
        $GLOBALS['direccionPago']  = trim($clientePago['direccion'] ?? '');
        $GLOBALS['tieneDireccion'] = !empty($GLOBALS['direccionPago']);
        $GLOBALS['clientePago']    = $clientePago;

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
            $itemsCarritoPago[] = ['nombre'=>$p['nombre'],'precio'=>$p['precio'],'cantidad'=>$cantidad,'subtotal'=>$subtotal,'imagen'=>$img];
        }

        if (empty($itemsCarritoPago)) {
            $_SESSION['alert'] = ['icon'=>'warning','title'=>'Carrito vacío','text'=>'Agrega productos antes de pagar'];
            header("Location: index.php"); exit;
        }

        $GLOBALS['itemsCarritoPago'] = $itemsCarritoPago;
        $GLOBALS['totalCarritoPago'] = $totalCarritoPago;
        $vista = 'procesar_pago';
    }
}
