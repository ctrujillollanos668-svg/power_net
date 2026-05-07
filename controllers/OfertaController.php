<?php
require_once __DIR__ . '/../models/Oferta.php';
require_once __DIR__ . '/../models/Product.php';

class OfertaController {

    // Guarda una nueva oferta desde el panel admin
    public function guardar() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /power-net/views/admin/dashboard.php");
            exit;
        }

        $id_producto  = (int)($_POST['id_producto']  ?? 0);
        $precio_oferta= (float)($_POST['precio_oferta'] ?? 0);
        $descuento    = (int)($_POST['descuento']    ?? 0);
        $fecha_inicio = $_POST['fecha_inicio'] ?? '';
        $fecha_fin    = $_POST['fecha_fin']    ?? '';

        if (!$id_producto || !$precio_oferta || !$fecha_inicio || !$fecha_fin) {
            $_SESSION['alert'] = [
                'icon'  => 'error',
                'title' => 'Datos incompletos',
                'text'  => 'Completa todos los campos de la oferta'
            ];
            header("Location: /power-net/views/admin/dashboard.php");
            exit;
        }

        $ofertaModel = new Oferta();
        $ofertaModel->crear($id_producto, $precio_oferta, $descuento, $fecha_inicio, $fecha_fin);

        $_SESSION['alert'] = [
            'icon'  => 'success',
            'title' => 'Oferta creada',
            'text'  => 'La oferta fue registrada correctamente'
        ];

        header("Location: /power-net/views/admin/dashboard.php");
        exit;
    }

    // Desactiva una oferta
    public function desactivar() {

        $id_oferta = (int)($_GET['id'] ?? 0);

        if ($id_oferta) {
            $ofertaModel = new Oferta();
            $ofertaModel->desactivar($id_oferta);

            $_SESSION['alert'] = [
                'icon'  => 'success',
                'title' => 'Oferta desactivada',
                'text'  => 'La oferta fue desactivada correctamente'
            ];
        }

        header("Location: /power-net/views/admin/dashboard.php");
        exit;
    }
}
