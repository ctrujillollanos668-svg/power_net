# models/Cart.php

**Qué hace:** Maneja el carrito de compras en sesión PHP. Sin base de datos.

---

## Conectado con
- `$_SESSION['carrito']` — array `[id_producto => cantidad]`
- `controllers/CartController.php` — lo llama para todas las operaciones
- `controllers/PagoController.php` — llama `Cart::obtener()` y `Cart::vaciar()` al pagar

---

## Métodos

| Método | Qué hace |
|---|---|
| `obtener()` | Retorna el carrito completo |
| `agregar($id)` | Agrega o incrementa cantidad |
| `aumentar($id)` | +1 a la cantidad |
| `disminuir($id)` | -1; si llega a 0 lo elimina |
| `eliminar($id)` | Quita el producto |
| `vaciar()` | Borra todo el carrito |

---

## Si se daña este archivo
- El carrito deja de funcionar completamente
- No se puede agregar, modificar ni vaciar
- El pago no puede leer los productos → no se puede comprar
