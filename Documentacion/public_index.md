# public/index.php

**Qué es:** Punto de entrada de toda la tienda. Todo pasa por aquí.

---

## Conectado con
- `routes/web.php` — decide qué controlador ejecutar según `?action=`
- Todos los controladores de `controllers/`
- Todas las vistas de `views/cliente/`
- `views/auth/login.php` — dentro del modal login
- `views/cliente/partials/header.php` y `footer.php`
- `public/assets/css/store.css` y `js/store.js`

---

## Modales que viven aquí

### `#loginModal`
- Contiene `views/auth/login.php`
- Se abre cuando `$_SESSION['open_login'] = true` (lo activan los controladores cuando el usuario no está logueado)
- También se abre desde JS con `abrirLogin()`

**Si se daña este modal:**
- El cliente no puede iniciar sesión ni registrarse
- No puede agregar favoritos, ver pedidos, pagar — todo lo que requiere sesión queda bloqueado

### `#recuperarModal`
- Formulario de recuperación de contraseña
- Se abre desde el enlace "¿Olvidaste tu contraseña?" dentro del `#loginModal`
- La transición entre modales la maneja `store.js`

**Si se daña este modal:**
- El usuario no puede recuperar su contraseña, pero el resto del sitio funciona normal

---

## Si se daña este archivo
Todo el sitio cliente deja de funcionar. Es el archivo más crítico del proyecto.
