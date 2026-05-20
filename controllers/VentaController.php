<?php
require_once __DIR__ . '/../models/Venta.php';

class VentaController {

    public function eliminar() {
        $id = (int)($_GET['id'] ?? 0);
        if ($id) {
            (new Venta())->eliminar($id);
            $_SESSION['alert'] = ['icon'=>'success','title'=>'Eliminada','text'=>'Registro de venta eliminado'];
        }
        header("Location: index.php?action=ventas");
        exit;
    }

    public function actualizarEstado() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_venta = (int)($_POST['id_venta'] ?? 0);
            $estado   = $_POST['estado'] ?? '';
            if ($id_venta && $estado) {
                (new Venta())->actualizarEstado($id_venta, $estado);
                $_SESSION['alert'] = ['icon'=>'success','title'=>'Actualizado','text'=>'Estado de venta actualizado'];
            }
        }
        header("Location: index.php?action=ventas");
        exit;
    }
}