# Guía: Si me quitan algo, ¿qué se rompe y dónde lo arreglo?

Esta es tu guía de diagnóstico rápido. Si el instructor te quita código y algo deja de funcionar, busca aquí qué archivo revisar.

---

## 🔴 CLIENTE — Cosas que afectan al usuario

---

### Si desaparece el menú de navegación (Inicio, Categorías, Ofertas, Mis Pedidos)
- **Archivo dañado:** `views/cliente/partials/header.php`
- **Qué buscar:** La etiqueta `<nav>` con clase `sidebar-nav` o los `<a href="index.php...">` del menú
- **Impacto:** El cliente no puede navegar entre secciones

---

### Si desaparece el buscador de productos
- **Archivo dañado:** `views/cliente/partials/header.php`
- **Qué buscar:** El `<form id="form-buscar">` con el `<input name="buscar">`
- **Impacto:** El cliente no puede buscar productos

---

### Si el dropdown de "Mi cuenta" no aparece o no funciona
- **Archivo dañado:** `views/cliente/partials/header.php`
- **Qué buscar:** El bloque `<?php if (isset($_SESSION['usuario'])): ?>` con el `<div class="dropdown">`
- **Impacto:** El cliente no puede acceder a su perfil, pedidos, favoritos ni cerrar sesión

---

### Si el carrito (🛒) no muestra el contador de productos
- **Archivo dañado:** `views/cliente/partials/header.php`
- **Qué buscar:** El bloque `<?php if (!empty($_SESSION['carrito'])): ?>` dentro del ícono del carrito
- **Impacto:** El carrito funciona pero no muestra cuántos productos hay

---

### Si el asistente virtual (🤖) no aparece
- **Archivo dañado:** `views/cliente/partials/header.php`
- **Qué buscar:** El botón `<button id="asistente-btn">` y el `<div id="asistente-box">`
- **Impacto:** Solo se pierde el chatbot, el resto del sitio funciona normal

---

### Si las alertas (SweetAlert2) no aparecen después de una acción
- **Archivo dañado:** `views/cliente/partials/header.php`
- **Qué buscar:** El bloque `<?php if (isset($_SESSION['alert'])): ?>` con el `Swal.fire(...)`
- **Impacto:** Las acciones funcionan pero el cliente no ve confirmaciones ni errores

---

### Si el footer no aparece
- **Archivo dañado:** `views/cliente/partials/footer.php`
- **Qué buscar:** La etiqueta `<footer>` completa
- **Impacto:** Solo visual, el sitio funciona normal

---

### Si el modal de login no abre (no puede iniciar sesión)
- **Archivo dañado:** `public/index.php`
- **Qué buscar:** `<div class="modal fade" id="loginModal">`
- **También revisar:** `views/auth/login.php` — el formulario dentro del modal
- **Impacto:** Nadie puede iniciar sesión ni registrarse. Todo lo que requiere sesión queda bloqueado

---

### Si dentro del modal login no aparece el formulario
- **Archivo dañado:** `views/auth/login.php`
- **Qué buscar:** El `<div id="formLogin">` con el `<form action="...?action=login">`
- **Impacto:** El modal abre pero está vacío, no se puede iniciar sesión

---

### Si el botón "¿Olvidaste tu contraseña?" no hace nada
- **Archivo dañado:** `views/auth/login.php`
- **Qué buscar:** `<a href="#" onclick="showRecuperar()">` y la función `showRecuperar()` en el JS
- **También revisar:** `public/assets/js/store.js` — la transición entre modales
- **Impacto:** No se puede recuperar la contraseña, pero el login sigue funcionando

---

### Si el formulario de registro no aparece dentro del modal
- **Archivo dañado:** `views/auth/login.php`
- **Qué buscar:** `<div id="formRegistro">` y el enlace `onclick="showRegister()"`
- **Impacto:** No se pueden crear cuentas nuevas

---

### Si los botones +/- de cantidad en las tarjetas no funcionan
- **Archivo dañado:** `public/assets/js/store.js`
- **Qué buscar:** La función `cambiarCantidad(btn, cambio)`
- **Impacto:** La cantidad queda fija en 1, pero se puede comprar igual

---

### Si el corazón ❤️ de favoritos no responde al hacer clic
- **Archivo dañado:** `public/assets/js/store.js`
- **Qué buscar:** La función `toggleFavorito(btn, idProducto)`
- **También revisar:** `controllers/ClienteController.php` método `toggleFavorito()`
- **Impacto:** No se pueden agregar ni quitar favoritos

---

### Si los productos no aparecen en el home
- **Archivo dañado:** `views/cliente/home.php` o `models/Product.php`
- **Qué buscar en home.php:** El `<div class="products-grid">` con el `foreach ($productos as $p)`
- **Qué buscar en Product.php:** El método `filtrar()` o `obtenerActivos()`
- **Impacto:** El catálogo queda vacío

---

### Si los filtros del catálogo (precio, categoría, búsqueda) no funcionan
- **Archivo dañado:** `views/cliente/home.php`
- **Qué buscar:** El `<form method="GET" action="index.php">` con los inputs de filtro
- **También revisar:** `public/index.php` — las variables `$filtroCategoria`, `$filtroBuscar`, etc.
- **Impacto:** No se puede filtrar, se muestran todos los productos

---

### Si en el checkout no aparece la sección de métodos de pago
- **Archivo dañado:** `views/cliente/procesar_pago.php`
- **Qué buscar:** El bloque `<!-- MÉTODOS DE PAGO -->` con el `foreach ($metodosPago as $m)`
- **Impacto:** El cliente no puede seleccionar método y no puede pagar

---

### Si el modal de "Editar método de pago" no abre en el checkout
- **Archivo dañado:** `views/cliente/procesar_pago.php`
- **Qué buscar:** `<div class="modal fade" id="modalEditarMetodo">` y la función `abrirEditar()`
- **Impacto:** No se puede editar el método, pero sí se puede eliminar y crear uno nuevo

---

### Si el modal de "Editar dirección" no abre en el checkout
- **Archivo dañado:** `views/cliente/procesar_pago.php`
- **Qué buscar:** `<div class="modal fade" id="modalEditarDir">` y la función `abrirEditarDir()`
- **Impacto:** No se puede editar la dirección, pero sí eliminarla y crear una nueva

---

### Si el botón "Pagar" está desactivado aunque hay método y dirección
- **Archivo dañado:** `views/cliente/procesar_pago.php`
- **Qué buscar:** El `<button class="btn-pagar" disabled>` — puede estar mostrando la condición equivocada
- **También revisar:** `controllers/ClienteController.php` método `procesarPago()` — verifica `$tieneDireccion`
- **Impacto:** El cliente no puede completar el pago

---

## 🟡 ADMIN — Cosas que afectan al administrador

---

### Si el sidebar del admin no aparece (menú lateral)
- **Archivo dañado:** `views/admin/partials/sidebar.php`
- **Qué buscar:** La etiqueta `<aside class="admin-sidebar">` y los `<?php sl(...) ?>`
- **Impacto:** El admin no puede navegar entre secciones del panel

---

### Si el topbar del admin no aparece (barra superior con nombre y botón salir)
- **Archivo dañado:** `views/admin/partials/header.php`
- **Qué buscar:** `<nav class="admin-topbar">` con el nombre del usuario y el `<a class="btn-logout">`
- **Impacto:** Solo visual, el panel funciona pero sin barra superior

---

### Si el admin puede entrar sin estar logueado
- **Archivo dañado:** `views/admin/partials/auth_check.php`
- **Qué buscar:** `if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['rol'] != 1)`
- **Impacto:** Cualquier persona puede acceder al panel admin sin contraseña — **crítico de seguridad**

---

### Si las alertas del admin no aparecen
- **Archivo dañado:** `views/admin/partials/header.php`
- **Qué buscar:** El bloque `<?php if (isset($_SESSION['alert'])): ?>` con el `Swal.fire(...)`
- **Impacto:** Las acciones funcionan pero el admin no ve confirmaciones

---

## 🔵 GENERAL — Cosas que rompen todo

---

### Si absolutamente nada funciona (pantalla en blanco o error de conexión)
- **Archivo dañado:** `config/Database.php`
- **Qué buscar:** Las credenciales `$host`, `$db_name`, `$username`, `$password`
- **Impacto:** Todo el sitio muere

---

### Si la tienda cliente no carga en absoluto
- **Archivo dañado:** `public/index.php`
- **Qué buscar:** Los `require_once` de los controladores al inicio del archivo
- **Impacto:** Todo el sitio cliente muere

---

### Si una acción (agregar al carrito, pagar, etc.) redirige al home sin hacer nada
- **Archivo dañado:** `routes/web.php`
- **Qué buscar:** El `case` correspondiente a la acción en `dispatchWebRoutes()`
- **Impacto:** Esa acción específica no funciona
