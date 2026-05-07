<?php
require_once __DIR__ . '/../models/Category.php';

class CategoryController {

    private $model;

    // 🔥 CONSTRUCTOR
    public function __construct() {
        $this->model = new Category();
    }

    public function guardar() {

        $this->model->guardar(
            $_POST['nombre_categoria'],
            $_POST['descripcion']
        );

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    public function editar() {

        $id = $_POST['id'];
        $nombre = $_POST['nombre_categoria'];
        $descripcion = $_POST['descripcion'];

        $this->model->editar($id, $nombre, $descripcion);

        header("Location: /power-net/views/admin/categoria/categorias.php");
    }

   public function toggle() {

        $id        = $_GET['id'];
        $resultado = $this->model->toggleEstado($id);

        if ($resultado['bloqueado']) {
            $total    = $resultado['total'];
            $nombres  = $resultado['productos'];
            $lista    = implode(', ', $nombres);
            $extra    = $total > 5 ? " y " . ($total - 5) . " más..." : '';

            $_SESSION['alert'] = [
                'icon'  => 'warning',
                'title' => "⚠️ Categoría en uso",
                'text'  => "Esta categoría tiene {$total} producto(s) asignado(s): {$lista}{$extra}. Reasigna o elimina esos productos antes de desactivarla."
            ];
        } else {
            $_SESSION['alert'] = [
                'icon'  => 'success',
                'title' => 'Estado actualizado',
                'text'  => 'El estado de la categoría fue cambiado correctamente.'
            ];
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
}