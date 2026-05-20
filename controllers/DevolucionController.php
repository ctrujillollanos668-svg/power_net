<?php
require_once __DIR__ . '/../models/Devolucion.php';
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/Cliente.php';

class DevolucionController {

    private $clienteModel;

    public function __construct() {
        $this->clienteModel = new Cliente();
    }

    // Muestra el formulario de solicitud de devolución
    public function solicitar() {
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['open_login'] = true;
            header("Location: index.php"); exit;
        }

        $id_pedido_dev = (int)($_GET['id'] ?? 0);
        if (!$id_pedido_dev) { header("Location: index.php?action=mis_pedidos"); exit; }

        $pedidoDevModel = new Pedido();
        $devModel       = new Devolucion();
        $clienteDev     = $this->clienteModel->obtenerPorUsuario($_SESSION['usuario']['id']);
        $id_cli_dev     = $clienteDev['id_cliente'] ?? null;

        $pedidosDev = $id_cli_dev ? $pedidoDevModel->obtenerPorCliente($id_cli_dev) : [];
        $pedidoDev  = null;
        foreach ($pedidosDev as $pd) {
            if ($pd['id_pedido'] == $id_pedido_dev) { $pedidoDev = $pd; break; }
        }

        if (!$pedidoDev) {
            $_SESSION['alert'] = ['icon'=>'error','title'=>'No autorizado','text'=>'Pedido no encontrado'];
            header("Location: index.php?action=mis_pedidos"); exit;
        }

        if (strtolower($pedidoDev['estado_pedido']) !== 'entregado') {
            $_SESSION['alert'] = ['icon'=>'warning','title'=>'No disponible','text'=>'Solo puedes solicitar devolución de pedidos entregados'];
            header("Location: index.php?action=mis_pedidos"); exit;
        }

        if ($devModel->existePorPedido($id_pedido_dev)) {
            $_SESSION['alert'] = ['icon'=>'info','title'=>'Ya existe','text'=>'Ya tienes una solicitud de devolución para este pedido'];
            header("Location: index.php?action=mis_pedidos"); exit;
        }

        $detalleDev = $pedidoDevModel->obtenerDetalle($id_pedido_dev);
        include __DIR__ . '/../views/cliente/solicitar_devolucion.php';
        exit;
    }

    // Procesa y guarda la solicitud de devolución
    public function procesar() {
        if (!isset($_SESSION['usuario'])) { header("Location: index.php"); exit; }

        $id_pedido_proc = (int)($_POST['id_pedido'] ?? 0);
        $motivo_general = trim($_POST['motivo_general'] ?? '');
        $productos_dev  = $_POST['productos'] ?? [];

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
            $_SESSION['alert'] = ['icon'=>'error','title'=>'Sin productos','text'=>'Selecciona al menos un producto'];
            header("Location: index.php?action=solicitar_devolucion&id=" . $id_pedido_proc); exit;
        }

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

        $_SESSION['alert'] = ['icon'=>'success','title'=>'¡Solicitud enviada!','text'=>'Tu solicitud #' . $id_dev . ' fue registrada. La revisaremos en 3-5 días hábiles.'];
        header("Location: index.php?action=mis_pedidos"); exit;
    }

    // Admin: aprobar devolución
    public function aprobar() {
        if (isset($_GET['id'])) {
            (new Devolucion())->cambiarEstado((int)$_GET['id'], 'aprobada');
            $_SESSION['alert'] = ['icon'=>'success','title'=>'Aprobada','text'=>'Devolución aprobada correctamente'];
        }
        header("Location: index.php?action=devoluciones"); exit;
    }

    // Admin: rechazar devolución
    public function rechazar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new Devolucion())->cambiarEstado(
                (int)$_POST['id_devolucion'], 'rechazada',
                trim($_POST['motivo_rechazo'] ?? '')
            );
            $_SESSION['alert'] = ['icon'=>'info','title'=>'Rechazada','text'=>'Devolución rechazada'];
        }
        header("Location: index.php?action=devoluciones"); exit;
    }

    // Admin: procesar reembolso
    public function reembolso() {
        if (isset($_GET['id'])) {
            (new Devolucion())->cambiarEstado((int)$_GET['id'], 'completada');
            $_SESSION['alert'] = ['icon'=>'success','title'=>'Reembolso procesado','text'=>'El reembolso fue registrado como completado'];
        }
        header("Location: index.php?action=devoluciones"); exit;
    }
}
