<?php
require_once __DIR__ . '/../config/Database.php';

class Venta {

    private $conn;

    public function __construct() {
        $db         = new Database();
        $this->conn = $db->getConnection();
    }

    // Registra una venta cerrada al confirmar el pago
    public function registrar($id_pedido, $id_cliente, $total) {
        $sql  = "INSERT INTO venta (id_pedido, id_cliente, total, fecha_venta)
                 VALUES (?, ?, ?, NOW())
                 ON DUPLICATE KEY UPDATE total = VALUES(total)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id_pedido, $id_cliente, $total]);
    }

    // Trae todas las ventas con datos del cliente
    public function obtenerTodas() {
        $sql  = "SELECT v.*,
                        pe.nombre_persona AS nombre_cliente,
                        p.estado_pedido,
                        pg.metodo_pago,
                        pg.factura
                 FROM venta v
                 INNER JOIN pedido  p   ON v.id_pedido  = p.id_pedido
                 INNER JOIN cliente c   ON v.id_cliente = c.id_cliente
                 INNER JOIN persona pe  ON c.id_persona = pe.id_persona
                 LEFT JOIN  pago    pg  ON pg.id_pedido = v.id_pedido
                 ORDER BY v.fecha_venta DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ventas por día (últimos N días)
    public function ventasPorDia($dias = 30) {
        $sql  = "SELECT DATE(fecha_venta) AS dia,
                        COUNT(*) AS total_ventas,
                        SUM(total) AS monto_total
                 FROM venta
                 WHERE fecha_venta >= DATE_SUB(NOW(), INTERVAL ? DAY)
                 GROUP BY DATE(fecha_venta)
                 ORDER BY dia DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$dias]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Productos más vendidos
    public function topProductos($limite = 10) {
        $sql  = "SELECT pr.nombre,
                        SUM(dp.cantidad)  AS unidades,
                        SUM(dp.subtotal)  AS ingresos
                 FROM venta v
                 INNER JOIN detalle_pedido dp ON dp.id_pedido  = v.id_pedido
                 INNER JOIN productos      pr ON pr.id_producto = dp.id_producto
                 GROUP BY dp.id_producto
                 ORDER BY unidades DESC
                 LIMIT ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$limite]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Totales generales
    public function totales() {
        $sql  = "SELECT COUNT(*)    AS total_ventas,
                        SUM(total)  AS monto_total,
                        AVG(total)  AS promedio
                 FROM venta";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
