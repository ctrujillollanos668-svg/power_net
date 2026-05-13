# controllers/CartController.php

**Qué hace:** Gestiona el carrito de compras (agregar, quitar, vaciar).

---

## Conectado con
- `models/Cart.php` — guarda el carrito en `$_SESSION['carrito']`
- `models/Product.php` — obtiene datos del producto para la vista
- `views/cliente/carrito.php` — vista del carrito
- `views/cliente/procesar_pago.php` — destino del botón "Comprar ahora"

---

## Acciones

| Action URL | Qué hace |
|---|---|
| `?action=agregar_carrito` | Agrega producto. Si viene `ir_a_pago`, va directo al checkout |
| `?action=aumentar_carrito` | +1 a la cantidad |
| `?action=disminuir_carrito` | -1 a la cantidad (si llega a 0, lo elimina) |
| `?action=eliminar_carrito` | Quita el producto del carrito |
| `?action=vaciar_carrito` | Vacía todo el carrito |

---

## El carrito vive en sesión
```
$_SESSION['carrito'] = [ id_producto => cantidad ]
```
Si la sesión expira o el usuario cierra el navegador, el carrito se pierde.

---

## Si se daña este archivo
- No se pueden agregar productos al carrito
- El botón "Comprar ahora" no funciona
- El carrito no se puede vaciar ni modificar
- El pago no se puede iniciar
