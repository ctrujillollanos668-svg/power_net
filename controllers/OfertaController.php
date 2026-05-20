<?php
require_once __DIR__ . '/../models/Oferta.php';
require_once __DIR__ . '/../models/Product.php';

class OfertaController {

    public function guardar() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=ofertas");
            exit;
        }

        $id_producto   = (int)($_POST['id_producto']   ?? 0);
        $precio_oferta = (float)($_POST['precio_oferta'] ?? 0);
        $descuento     = (int)($_POST['descuento']     ?? 0);
        $fecha_inicio  = $_POST['fecha_inicio'] ?? '';
        $fecha_fin     = $_POST['fecha_fin']    ?? '';

        // Validar campos
        if (!$id_producto || !$precio_oferta || !$fecha_inicio || !$fecha_fin) {
            $_SESSION['alert'] = ['icon'=>'error','title'=>'Datos incompletos','text'=>'Completa todos los campos'];
            header("Location: index.php?action=ofertas"); exit;
        }

        // Validar que fecha_fin >= fecha_inicio
        if ($fecha_fin < $fecha_inicio) {
            $_SESSION['alert'] = ['icon'=>'error','title'=>'Fechas inválidas','text'=>'La fecha de fin debe ser mayor o igual a la fecha de inicio'];
            header("Location: index.php?action=ofertas"); exit;
        }

        $ofertaModel = new Oferta();

        // Desactivar TODAS las ofertas activas del producto (sin filtro de fecha)
        $pdo  = (new Database())->getConnection();
        $pdo->prepare("UPDATE oferta SET estado = 0 WHERE id_producto = ? AND estado = 1")
            ->execute([$id_producto]);

        // Crear nueva oferta
        $ofertaModel->crear($id_producto, $precio_oferta, $descuento, $fecha_inicio, $fecha_fin);

        $_SESSION['alert'] = ['icon'=>'success','title'=>'🏷️ Oferta creada','text'=>'La oferta ya es visible para los clientes'];
        header("Location: index.php?action=ofertas"); exit;
    }

    public function desactivar() {
        $id_oferta = (int)($_GET['id'] ?? 0);
        if ($id_oferta) {
            $ofertaModel = new Oferta();
            $ofertaModel->desactivar($id_oferta);
            $_SESSION['alert'] = ['icon'=>'success','title'=>'Desactivada','text'=>'Oferta desactivada correctamente'];
        }
        header("Location: index.php?action=ofertas"); exit;
    }

    public function activar() {
        $id_oferta = (int)($_GET['id'] ?? 0);
        if ($id_oferta) {
            require_once __DIR__ . '/../config/Database.php';
            (new Database())->getConnection()
                ->prepare("UPDATE oferta SET estado = 1 WHERE id_oferta = ?")
                ->execute([$id_oferta]);
            $_SESSION['alert'] = ['icon'=>'success','title'=>'Activada','text'=>'Oferta activada correctamente'];
        }
        header("Location: index.php?action=ofertas"); exit;
    }

    public function editar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../config/Database.php';
            $id_oferta     = (int)($_POST['id_oferta']     ?? 0);
            $precio_oferta = (float)($_POST['precio_oferta'] ?? 0);
            $descuento     = (int)($_POST['descuento']     ?? 0);
            $fecha_inicio  = $_POST['fecha_inicio'] ?? '';
            $fecha_fin     = $_POST['fecha_fin']    ?? '';

            if ($id_oferta && $precio_oferta && $fecha_inicio && $fecha_fin) {
                (new Database())->getConnection()
                    ->prepare("UPDATE oferta SET precio_oferta=?, descuento=?, fecha_inicio=?, fecha_fin=? WHERE id_oferta=?")
                    ->execute([$precio_oferta, $descuento, $fecha_inicio, $fecha_fin, $id_oferta]);
                $_SESSION['alert'] = ['icon'=>'success','title'=>'Actualizada','text'=>'Oferta actualizada correctamente'];
            }
        }
        header("Location: index.php?action=ofertas"); exit;
    }
}
