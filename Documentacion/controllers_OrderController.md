# controllers/OrderController.php

**Qué hace:** Versión simplificada de creación de pedidos. Usado como flujo alternativo/legacy.

---

## Conectado con
- `models/Order.php`, `Product.php`, `Cart.php`, `Cliente.php`, `MetodoPago.php`

---

## Diferencia con PagoController
`OrderController` **no** registra en las tablas `pago`, `inventario` ni `venta`. No usa transacción atómica. Para el flujo real de compra del cliente se usa `PagoController::confirmarPago()`.

---

## Si se daña este archivo
Impacto mínimo. El flujo principal de compra usa `PagoController`, no este.
