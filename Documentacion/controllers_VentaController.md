# controllers/VentaController.php

**Qué hace:** Gestiona registros de ventas desde el panel admin (eliminar y cambiar estado).

---

## Conectado con
- `models/Venta.php`
- `views/admin/pago/ventas.php`

---

## Acciones

| Action URL | Qué hace |
|---|---|
| `?action=eliminar_venta` | Elimina el registro de venta (no el pedido) |
| `?action=actualizar_estado_venta` | Cambia el estado del pedido asociado |

---

## Si se daña este archivo
- El admin no puede gestionar ventas desde el panel
- Las ventas se siguen registrando automáticamente al pagar (eso lo hace `PagoController`)
- No afecta al cliente
