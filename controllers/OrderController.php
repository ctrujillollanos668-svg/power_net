<?php
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Cliente.php';
require_once __DIR__ . '/../models/MetodoPago.php';
require_once __DIR__ . '/../models/Cliente.php';
class OrderController {

    public function guardar() {

        if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
            echo "Carrito vacío";
            return;
        }

        if (!isset($_SESSION['usuario'])) {
            echo "Usuario no logueado";
            return;
        }

        $order = new Order();
        $product = new Product();
        $clienteModel = new Cliente();

        $carrito = $_SESSION['carrito'];

        // 🔥 OBTENER USUARIO
$id_usuario = $_SESSION['usuario']['id'];

// 🔍 BUSCAR CLIENTE CORRECTAMENTE
$cliente = $clienteModel->obtenerPorUsuario($id_usuario);

if (!$cliente) {
    echo "Cliente no encontrado";
    return;
}

$id_cliente = $cliente['id_cliente'];

        // 🔥 CALCULAR TOTAL
        foreach ($carrito as $id => $cantidad) {

            $p = $product->obtenerPorId($id);
            if (!$p) continue;

            $total += $p['precio'] * $cantidad;
        }

        // 🔥 CREAR PEDIDO (YA CON ID CORRECTO)
        $id_pedido = $order->crearPedido($id_cliente, $total);

        // 🔥 GUARDAR DETALLE
        foreach ($carrito as $id => $cantidad) {

            $p = $product->obtenerPorId($id);
            if (!$p) continue;

            $order->agregarDetalle(
                $id_pedido,
                $id,
                $p['precio'],
                $cantidad
            );

            // 🔥 DESCONTAR STOCK
            $product->descontarStock($id, $cantidad);
        }

        // 🧹 LIMPIAR CARRITO
        unset($_SESSION['carrito']);

        header("Location: /power-net/public/index.php?action=pedidos");
    }
  public function procesarPago() {

    if (!isset($_SESSION['usuario'])) {
        echo "No autorizado";
        return;
    }

    if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
        echo "Carrito vacío";
        return;
    }

    $tipo = $_POST['tipo'] ?? '';
    $numero = $_POST['numero'] ?? '';
    $titular = $_POST['titular'] ?? '';

    $clienteModel = new Cliente();
    $metodoModel = new MetodoPago();
    $order = new Order();
    $product = new Product();

    $id_usuario = $_SESSION['usuario']['id'];

    // 🔍 OBTENER CLIENTE
    $cliente = $clienteModel->obtenerPorUsuario($id_usuario);

    if (!$cliente) {
        echo "Cliente no encontrado";
        return;
    }

    $id_cliente = $cliente['id_cliente'];

    // 🔥 GUARDAR MÉTODO DE PAGO
    $metodoModel->guardar($id_cliente, $tipo, $numero, $titular);

    // 🔥 CREAR PEDIDO
    $carrito = $_SESSION['carrito'];
    $total = 0;

    foreach ($carrito as $id => $cantidad) {
        $p = $product->obtenerPorId($id);
        if (!$p) continue;

        $total += $p['precio'] * $cantidad;
    }

    $id_pedido = $order->crearPedido($id_cliente, $total);

    foreach ($carrito as $id => $cantidad) {
        $p = $product->obtenerPorId($id);
        if (!$p) continue;

        $order->agregarDetalle(
            $id_pedido,
            $id,
            $p['precio'],
            $cantidad
        );

        $product->descontarStock($id, $cantidad);
    }

    unset($_SESSION['carrito']);

  header("Location: index.php?action=medios_pago");
}
}