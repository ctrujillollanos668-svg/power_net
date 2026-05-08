# UsuarioController.php — Controlador de Usuarios

## Ubicación
`controllers/UsuarioController.php`

## ¿Qué hace?
Gestiona todo el ciclo de autenticación y gestión de cuenta: registro, login, logout, actualización de perfil, cambio de contraseña y recuperación de contraseña.

## Modelos que usa
| Modelo | Para qué |
|---|---|
| `User` | Todas las operaciones sobre la tabla `usuarios` y `persona` |

## Métodos
| Método | Action en index.php | Descripción |
|---|---|---|
| `register()` | `register` | Crea persona + usuario + cliente en una sola operación |
| `login()` | `login` | Verifica credenciales y crea la sesión |
| `logout()` | `logout` | Destruye la sesión y redirige al home |
| `actualizarPerfil()` | `actualizar_perfil` | Actualiza nombre y email del usuario |
| `cambiarRol()` | `cambiar_rol` | Alterna entre rol admin (1) y cliente (2) |
| `cambiarPassword()` | *(interno)* | Verifica contraseña actual y actualiza |
| `enviarRecuperacion()` | *(interno)* | Genera token y enlace de recuperación |
| `resetPassword()` | *(interno)* | Actualiza contraseña usando el token |

## Flujo — Registro
```
Modal login → tab Registrarse → formulario POST
    → index.php?action=register
        → UsuarioController::register()
            → User::register($nombre, $email, $password)
                → INSERT INTO persona
                → INSERT INTO usuarios (con id_persona, rol=2)
                → INSERT INTO cliente (con id_persona)
            → $_SESSION['alert'] de éxito o error
            → redirect a index.php
```

## Flujo — Login
```
Modal login → formulario POST
    → index.php?action=login
        → UsuarioController::login()
            → User::findByEmail($email)
            → password_verify($password, $hash)
            → $_SESSION['usuario'] = [id, nombre, email, rol]
            → Si rol=1: redirect a /views/admin/dashboard.php
            → Si rol=2: redirect a /public/index.php
```

## Sesión que crea
```php
$_SESSION['usuario'] = [
    'id'     => int,
    'nombre' => string,
    'email'  => string,
    'rol'    => 1 (admin) | 2 (cliente)
]
```

## Recuperación de contraseña
- Genera token con `bin2hex(random_bytes(32))`
- Expira en 1 hora
- Guarda `reset_token` y `reset_expira` en la tabla `usuarios`
- El enlace es: `index.php?action=reset_password&token=XXXX`

## Conectado con
- `index.php` (router)
- `models/User.php`
- `views/auth/login.php` (modal)
- `views/auth/recuperar.php`
- `views/auth/reset_password.php`
- `views/admin/dashboard.php` (redirección admin)
