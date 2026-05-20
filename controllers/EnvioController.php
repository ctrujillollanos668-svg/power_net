<?php
require_once __DIR__ . '/../models/Envio.php';
require_once __DIR__ . '/../config/Database.php';

class EnvioController {

    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $envNuevo = new Envio();
            $envNuevo->crear(
                (int)$_POST['id_pedido'],
                trim($_POST['empresa']   ?? 'Power Net Envíos'),
                trim($_POST['direccion'] ?? ''),
                (float)($_POST['costo'] ?? 0)
            );
            $pdo = (new Database())->getConnection();
            $pdo->prepare("UPDATE pedido SET estado_pedido = 'enviado' WHERE id_pedido = ?")
                ->execute([(int)$_POST['id_pedido']]);
            $_SESSION['alert'] = ['icon'=>'success','title'=>'Envío registrado','text'=>'El envío fue creado correctamente'];
        }
        header("Location: index.php?action=envios"); exit;
    }

    public function actualizarEstado() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pdo = (new Database())->getConnection();
            $pdo->prepare("UPDATE envio SET estado = ? WHERE id_envio = ?")
                ->execute([$_POST['estado'], (int)$_POST['id_envio']]);
            if ($_POST['estado'] === 'entregado') {
                $row = $pdo->prepare("SELECT id_pedido FROM envio WHERE id_envio = ?");
                $row->execute([(int)$_POST['id_envio']]);
                $r = $row->fetch(PDO::FETCH_ASSOC);
                if ($r) $pdo->prepare("UPDATE pedido SET estado_pedido = 'entregado' WHERE id_pedido = ?")
                            ->execute([$r['id_pedido']]);
            }
            $_SESSION['alert'] = ['icon'=>'success','title'=>'Actualizado','text'=>'Estado del envío actualizado'];
        }
        header("Location: index.php?action=envios"); exit;
    }

    public function eliminar() {
        if (isset($_GET['id'])) {
            $pdo = (new Database())->getConnection();
            $pdo->prepare("DELETE FROM envio WHERE id_envio = ?")->execute([(int)$_GET['id']]);
            $_SESSION['alert'] = ['icon'=>'success','title'=>'Eliminado','text'=>'Envío eliminado'];
        }
        header("Location: index.php?action=envios"); exit;
    }
}
