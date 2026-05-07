<?php
require_once __DIR__ . '/../config/Database.php';

class MetodoPago {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Guarda un nuevo método de pago para un cliente
    public function guardar($id_cliente, $tipo, $numero, $titular) {
        $sql  = "INSERT INTO metodo_pago (id_cliente, tipo, numero, titular)
                 VALUES (:id_cliente, :tipo, :numero, :titular)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':id_cliente' => $id_cliente,
            ':tipo'       => $tipo,
            ':numero'     => $numero,
            ':titular'    => $titular
        ]);
    }

    // Trae todos los métodos de un cliente
    public function obtenerPorCliente($id_cliente) {
        $sql  = "SELECT * FROM metodo_pago WHERE id_cliente = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id_cliente);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Trae un método por su ID (para verificar pertenencia antes de editar/eliminar)
    public function obtenerPorId($id_metodo) {
        $sql  = "SELECT * FROM metodo_pago WHERE id_metodo = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id_metodo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualiza tipo, número y titular de un método existente
    public function actualizar($id_metodo, $tipo, $numero, $titular) {
        $sql  = "UPDATE metodo_pago
                 SET tipo = :tipo, numero = :numero, titular = :titular
                 WHERE id_metodo = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':tipo'    => $tipo,
            ':numero'  => $numero,
            ':titular' => $titular,
            ':id'      => $id_metodo
        ]);
    }

    // Elimina un método de pago por su ID
    public function eliminar($id_metodo) {
        $sql  = "DELETE FROM metodo_pago WHERE id_metodo = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id_metodo]);
    }
}
