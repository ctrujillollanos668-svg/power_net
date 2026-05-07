<?php
require_once __DIR__ . '/../config/Database.php';

class Pago {

    public function crear($data) {

        $database = new Database();
        $db = $database->getConnection();

        $stmt = $db->prepare("
            INSERT INTO pago 
            (monto, metodo_pago, fecha_pago, factura, estado_pago, id_pedido)
            VALUES (?, ?, NOW(), ?, ?, ?)
        ");

        $stmt->execute([
            $data['monto'],
            $data['metodo_pago'],
            $data['factura'],
            $data['estado_pago'],
            $data['id_pedido']
        ]);

        return $db->lastInsertId();
    }
}