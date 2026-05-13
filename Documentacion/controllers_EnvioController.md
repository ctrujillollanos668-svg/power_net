# controllers/EnvioController.php

**Qué hace:** Gestiona los envíos de pedidos desde el panel admin.

---

## Conectado con
- `models/Envio.php`
- `config/Database.php` — actualiza el estado del pedido directamente
- `views/admin/envios/envios.php`

---

## Acciones

| Action URL | Qué hace |
|---|---|
| `?action=guardar_envio` | Crea el envío y cambia el pedido a `enviado` |
| `?action=actualizar_envio` | Cambia estado del envío; si es `entregado`, actualiza el pedido también |
| `?action=eliminar_envio` | Elimina el registro de envío |

---

## Impacto en el pedido
- Crear envío → pedido pasa a `enviado`
- Marcar como entregado → pedido pasa a `entregado` (el cliente puede solicitar devolución)

---

## Si se daña este archivo
- El admin no puede registrar ni actualizar envíos
- Los pedidos quedan en estado `pendiente` para siempre
- El cliente nunca puede solicitar devoluciones (requiere estado `entregado`)
