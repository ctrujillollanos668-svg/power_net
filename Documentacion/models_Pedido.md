# models/Pedido.php

**Qué hace:** Crea pedidos y consulta el historial del cliente. Usado en el flujo del cliente.

---

## Conectado con
- `config/Database.php`
- `controllers/ClienteController.php` — carga `mis_pedidos`
- `controllers/DevolucionController.php` — verifica estado del pedido
- Tablas: `pedido`, `detalle_pedido`, `pago`, `productos`

---

## Métodos clave

| Método | Qué hace |
|---|---|
| `obtenerPorCliente($id)` | Historial de pedidos con datos del pago |
| `obtenerDetalle($id_pedido)` | Productos de un pedido |
| `crear($data)` | Crea un pedido nuevo |
| `guardarDetalle()` | Guarda cada producto del pedido |

---

## Si se daña este archivo
- El cliente no puede ver sus pedidos
- No se puede solicitar devolución (necesita verificar el pedido)
