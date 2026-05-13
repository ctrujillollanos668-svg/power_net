# controllers/ClienteController.php

**Qué hace:** Prepara los datos para todas las vistas del cliente (pedidos, favoritos, pago, ofertas, etc.).

---

## Conectado con
- `models/Cliente.php`, `MetodoPago.php`, `Pedido.php`, `Favorito.php`, `Devolucion.php`, `Cart.php`, `Product.php`, `Oferta.php`, `User.php`
- Pasa datos a las vistas usando `$GLOBALS`
- `store.js` — el toggle de favoritos llama a `?action=toggle_favorito` y recibe JSON

---

## Acciones y vista que activan

| Action | Vista que carga | Requiere login |
|---|---|---|
| `?action=medios_pago` | `medios_pago.php` | ✅ |
| `?action=mis_pedidos` | `mis_pedidos.php` | ✅ |
| `?action=mis_favoritos` | `mis_favoritos.php` | ✅ |
| `?action=mis_devoluciones` | `mis_devoluciones.php` | ✅ |
| `?action=toggle_favorito` | Responde JSON (AJAX) | ✅ |
| `?action=procesar_pago` | `procesar_pago.php` | ✅ |
| `?action=ofertas` | `ofertas.php` | ❌ |
| `?action=detalle_producto` | `detalle_producto.php` | ❌ |
| `?action=factura` | `factura.php` | ✅ |

---

## Si no hay sesión activa
Redirige a `index.php` con `$_SESSION['open_login'] = true` → se abre el `#loginModal` automáticamente.

---

## Si se daña este archivo
- Las vistas de perfil, pedidos, favoritos y pago no cargan
- Los favoritos (corazón) dejan de funcionar aunque el JS esté bien
- El checkout no se puede iniciar
