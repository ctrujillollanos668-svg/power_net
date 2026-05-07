<?php
require_once __DIR__ . '/../config/Database.php';

class User {
    private $conn;
    private $table_name = "usuarios";

    public function __construct() {
        $database = new Database();
       $this->conn = $database->getConnection();
    }

public function register($nombre, $email, $password) {

    $passwordHash = password_hash($password, PASSWORD_BCRYPT);

    // 🔥 1. CREAR PERSONA
    $sqlPersona = "INSERT INTO persona (nombre_persona) VALUES (?)";
    $stmtPersona = $this->conn->prepare($sqlPersona);

    if (!$stmtPersona->execute([$nombre])) {
        return "Error al crear persona";
    }

    $id_persona = $this->conn->lastInsertId();

    // 🔥 2. CREAR USUARIO (CON id_persona)
    $sqlUsuario = "INSERT INTO usuarios (nombre, email, password, id_rol, id_persona)
                   VALUES (?, ?, ?, 2, ?)";

    $stmtUsuario = $this->conn->prepare($sqlUsuario);

    if (!$stmtUsuario->execute([$nombre, $email, $passwordHash, $id_persona])) {
        return "Error al crear usuario";
    }

    // 🔥 3. CREAR CLIENTE (CON id_persona)
    $sqlCliente = "INSERT INTO cliente (id_persona)
                   VALUES (?)";

    $stmtCliente = $this->conn->prepare($sqlCliente);

    if (!$stmtCliente->execute([$id_persona])) {
        return "Error al crear cliente";
    }

    return true;
}
    public function findByEmail($email) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // 🔥 OBTENER TODOS LOS USUARIOS
public function obtenerTodos() {
    $sql = "SELECT u.*, r.nombre AS rol_nombre
            FROM usuarios u
            LEFT JOIN roles r ON u.id_rol = r.id
            ORDER BY u.id DESC";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 🔄 CAMBIAR ROL (1 = admin, 2 = cliente)
public function cambiarRol($id) {
    $sql = "UPDATE usuarios 
            SET id_rol = IF(id_rol = 1, 2, 1)
            WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":id", $id);

    return $stmt->execute();
}
// --- ACTUALIZAR PERFIL DEL USUARIO ---
public function actualizarPerfil($id, $nombre, $email) {

    $sql = "UPDATE " . $this->table_name . " 
            SET nombre = :nombre, email = :email 
            WHERE id = :id";

    $stmt = $this->conn->prepare($sql);

    $stmt->bindParam(":nombre", $nombre);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":id", $id);

    return $stmt->execute();
}
// --- BUSCAR USUARIO POR ID ---
public function findById($id) {
    $sql = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// --- ACTUALIZAR CONTRASEÑA ---
public function actualizarPassword($id, $password) {

    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    $sql = "UPDATE " . $this->table_name . " 
            SET password = :password 
            WHERE id = :id";

    $stmt = $this->conn->prepare($sql);

    $stmt->bindParam(":password", $password_hash);
    $stmt->bindParam(":id", $id);

    return $stmt->execute();
}
// --- GUARDAR TOKEN DE RECUPERACIÓN ---
public function guardarTokenRecuperacion($email, $token, $expira) {

    $sql = "UPDATE " . $this->table_name . "
            SET reset_token = :token,
                reset_expira = :expira
            WHERE email = :email";

    $stmt = $this->conn->prepare($sql);

    $stmt->bindParam(":token", $token);
    $stmt->bindParam(":expira", $expira);
    $stmt->bindParam(":email", $email);

    return $stmt->execute();
}
// BUSCAR POR TOKEN
public function buscarPorToken($token) {

    $sql = "SELECT * FROM usuarios 
            WHERE reset_token = :token 
            AND reset_expira > NOW()
            LIMIT 1";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":token", $token);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// ACTUALIZAR PASSWORD
public function actualizarPasswordPorToken($token, $password) {

    $sql = "UPDATE usuarios 
            SET password = :password,
                reset_token = NULL,
                reset_expira = NULL
            WHERE reset_token = :token";

    $stmt = $this->conn->prepare($sql);

    $stmt->bindParam(":password", $password);
    $stmt->bindParam(":token", $token);

    return $stmt->execute();
}
}
?>