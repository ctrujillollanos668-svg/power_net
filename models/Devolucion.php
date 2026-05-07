<?php
require_once __DIR__ . '/../config/Database.php';

// Modelo adaptado a la estructura real de la BD:
// devolucion: id_devolucion, fecha_devolucion, monto_devolucion, id_pedido
// detalle_devolucion: id_detalle, id_devolucion, id_producto, cantidad, motivo
class Devolucion {

    private $conn;

    public function __construct() {
        $db         = new Database();
        $this->conn = $db->getConnection();
    }

    // Crea una devolución con su detalle de productos
    public function crear($id_pedido, $monto, $items, $motivo_general = null) {
        // Insertar cabecera — incluye motivo general si la columna existe
        try {
            $sql  = "INSERT INTO devolucion (fecha_devolucion, monto_devolucion, id_pedido, motivo)
                     VALUES (NOW(), ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$monto, $id_pedido, $motivo_general]);
        } catch (\PDOException $e) {
            // Si la columna motivo no existe aún, insertar sin ella
            $sql  = "INSERT INTO devolucion (fecha_devolucion, monto_devolucion, id_pedido)
                     VALUES (NOW(), ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$monto, $id_pedido]);
        }
        $id_devolucion = $this->conn->lastInsertId();

        // Insertar detalle por cada producto seleccionado
        $sqlDet  = "INSERT INTO detalle_devolucion (id_devolucion, id_producto, cantidad, motivo)
                    VALUES (?, ?, ?, ?)";
        $stmtDet = $this->conn->prepare($sqlDet);

        foreach ($items as $item) {
            $stmtDet->execute([
                $id_devolucion,
                $item['id_producto'],
                $item['cantidad'],
                $item['motivo']
            ]);
        }

        return $id_devolucion;
    }

    // Verifica si un pedido ya tiene una devolución registrada
    public function existePorPedido($id_pedido) {
        $sql  = "SELECT id_devolucion FROM devolucion WHERE id_pedido = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_pedido]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Trae las devoluciones de un cliente
    public function obtenerPorCliente($id_cliente) {
        $sql  = "SELECT d.*, p.total_pedido
                 FROM devolucion d
                 INNER JOIN pedido p ON d.id_pedido = p.id_pedido
                 WHERE p.id_cliente = ?
                 ORDER BY d.id_devolucion DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_cliente]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Trae el detalle de una devolución con nombre del producto
    public function obtenerDetalle($id_devolucion) {
        $sql  = "SELECT dd.*, pr.nombre
                 FROM detalle_devolucion dd
                 LEFT JOIN productos pr ON dd.id_producto = pr.id_producto
                 WHERE dd.id_devolucion = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_devolucion]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Trae todas las devoluciones para el admin
    public function obtenerTodas() {
        $sql  = "SELECT d.*,
                        pe.nombre_persona AS nombre_cliente,
                        p.total_pedido
                 FROM devolucion d
                 INNER JOIN pedido p   ON d.id_pedido  = p.id_pedido
                 INNER JOIN cliente c  ON p.id_cliente = c.id_cliente
                 INNER JOIN persona pe ON c.id_persona = pe.id_persona
                 ORDER BY d.id_devolucion DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
