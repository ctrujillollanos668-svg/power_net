<?php
require_once __DIR__ . '/../config/Database.php';

// Order.php → usado por el panel ADMIN
// Pedido.php → usado por el cliente
// Ambos apuntan a la misma tabla 'pedido' en la BD
class Order {

    private $conn;

    public function __construct() {
        $database   = new Database();
        $this->conn = $database->getConnection();
    }

    // Crea un pedido nuevo. Retorna el id_pedido generado.
    public function crearPedido($id_cliente, $total) {
        $sql  = "INSERT INTO pedido (fecha_pedido, total_pedido, estado_pedido, id_cliente)
                 VALUES (NOW(), :total, 'pendiente', :cliente)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':total',   $total);
        $stmt->bindParam(':cliente', $id_cliente);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    // Inserta una línea de detalle por cada producto del carrito
    public function agregarDetalle($id_pedido, $id_producto, $precio, $cantidad) {
        $subtotal = $precio * $cantidad;
        $sql  = "INSERT INTO detalle_pedido
                 (id_pedido, id_producto, precio_unitario, cantidad, subtotal)
                 VALUES (:pedido, :producto, :precio, :cantidad, :subtotal)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':pedido',   $id_pedido);
        $stmt->bindParam(':producto', $id_producto);
        $stmt->bindParam(':precio',   $precio);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':subtotal', $subtotal);
        return $stmt->execute();
    }

    // Lista todos los pedidos para el panel admin con nombre del cliente y datos del pago
    public function obtenerPedidos() {
        $sql  = "SELECT p.*,
                        pe.nombre_persona AS nombre_cliente,
                        pg.estado_pago,
                        pg.metodo_pago,
                        pg.factura
                 FROM pedido p
                 LEFT JOIN cliente  c  ON p.id_cliente  = c.id_cliente
                 LEFT JOIN persona  pe ON c.id_persona  = pe.id_persona
                 LEFT JOIN pago     pg ON pg.id_pago    = (
                     SELECT id_pago FROM pago
                     WHERE id_pedido = p.id_pedido
                     ORDER BY id_pago DESC LIMIT 1
                 )
                 ORDER BY p.id_pedido DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Pedidos de un cliente específico con su último pago asociado.
    // Usa subconsulta en lugar de GROUP BY para evitar el error only_full_group_by de MySQL.
    public function obtenerPorCliente($id_cliente) {
        $sql  = "SELECT p.*,
                        pg.estado_pago,
                        pg.metodo_pago,
                        pg.factura,
                        pg.fecha_pago
                 FROM pedido p
                 LEFT JOIN pago pg ON pg.id_pago = (
                     SELECT id_pago FROM pago
                     WHERE id_pedido = p.id_pedido
                     ORDER BY id_pago DESC LIMIT 1
                 )
                 WHERE p.id_cliente = :id_cliente
                 ORDER BY p.id_pedido DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_cliente', $id_cliente);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Detalle de un pedido: productos, cantidades, precios y subtotales
    public function obtenerDetalle($id_pedido) {
        $sql  = "SELECT dp.*, pr.nombre, pr.descripcion
                 FROM detalle_pedido dp
                 LEFT JOIN productos pr ON dp.id_producto = pr.id_producto
                 WHERE dp.id_pedido = :id_pedido";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_pedido', $id_pedido);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
