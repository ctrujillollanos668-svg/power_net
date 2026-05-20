# controllers/UsuarioController.php

**Qué hace:** Login, registro, logout, perfil y recuperación de contraseña.

---

## Conectado con
- `models/User.php` — toda la lógica de BD
- `views/auth/login.php` — formulario dentro del `#loginModal`
- `$_SESSION['usuario']` — guarda los datos del usuario logueado
- `$_SESSION['open_login']` — le dice a `index.php` que abra el modal login
- `$_SESSION['alert']` — mensajes que muestra SweetAlert2

---

## Acciones principales

| Action URL | Qué hace |
|---|---|
| `?action=register` | Crea usuario + persona + cliente en BD |
| `?action=login` | Verifica credenciales y crea sesión |
| `?action=logout` | Destruye la sesión |
| `?action=actualizar_perfil` | Cambia nombre y email |
| `?action=cambiar_password` | Cambia contraseña verificando la actual |
| `?action=enviar_recuperacion` | Genera token de recuperación |
| `?action=reset_password` | Cambia contraseña con token |

---

## Redirección por rol al hacer login
- Rol 1 (admin) → `/views/admin/dashboard.php`
- Rol 2 (cliente) → `/public/index.php`

---

## Si se daña este archivo
- Nadie puede iniciar sesión ni registrarse
- El modal login aparece pero los formularios no funcionan
- Usuarios ya logueados siguen navegando hasta que su sesión expire
