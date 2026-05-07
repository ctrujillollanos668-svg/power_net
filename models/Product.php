<?php
require_once __DIR__ . '/../config/Database.php';

class Product {
    private $conn;
    private $table = "productos";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // ✅ CREAR PRODUCTO (SIN IMAGEN)
    public function crear($nombre, $descripcion, $precio, $stock, $categoria = null) {

        $disponibilidad = 1;

        $sql = "INSERT INTO {$this->table} 
                (nombre, descripcion, precio, stock, disponibilidad, id_categoria) 
                VALUES (:nombre, :descripcion, :precio, :stock, :disponibilidad, :categoria)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":precio", $precio);
        $stmt->bindParam(":stock", $stock);
        $stmt->bindParam(":disponibilidad", $disponibilidad);
        $stmt->bindParam(":categoria", $categoria);

        if ($stmt->execute()) {
    return $this->conn->lastInsertId();
}

return false;
    }

    // 🔎 OBTENER TODOS (CLIENTE)
    public function obtenerTodos() {
        $sql = "SELECT p.*, c.nombre_categoria 
                FROM productos p
                LEFT JOIN categoria c 
                ON p.id_categoria = c.id_categoria
                WHERE p.disponibilidad = 1";
                  
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔁 ACTIVAR / DESACTIVAR
    public function toggle($id) {
        $sql = "UPDATE {$this->table} 
                SET disponibilidad = IF(disponibilidad=1,0,1) 
                WHERE id_producto = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    // 🗑 ELIMINAR — verifica que no tenga pedidos asociados
    public function eliminar($id) {

        // Verificar si tiene registros en detalle_pedido
        $sqlCheck = "SELECT COUNT(*) FROM detalle_pedido WHERE id_producto = ?";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->execute([$id]);
        $totalPedidos = (int)$stmtCheck->fetchColumn();

        if ($totalPedidos > 0) {
            return [
                'bloqueado' => true,
                'total'     => $totalPedidos,
                'mensaje'   => "Este producto aparece en {$totalPedidos} pedido(s). No se puede eliminar para mantener el historial. Puedes desactivarlo en su lugar."
            ];
        }

        // Sin pedidos: eliminar imágenes físicas primero
        $sqlImgs = "SELECT imagen FROM imagenes_producto WHERE id_producto = ?";
        $stmtImgs = $this->conn->prepare($sqlImgs);
        $stmtImgs->execute([$id]);
        $imagenes = $stmtImgs->fetchAll(PDO::FETCH_ASSOC);

        foreach ($imagenes as $img) {
            $ruta = __DIR__ . '/../public/uploads/' . $img['imagen'];
            if (file_exists($ruta)) unlink($ruta);
        }

        // Eliminar de BD
        $sql  = "DELETE FROM {$this->table} WHERE id_producto = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);

        return ['bloqueado' => false];
    }

    // ✏ ACTUALIZAR (SIN IMAGEN)
    public function actualizar($id, $nombre, $descripcion, $precio, $stock, $categoria) {

        $sql = "UPDATE productos 
                SET nombre=:nombre, 
                    descripcion=:descripcion, 
                    precio=:precio, 
                    stock=:stock,
                    id_categoria=:categoria
                WHERE id_producto=:id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":nombre", $nombre);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":precio", $precio);
        $stmt->bindParam(":stock", $stock);
        $stmt->bindParam(":categoria", $categoria);

        return $stmt->execute();
    }

    // 🧑‍💻 ADMIN
    public function obtenerTodosAdmin() {
        $sql = "SELECT p.*, c.nombre_categoria 
                FROM productos p
                LEFT JOIN categoria c 
                ON p.id_categoria = c.id_categoria";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Solo trae productos activos Y con stock disponible para el catálogo del cliente
    public function obtenerActivos() {
        $sql = "SELECT p.*, c.nombre_categoria
                FROM productos p
                LEFT JOIN categoria c ON p.id_categoria = c.id_categoria
                WHERE p.disponibilidad = 1
                AND   p.stock > 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Trae solo productos con oferta activa hoy
    public function obtenerOfertas() {
        $sql = "SELECT p.*, c.nombre_categoria,
                       o.precio_oferta,
                       o.descuento
                FROM oferta o
                INNER JOIN productos p ON o.id_producto = p.id_producto
                LEFT JOIN  categoria c ON p.id_categoria = c.id_categoria
                WHERE o.estado = 1
                AND   o.fecha_inicio <= CURDATE()
                AND   o.fecha_fin    >= CURDATE()
                AND   p.disponibilidad = 1
                AND   p.stock > 0
                ORDER BY o.id_oferta DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Filtra productos. Si soloOfertas=true, solo trae los que tienen oferta activa hoy.
    public function filtrar($categoria = null, $buscar = null, $orden = null, $precioMin = null, $precioMax = null, $soloOfertas = false) {

        if ($soloOfertas) {
            // JOIN con oferta para traer solo los que tienen oferta activa
            $sql = "SELECT p.*, c.nombre_categoria,
                           o.precio_oferta,
                           o.descuento
                    FROM oferta o
                    INNER JOIN productos p ON o.id_producto = p.id_producto
                    LEFT JOIN  categoria c ON p.id_categoria = c.id_categoria
                    WHERE o.estado = 1
                    AND   o.fecha_inicio <= CURDATE()
                    AND   o.fecha_fin    >= CURDATE()
                    AND   p.disponibilidad = 1
                    AND   p.stock > 0";
        } else {
            $sql = "SELECT p.*, c.nombre_categoria,
                           o.precio_oferta,
                           o.descuento
                    FROM productos p
                    LEFT JOIN categoria c ON p.id_categoria = c.id_categoria
                    LEFT JOIN oferta o ON o.id_producto = p.id_producto
                              AND o.estado = 1
                              AND o.fecha_inicio <= CURDATE()
                              AND o.fecha_fin    >= CURDATE()
                    WHERE p.disponibilidad = 1 AND p.stock > 0";
        }

        $params = [];

        if ($categoria) {
            $sql     .= " AND p.id_categoria = ?";
            $params[] = $categoria;
        }

        if ($buscar) {
            $sql     .= " AND (p.nombre LIKE ? OR p.descripcion LIKE ?)";
            $like     = '%' . $buscar . '%';
            $params[] = $like;
            $params[] = $like;
        }

        if ($precioMin !== null && $precioMin !== '') {
            $sql     .= " AND COALESCE(o.precio_oferta, p.precio) >= ?";
            $params[] = $precioMin;
        }

        if ($precioMax !== null && $precioMax !== '') {
            $sql     .= " AND COALESCE(o.precio_oferta, p.precio) <= ?";
            $params[] = $precioMax;
        }

        switch ($orden) {
            case 'precio_asc':  $sql .= " ORDER BY COALESCE(o.precio_oferta, p.precio) ASC";  break;
            case 'precio_desc': $sql .= " ORDER BY COALESCE(o.precio_oferta, p.precio) DESC"; break;
            case 'nombre_asc':  $sql .= " ORDER BY p.nombre ASC"; break;
            default:            $sql .= " ORDER BY p.id_producto DESC"; break;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 🔎 POR ID
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM productos WHERE id_producto = :id LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🖼 OBTENER IMÁGENES (CORREGIDO)
    public function obtenerImagenes($idProducto) {
    $sql = "SELECT id, imagen 
            FROM imagenes_producto 
            WHERE id_producto = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":id", $idProducto);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // Descuenta stock al vender. Si llega a 0, oculta el producto del catálogo automáticamente.
    public function descontarStock($id, $cantidad) {

        // Resta la cantidad vendida, solo si hay suficiente stock
        $sqlStock = "UPDATE productos
                     SET stock = stock - :cantidad
                     WHERE id_producto = :id AND stock >= :cantidad";
        $stmt = $this->conn->prepare($sqlStock);
        $stmt->bindParam(':cantidad', $cantidad);
        $stmt->bindParam(':id',       $id);
        $stmt->execute();

        // Si el stock llegó a 0, lo oculta del catálogo (disponibilidad = 0)
        $sqlOcultar = "UPDATE productos
                       SET disponibilidad = 0
                       WHERE id_producto = :id AND stock = 0";
        $stmt2 = $this->conn->prepare($sqlOcultar);
        $stmt2->bindParam(':id', $id);
        $stmt2->execute();

        return true;
    }
    // 🖼 GUARDAR IMAGEN DEL PRODUCTO
public function guardarImagen($idProducto, $imagen) {
    $sql = "INSERT INTO imagenes_producto (id_producto, imagen)
            VALUES (:id_producto, :imagen)";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":id_producto", $idProducto);
    $stmt->bindParam(":imagen", $imagen);

    return $stmt->execute();
}
// 🔎 OBTENER UNA IMAGEN POR ID
public function obtenerImagenPorId($idImagen) {
    $sql = "SELECT * FROM imagenes_producto WHERE id = :id LIMIT 1";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":id", $idImagen);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// 🗑 ELIMINAR IMAGEN POR ID
public function eliminarImagen($idImagen) {
    $sql = "DELETE FROM imagenes_producto WHERE id = :id";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":id", $idImagen);

    return $stmt->execute();
}
// 🔎 PRODUCTOS RELACIONADOS
public function obtenerRelacionados($idProducto, $idCategoria = null) {

    if ($idCategoria) {
        $sql = "SELECT * FROM productos 
                WHERE disponibilidad = 1 
                AND id_producto != :id 
                AND id_categoria = :categoria
                LIMIT 4";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $idProducto);
        $stmt->bindParam(":categoria", $idCategoria);
    } else {
        $sql = "SELECT * FROM productos 
                WHERE disponibilidad = 1 
                AND id_producto != :id
                LIMIT 4";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $idProducto);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}