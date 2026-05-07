<?php
require_once __DIR__ . '/../config/Database.php';

class Favorito {

    private $conn;

    public function __construct() {
        $db         = new Database();
        $this->conn = $db->getConnection();
    }

    // Agrega un producto a favoritos. Si ya existe, lo elimina (toggle).
    // Retorna true si quedó como favorito, false si se eliminó.
    public function toggle($id_usuario, $id_producto) {

        // Verificar si ya existe
        $check = $this->conn->prepare(
            "SELECT id_favorito FROM favorito WHERE id_usuario = ? AND id_producto = ?"
        );
        $check->execute([$id_usuario, $id_producto]);

        if ($check->fetch()) {
            // Ya existe → eliminar
            $del = $this->conn->prepare(
                "DELETE FROM favorito WHERE id_usuario = ? AND id_producto = ?"
            );
            $del->execute([$id_usuario, $id_producto]);
            return false; // ya no es favorito
        } else {
            // No existe → agregar
            $ins = $this->conn->prepare(
                "INSERT INTO favorito (id_usuario, id_producto) VALUES (?, ?)"
            );
            $ins->execute([$id_usuario, $id_producto]);
            return true; // ahora es favorito
        }
    }

    // Trae todos los id_producto que el usuario tiene en favoritos
    public function obtenerIdsPorUsuario($id_usuario) {
        $sql  = "SELECT id_producto FROM favorito WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_usuario]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'id_producto');
    }

    // Trae los productos favoritos completos de un usuario
    public function obtenerProductosPorUsuario($id_usuario) {
        $sql  = "SELECT p.*, c.nombre_categoria
                 FROM favorito f
                 INNER JOIN productos p ON f.id_producto = p.id_producto
                 LEFT JOIN  categoria c ON p.id_categoria = c.id_categoria
                 WHERE f.id_usuario = ?
                 ORDER BY f.creado_en DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_usuario]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cuenta cuántos usuarios tienen un producto en favoritos
    public function contarPorProducto($id_producto) {
        $sql  = "SELECT COUNT(*) FROM favorito WHERE id_producto = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_producto]);
        return (int)$stmt->fetchColumn();
    }
}
