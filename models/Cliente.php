<?php
require_once __DIR__ . '/../config/Database.php';

class Cliente {

    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // 🔍 Buscar cliente por usuario
    public function obtenerPorUsuario($id_usuario) {

        $sql = "SELECT * FROM cliente 
                WHERE id_persona = (
                    SELECT id_persona FROM usuarios WHERE id = ?
                )";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_usuario]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crea un registro en cliente si no existe para ese usuario
    public function crearSiNoExiste($id_usuario) {

        $sql = "SELECT id_persona FROM usuarios WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id_usuario]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !$row['id_persona']) return false;

        $id_persona = $row['id_persona'];

        // Verificar si ya existe cliente para esa persona
        $check = $this->conn->prepare("SELECT id_cliente FROM cliente WHERE id_persona = ?");
        $check->execute([$id_persona]);
        if ($check->fetch()) return true; // ya existe

        // Crear cliente
        $insert = $this->conn->prepare("INSERT INTO cliente (id_persona) VALUES (?)");
        return $insert->execute([$id_persona]);
    }

    // Guarda la dirección de envío y teléfono en el registro del cliente.
    // También actualiza el teléfono en la tabla persona.
    public function actualizarDireccion($id_cliente, $direccion, $telefono) {

        // Actualizar dirección en tabla cliente
        $sql  = "UPDATE cliente SET direccion = ? WHERE id_cliente = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$direccion, $id_cliente]);

        // Actualizar teléfono en tabla persona (si viene)
        if ($telefono) {
            $sqlTel  = "UPDATE persona SET telefono = ?
                        WHERE id_persona = (
                            SELECT id_persona FROM cliente WHERE id_cliente = ?
                        )";
            $stmtTel = $this->conn->prepare($sqlTel);
            $stmtTel->execute([$telefono, $id_cliente]);
        }

        return true;
    }
}