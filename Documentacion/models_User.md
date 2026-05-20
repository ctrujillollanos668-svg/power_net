# models/User.php

**Qué hace:** Acceso a la tabla `usuarios`. Login, registro, perfil y recuperación de contraseña.

---

## Conectado con
- `config/Database.php`
- `controllers/UsuarioController.php` — lo usa para todo
- Tablas: `usuarios`, `persona`, `cliente`, `roles`

---

## Métodos clave

| Método | Qué hace |
|---|---|
| `register()` | Crea persona → usuario → cliente en cascada |
| `findByEmail()` | Busca usuario para el login |
| `findById()` | Busca usuario por ID (para perfil) |
| `actualizarPerfil()` | Cambia nombre y email |
| `actualizarPassword()` | Cambia contraseña (hashea con bcrypt) |
| `guardarTokenRecuperacion()` | Guarda token de reset con expiración de 1 hora |
| `buscarPorToken()` | Verifica que el token sea válido y no haya expirado |

---

## Si se daña este archivo
- Login y registro dejan de funcionar
- Nadie puede entrar al sitio (ni admin ni cliente)
