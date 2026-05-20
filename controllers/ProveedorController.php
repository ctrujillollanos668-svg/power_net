<?php
require_once __DIR__ . '/../models/Proveedor.php';

class ProveedorController {

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new Proveedor())->crear(
                trim($_POST['nombre']   ?? ''),
                trim($_POST['correo']   ?? ''),
                trim($_POST['telefono'] ?? '')
            );
            $_SESSION['alert'] = ['icon'=>'success','title'=>'Guardado','text'=>'Proveedor creado correctamente'];
        }
        header("Location: index.php?action=proveedores"); exit;
    }

    public function editar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            (new Proveedor())->actualizar(
                (int)$_POST['id'],
                trim($_POST['nombre']   ?? ''),
                trim($_POST['correo']   ?? ''),
                trim($_POST['telefono'] ?? '')
            );
            $_SESSION['alert'] = ['icon'=>'success','title'=>'Actualizado','text'=>'Proveedor actualizado correctamente'];
        }
        header("Location: index.php?action=proveedores"); exit;
    }

    public function eliminar() {
        if (isset($_GET['id'])) {
            $res = (new Proveedor())->eliminar((int)$_GET['id']);
            if ($res['bloqueado']) {
                $_SESSION['alert'] = ['icon'=>'warning','title'=>'No se puede eliminar','text'=>'Este proveedor tiene productos asociados'];
            } else {
                $_SESSION['alert'] = ['icon'=>'success','title'=>'Eliminado','text'=>'Proveedor eliminado correctamente'];
            }
        }
        header("Location: index.php?action=proveedores"); exit;
    }

    public function toggle() {
        if (isset($_GET['id'])) {
            (new Proveedor())->toggleActivo((int)$_GET['id']);
        }
        header("Location: index.php?action=proveedores"); exit;
    }
}
