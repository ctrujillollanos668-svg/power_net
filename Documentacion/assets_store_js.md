# public/assets/js/store.js

**Qué es:** JavaScript de la tienda cliente. Maneja modales, favoritos y cantidades.

---

## Conectado con
- `#loginModal` en `public/index.php`
- `#recuperarModal` en `public/index.php`
- Endpoint `index.php?action=toggle_favorito` → `ClienteController::toggleFavorito()`
- Botones `.btn-favorito` en `views/cliente/home.php`
- Botones `+`/`-` de cantidad en las tarjetas de producto

---

## Funciones y qué rompen si fallan

| Función | Qué hace | Si se daña |
|---|---|---|
| `abrirLogin()` | Abre el modal `#loginModal` | El usuario no puede loguearse desde botones que lo requieren |
| `cambiarCantidad()` | Controla el selector +/- de cantidad | Los botones +/- no funcionan, la cantidad queda en 1 |
| `toggleFavorito()` | Marca/desmarca favorito sin recargar | El corazón no responde; si el usuario no está logueado no abre el login |
| Transición login→recuperar | Cierra un modal y abre el otro | El modal de recuperar no abre o el backdrop queda pegado en pantalla |

---

## Si se daña este archivo
- Los favoritos no funcionan (AJAX roto)
- Los botones +/- de cantidad no responden
- La transición entre modales falla (backdrop pegado)
- El resto del sitio (navegación, compra, pagos) sigue funcionando porque son formularios PHP normales
