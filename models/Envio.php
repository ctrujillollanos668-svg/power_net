<?php
require_once __DIR__ . '/../config/Database.php';

class Envio {

    private $conn;

    public function __construct() {
        $db         = new Database();
        $this->conn = $db->getConnection();
    }

    // Crea un registro de envío cuando el admin marca el pedido como "enviado"
    public function crear($id_pedido, $empresa, $direccion_envio, $costo = 0) {
        $sql  = "INSERT INTO envio (empresa_envios, estado, costo, fecha_hora, direccion_envio, id_pedido)
                 VALUES (?, 'en_camino', ?, NOW(), ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$empresa, $costo, $direccion_envio, $id_pedido]);
    }

    // Actualiza el estado del envío
    public function actualizarEstado($id_pedido, $estado) {
        $sql  = "UPDATE envio SET estado = ? WHERE id_pedido = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$estado, $id_pedido]);
    }

    // Obtiene el envío de un pedido
    public function obtenerPorPedido($id_pedido) {
        $sql  = "SELECT * FROM envio WHERE id_pedido = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_pedido]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Verifica si un pedido ya tiene envío registrado
    public function existePorPedido($id_pedido) {
        $sql  = "SELECT id_envio FROM envio WHERE id_pedido = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_pedido]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
