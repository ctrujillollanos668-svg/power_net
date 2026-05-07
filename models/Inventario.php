<?php
require_once __DIR__ . '/../config/Database.php';

class Inventario {

    private $conn;

    public function __construct() {
        $db         = new Database();
        $this->conn = $db->getConnection();
    }

    // Registra un movimiento de stock (entrada o salida)
    public function registrarMovimiento($id_producto, $tipo, $cantidad, $motivo = null, $id_pedido = null) {

        // Obtener stock actual antes del movimiento
        $stmtStock = $this->conn->prepare("SELECT stock FROM productos WHERE id_producto = ?");
        $stmtStock->execute([$id_producto]);
        $prod = $stmtStock->fetch(PDO::FETCH_ASSOC);

        if (!$prod) return false;

        $stock_anterior = (int)$prod['stock'];
        $stock_nuevo    = $tipo === 'entrada'
            ? $stock_anterior + $cantidad
            : $stock_anterior - $cantidad;

        $sql  = "INSERT INTO inventario
                 (id_producto, tipo, cantidad, stock_anterior, stock_nuevo, motivo, id_pedido)
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $id_producto,
            $tipo,
            $cantidad,
            $stock_anterior,
            $stock_nuevo,
            $motivo,
            $id_pedido
        ]);
    }

    // Registra una entrada de stock (admin agrega stock)
    public function entrada($id_producto, $cantidad, $motivo = 'ajuste_admin') {
        // Actualizar stock en productos
        $this->conn->prepare("UPDATE productos SET stock = stock + ? WHERE id_producto = ?")
                   ->execute([$cantidad, $id_producto]);

        // Si el producto estaba inactivo por stock 0, reactivarlo
        $this->conn->prepare(
            "UPDATE productos SET disponibilidad = 1 WHERE id_producto = ? AND stock > 0"
        )->execute([$id_producto]);

        return $this->registrarMovimiento($id_producto, 'entrada', $cantidad, $motivo);
    }

    // Registra una salida de stock (se llama al confirmar pago)
    public function salida($id_producto, $cantidad, $id_pedido = null) {
        return $this->registrarMovimiento($id_producto, 'salida', $cantidad, 'compra_cliente', $id_pedido);
    }

    // Trae todos los movimientos con nombre del producto
    public function obtenerMovimientos($limite = 100) {
        $sql  = "SELECT i.*,
                        p.nombre AS nombre_producto,
                        p.stock  AS stock_actual
                 FROM inventario i
                 INNER JOIN productos p ON i.id_producto = p.id_producto
                 ORDER BY i.id_inventario DESC
                 LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Trae movimientos de un producto específico
    public function obtenerPorProducto($id_producto) {
        $sql  = "SELECT * FROM inventario
                 WHERE id_producto = ?
                 ORDER BY id_inventario DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_producto]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Resumen de stock actual de todos los productos
    public function resumenStock() {
        $sql  = "SELECT p.id_producto, p.nombre, p.stock, p.precio, p.disponibilidad,
                        c.nombre_categoria,
                        (p.precio * p.stock) AS valor_inventario
                 FROM productos p
                 LEFT JOIN categoria c ON p.id_categoria = c.id_categoria
                 ORDER BY p.stock ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
