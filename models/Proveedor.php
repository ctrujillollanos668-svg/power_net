<?php
require_once __DIR__ . '/../config/Database.php';

class Proveedor {

    private $conn;

    public function __construct() {
        $db         = new Database();
        $this->conn = $db->getConnection();
    }

    public function obtenerTodos() {
        $sql  = "SELECT * FROM proveedor ORDER BY id_proveedor DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql  = "SELECT * FROM proveedor WHERE id_proveedor = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear($nombre, $correo, $telefono) {
        $sql  = "INSERT INTO proveedor (nombre_proveedor, correo, telefono) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$nombre, $correo, $telefono]);
    }

    public function actualizar($id, $nombre, $correo, $telefono) {
        $sql  = "UPDATE proveedor SET nombre_proveedor=?, correo=?, telefono=? WHERE id_proveedor=?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$nombre, $correo, $telefono, $id]);
    }

    public function eliminar($id) {
        // Verificar si tiene productos asociados
        $check = $this->conn->prepare("SELECT COUNT(*) FROM productos WHERE id_proveedor = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) {
            return ['bloqueado' => true];
        }
        $sql  = "DELETE FROM proveedor WHERE id_proveedor = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return ['bloqueado' => false];
    }
}
