<?php
require_once __DIR__ . '/../config/Database.php';

class Category {
    private $conn;
    private $table = "categoria";

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // 🔥 OBTENER TODAS
   public function obtenerTodas() {
    $sql = "SELECT * FROM categoria ORDER BY id_categoria DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // 🔥 GUARDAR
    public function guardar($nombre, $descripcion) {
        $sql = "INSERT INTO " . $this->table . " (nombre_categoria, descripcion, estado)
                VALUES (?, ?, 1)";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$nombre, $descripcion]);
    }

    // 🔥 EDITAR (ESTE TE FALTABA)
    public function editar($id, $nombre, $descripcion) {
        $sql = "UPDATE " . $this->table . "
                SET nombre_categoria = ?, descripcion = ?
                WHERE id_categoria = ?";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$nombre, $descripcion, $id]);
    }

 // 🔁 CAMBIAR ESTADO
public function toggle($id) {
    $sql = "UPDATE categoria 
            SET estado = IF(estado = 1, 0, 1) 
            WHERE id_categoria = ?";

    $stmt = $this->conn->prepare($sql);
    return $stmt->execute([$id]);
}
// 📊 CONTAR PRODUCTOS POR CATEGORÍA
public function obtenerConConteo() {
    $sql = "SELECT c.*, COUNT(p.id_producto) as total_productos
            FROM categoria c
            LEFT JOIN productos p ON p.id_categoria = c.id_categoria
            GROUP BY c.id_categoria";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 🔁 TOGGLE CON VALIDACIÓN
public function toggleEstado($id) {

    // Verificar si tiene productos antes de desactivar
    $sql  = "SELECT COUNT(*) FROM productos WHERE id_categoria = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$id]);
    $total = $stmt->fetchColumn();

    if ($total > 0) {
        // Retornar los nombres de los productos para mostrar en el mensaje
        $sqlNombres = "SELECT nombre FROM productos WHERE id_categoria = ? LIMIT 5";
        $stmtN      = $this->conn->prepare($sqlNombres);
        $stmtN->execute([$id]);
        $nombres = array_column($stmtN->fetchAll(PDO::FETCH_ASSOC), 'nombre');

        return [
            'bloqueado'  => true,
            'total'      => $total,
            'productos'  => $nombres
        ];
    }

    // Sin productos: cambiar estado normalmente
    $sql  = "UPDATE categoria SET estado = IF(estado = 1, 0, 1) WHERE id_categoria = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$id]);

    return ['bloqueado' => false];
}
// ✅ SOLO ACTIVAS (PARA CLIENTE)
public function obtenerActivas() {
    $sql = "SELECT * FROM categoria WHERE estado = 1";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}