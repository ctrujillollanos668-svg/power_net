# controllers/DevolucionController.php

**Qué hace:** Gestiona solicitudes de devolución del cliente y su aprobación/rechazo por el admin.

---

## Conectado con
- `models/Devolucion.php`, `Pedido.php`, `Cliente.php`
- `views/cliente/solicitar_devolucion.php` — formulario del cliente
- `views/admin/pago/devolucion.php` — panel admin

---

## Acciones

| Action URL | Quién la usa | Qué hace |
|---|---|---|
| `?action=solicitar_devolucion` | Cliente | Muestra el formulario de devolución |
| `?action=procesar_devolucion` | Cliente | Guarda la solicitud |
| `?action=aprobar_devolucion` | Admin | Cambia estado a "aprobada" |
| `?action=rechazar_devolucion` | Admin | Cambia estado a "rechazada" con motivo |
| `?action=reembolso_devolucion` | Admin | Marca como "completada" |

---

## Condiciones para que el cliente pueda solicitar
- El pedido debe estar en estado `entregado`
- No debe existir ya una devolución para ese pedido
- El pedido debe pertenecer al cliente logueado

---

## Si se daña este archivo
- El cliente no puede solicitar devoluciones
- El admin no puede gestionar las solicitudes existentes
- Los pedidos y pagos no se ven afectados
