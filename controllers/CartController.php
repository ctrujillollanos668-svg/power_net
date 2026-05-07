<?php

// 📦 Modelos necesarios
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Product.php';

class CartController {

    // =========================================================
    // 🛒 AGREGAR PRODUCTO AL CARRITO
    // =========================================================
    public function agregar() {

        $id       = $_POST['id_producto'] ?? null;
        $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;
        $irAPago  = $_POST['ir_a_pago'] ?? null; // viene del botón "Comprar ahora"

        if ($id) {
            for ($i = 0; $i < $cantidad; $i++) {
                Cart::agregar($id);
            }
        }

        // Si viene del botón "Comprar ahora", redirige directo al checkout
        if ($irAPago) {
            header("Location: index.php?action=procesar_pago");
            exit;
        }

        $back = $_SERVER['HTTP_REFERER'] ?? 'index.php';
        header("Location: $back");
        exit;
    }

    // =========================================================
    // ➕ AUMENTAR CANTIDAD EN CARRITO
    // =========================================================
    public function aumentar() {

        $id = $_GET['id'] ?? null;

        if ($id) {
            Cart::aumentar($id);
        }

        header("Location: index.php?action=carrito");
        exit;
    }

    // =========================================================
    // ➖ DISMINUIR CANTIDAD EN CARRITO
    // =========================================================
    public function disminuir() {

        $id = $_GET['id'] ?? null;

        if ($id) {
            Cart::disminuir($id);
        }

        header("Location: index.php?action=carrito");
        exit;
    }

    // =========================================================
    // 🗑 ELIMINAR PRODUCTO DEL CARRITO
    // =========================================================
    public function eliminar() {

        $id = $_GET['id'] ?? null;

        if ($id) {
            Cart::eliminar($id);
        }

        header("Location: index.php?action=carrito");
        exit;
    }

    // =========================================================
    // 🧹 VACIAR TODO EL CARRITO
    // =========================================================
    public function vaciar() {

        Cart::vaciar();

        header("Location: index.php?action=carrito");
        exit;
    }

    // =========================================================
    // 📄 OBTENER DATOS COMPLETOS PARA LA VISTA CARRITO
    // =========================================================
    public function ver() {

        $productModel = new Product();

        // 🛒 carrito en sesión
        $carrito = Cart::obtener();

        $productos = [];
        $totalGeneral = 0;

        // 🔁 recorrer carrito
        foreach ($carrito as $id => $cantidad) {

            // 🔎 obtener producto
            $producto = $productModel->obtenerPorId($id);

            if (!$producto) continue;

            // 🧮 calcular subtotal
            $subtotal = $producto['precio'] * $cantidad;
            $totalGeneral += $subtotal;

            // 🖼 imagen principal
            $imagenes = $productModel->obtenerImagenes($id);
            $img = !empty($imagenes) ? $imagenes[0]['imagen'] : null;

            // 📦 estructura que usa la vista
            $productos[] = [
                'id' => $id,
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'cantidad' => $cantidad,
                'subtotal' => $subtotal,
                'imagen' => $img
            ];
        }

        // 📄 cargar vista carrito
        require __DIR__ . '/../views/cliente/carrito.php';
    }
    // 💳 MOSTRAR PÁGINA DE PROCESAR PAGO
public function checkout() {

    $carrito = Cart::obtener(); // [id => cantidad]

    $productModel = new Product();

    $productos = [];
    $totalGeneral = 0;

    foreach ($carrito as $id => $cantidad) {

        $p = $productModel->obtenerPorId($id);
        if (!$p) continue;

        $precio = $p['precio'];
        $subtotal = $precio * $cantidad;

        $totalGeneral += $subtotal;

        $imagenes = $productModel->obtenerImagenes($id);
        $imagen = $imagenes[0]['imagen'] ?? null;

        $productos[] = [
            'id' => $id,
            'nombre' => $p['nombre'],
            'precio' => $precio,
            'cantidad' => $cantidad,
            'subtotal' => $subtotal,
            'imagen' => $imagen
        ];
    }

    require __DIR__ . '/../views/cliente/procesar_pago.php';
}
}