<?php
require_once __DIR__ . '/../config/Database.php';

// Pedido.php → usado por el flujo del cliente (checkout, mis pedidos)
// Order.php  → usado por el panel admin
class Pedido {

    private $db;

    public function __construct() {
        $database  = new Database();
        $this->db  = $database->getConnection();
    }

    // Crea un pedido nuevo. Recibe array con cliente_id, total, estado.
    // Retorna el id_pedido generado (necesario para guardar detalle y pago).
    // fecha_pedido = NOW() para que nunca quede NULL en la BD.
    public function crear($data) {
        $sql  = "INSERT INTO pedido (fecha_pedido, id_cliente, total_pedido, estado_pedido)
                 VALUES (NOW(), :id_cliente, :total_pedido, :estado_pedido)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_cliente',    $data['cliente_id']);
        $stmt->bindParam(':total_pedido',  $data['total']);
        $stmt->bindParam(':estado_pedido', $data['estado']);
        $stmt->execute();
        return $this->db->lastInsertId();
    }

    // Inserta una línea de detalle por cada producto del carrito.
    // $item debe tener: id, precio, cantidad.
    public function guardarDetalle($id_pedido, $item) {
        $precio   = $item['precio'];
        $cantidad = $item['cantidad'];
        $subtotal = $precio * $cantidad;

        $sql  = "INSERT INTO detalle_pedido
                 (id_pedido, id_producto, precio_unitario, cantidad, subtotal)
                 VALUES (:id_pedido, :id_producto, :precio_unitario, :cantidad, :subtotal)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_pedido',       $id_pedido);
        $stmt->bindParam(':id_producto',     $item['id']);
        $stmt->bindParam(':precio_unitario', $precio);
        $stmt->bindParam(':cantidad',        $cantidad);
        $stmt->bindParam(':subtotal',        $subtotal);
        return $stmt->execute();
    }

    // Trae todos los pedidos del cliente con su último pago asociado.
    // Usa subconsulta correlacionada en lugar de GROUP BY para evitar
    // el error only_full_group_by que tiene MySQL activado por defecto.
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
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_cliente', $id_cliente);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Trae los productos de un pedido específico.
    // Se usa en mis_pedidos para mostrar el desglose al hacer clic en "Ver".
    public function obtenerDetalle($id_pedido) {
        $sql  = "SELECT dp.*, pr.nombre
                 FROM detalle_pedido dp
                 LEFT JOIN productos pr ON dp.id_producto = pr.id_producto
                 WHERE dp.id_pedido = :id_pedido";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id_pedido', $id_pedido);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
