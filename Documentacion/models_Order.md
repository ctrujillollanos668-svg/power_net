# models/Order.php

**Qué hace:** Igual que `Pedido.php` pero para el panel admin. Incluye datos del cliente en las consultas.

---

## Conectado con
- `config/Database.php`
- `controllers/OrderController.php`
- Tablas: `pedido`, `detalle_pedido`, `cliente`, `persona`, `pago`

---

## Diferencia con Pedido.php
`Order.php` trae el nombre del cliente y datos del pago. `Pedido.php` es para el flujo del cliente.

---

## Si se daña este archivo
Impacto mínimo. El flujo principal de compra usa `PagoController` con PDO directo, no este modelo.
