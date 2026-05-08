<?php
require_once __DIR__ . '/../config/Database.php';

class Oferta {

    private $conn;

    public function __construct() {
        $db         = new Database();
        $this->conn = $db->getConnection();
    }

    // Trae todos los productos que tienen oferta activa hoy
    public function obtenerActivas() {
        $sql = "SELECT p.*, c.nombre_categoria,
                       o.precio_oferta,
                       o.descuento,
                       o.id_oferta,
                       o.fecha_inicio,
                       o.fecha_fin
                FROM oferta o
                INNER JOIN productos p ON o.id_producto = p.id_producto
                LEFT JOIN  categoria c ON p.id_categoria = c.id_categoria
                WHERE o.estado = 1
                AND   DATE(o.fecha_inicio) <= CURDATE()
                AND   DATE(o.fecha_fin)    >= CURDATE()
                AND   p.disponibilidad = 1
                AND   p.stock > 0
                ORDER BY o.id_oferta DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verifica si un producto tiene oferta activa hoy y la retorna
    public function obtenerPorProducto($id_producto) {
        // Busca cualquier oferta activa del producto, sin filtro de fecha
        // para poder desactivarla antes de crear una nueva
        $sql = "SELECT * FROM oferta
                WHERE id_producto = ?
                AND   estado = 1
                ORDER BY id_oferta DESC
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_producto]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crea una nueva oferta
    public function crear($id_producto, $precio_oferta, $descuento, $fecha_inicio, $fecha_fin) {
        $sql  = "INSERT INTO oferta (id_producto, precio_oferta, descuento, fecha_inicio, fecha_fin, estado)
                 VALUES (?, ?, ?, ?, ?, 1)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id_producto, $precio_oferta, $descuento, $fecha_inicio, $fecha_fin]);
    }

    // Desactiva una oferta
    public function desactivar($id_oferta) {
        $sql  = "UPDATE oferta SET estado = 0 WHERE id_oferta = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id_oferta]);
    }

    // Lista todas las ofertas (para el admin)
    public function obtenerTodas() {
        $sql = "SELECT o.*, p.nombre AS nombre_producto, p.precio AS precio_original
                FROM oferta o
                INNER JOIN productos p ON o.id_producto = p.id_producto
                ORDER BY o.id_oferta DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
