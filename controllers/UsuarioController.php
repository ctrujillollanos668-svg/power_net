<?php
require_once __DIR__ . '/../models/User.php';

class UsuarioController {

    public function register() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $nombre = trim($_POST['nombre'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($nombre) || empty($email) || empty($password)) {
            $this->alerta('warning', 'Campos incompletos', 'Completa todos los campos');
            header("Location: index.php");
            exit;
        }

        $user = new User();
        $resultado = $user->register($nombre, $email, $password);

        if ($resultado === true) {
            $this->alerta('success', 'Registro exitoso', 'Cuenta creada correctamente');
        } else {
            $this->alerta('error', 'Error', $resultado);
        }

        header("Location: index.php");
        exit;
    }

    public function login() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($email) || empty($password)) {
            $this->alerta('warning', 'Error', 'Completa todos los campos');
            header("Location: index.php");
            exit;
        }

        $user = new User();
        $usuario = $user->findByEmail($email);

        if ($usuario && password_verify($password, $usuario['password'])) {

            $_SESSION['usuario'] = [
                'id'     => $usuario['id']     ?? null,
                'nombre' => $usuario['nombre'] ?? 'Usuario',
                'email'  => $usuario['email']  ?? '',
                'rol'    => $usuario['id_rol'] ?? 2
            ];

            $this->alerta('success', 'Bienvenido', 'Inicio de sesión correcto');

            if ($_SESSION['usuario']['rol'] == 1) {
                header("Location: index.php?action=dashboard");
            } else {
                header("Location: index.php");
            }
            exit;

        } else {
            $this->alerta('error', 'Error', 'Credenciales incorrectas');
            header("Location: index.php");
            exit;
        }
    }

    public function logout() {
        session_destroy();
        header("Location: index.php");
        exit;
    }

    private function alerta($icon, $title, $text) {
        $_SESSION['alert'] = [
            'icon'  => $icon,
            'title' => $title,
            'text'  => $text
        ];
    }

    public function cambiarRol() {
        $id = $_GET['id'];
        $user = new User();
        $user->cambiarRol($id);
        header("Location: index.php?action=dashboard");
        exit;
    }

    public function actualizarPerfil() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=mi_perfil&tab=datos");
            exit;
        }

        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php");
            exit;
        }

        $id     = $_SESSION['usuario']['id'];
        $nombre = trim($_POST['nombre'] ?? '');
        $email  = trim($_POST['correo'] ?? '');

        if (empty($nombre) || empty($email)) {
            $this->alerta('warning', 'Campos incompletos', 'Completa todos los campos');
            header("Location: index.php?action=mi_perfil&tab=datos");
            exit;
        }

        $user      = new User();
        $resultado = $user->actualizarPerfil($id, $nombre, $email);

        if ($resultado) {
            $_SESSION['usuario']['nombre'] = $nombre;
            $_SESSION['usuario']['email']  = $email;
            $this->alerta('success', 'Perfil actualizado', 'Tus datos fueron actualizados correctamente');
        } else {
            $this->alerta('error', 'Error', 'No se pudo actualizar el perfil');
        }

        header("Location: index.php?action=mi_perfil&tab=datos");
        exit;
    }

    public function cambiarPassword() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?action=mi_perfil&tab=seguridad");
            exit;
        }

        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php");
            exit;
        }

        $id        = $_SESSION['usuario']['id'];
        $actual    = $_POST['password_actual']    ?? '';
        $nueva     = $_POST['password_nueva']     ?? '';
        $confirmar = $_POST['password_confirmar'] ?? '';

        if (empty($actual) || empty($nueva) || empty($confirmar)) {
            $this->alerta('warning', 'Campos vacíos', 'Completa todos los campos');
            header("Location: index.php?action=mi_perfil&tab=seguridad");
            exit;
        }

        if ($nueva !== $confirmar) {
            $this->alerta('error', 'Error', 'Las contraseñas no coinciden');
            header("Location: index.php?action=mi_perfil&tab=seguridad");
            exit;
        }

        $user    = new User();
        $usuario = $user->findById($id);

        if (!$usuario || !password_verify($actual, $usuario['password'])) {
            $this->alerta('error', 'Error', 'Contraseña actual incorrecta');
            header("Location: index.php?action=mi_perfil&tab=seguridad");
            exit;
        }

        $user->actualizarPassword($id, $nueva);
        $this->alerta('success', 'Éxito', 'Contraseña actualizada correctamente');

        header("Location: index.php?action=mi_perfil&tab=seguridad");
        exit;
    }

    public function enviarRecuperacion() {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php");
            exit;
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email)) {
            $this->alerta('warning', 'Correo requerido', 'Ingresa tu correo');
            header("Location: index.php");
            exit;
        }

        $user    = new User();
        $usuario = $user->findByEmail($email);

        if (!$usuario) {
            $this->alerta('error', 'No encontrado', 'No existe una cuenta con ese correo');
            header("Location: index.php");
            exit;
        }

        $token  = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $user->guardarTokenRecuperacion($email, $token, $expira);

        // Construir link dinámico según el host actual
        $base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
              . '://' . $_SERVER['HTTP_HOST']
              . dirname($_SERVER['SCRIPT_NAME']);
        $link = rtrim($base, '/') . '/index.php?action=reset_password&token=' . $token;

        $this->alerta('success', 'Enlace generado', $link);

        header("Location: index.php");
        exit;
    }

    public function resetPassword() {

        $token     = $_POST['token']              ?? '';
        $nueva     = $_POST['password_nueva']     ?? '';
        $confirmar = $_POST['password_confirmar'] ?? '';

        if ($nueva !== $confirmar) {
            $this->alerta('error', 'Error', 'Las contraseñas no coinciden');
            header("Location: index.php");
            exit;
        }

        $user    = new User();
        $usuario = $user->buscarPorToken($token);

        if (!$usuario) {
            $this->alerta('error', 'Error', 'Token inválido o expirado');
            header("Location: index.php");
            exit;
        }

        $password_hash = password_hash($nueva, PASSWORD_BCRYPT);
        $user->actualizarPasswordPorToken($token, $password_hash);

        $this->alerta('success', 'Listo', 'Contraseña actualizada');

        header("Location: index.php");
        exit;
    }
}
