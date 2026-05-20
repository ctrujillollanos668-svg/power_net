# controllers/PagoController.php

**Qué hace:** Gestiona métodos de pago, dirección de envío y el proceso de pago completo.

---

## Conectado con
- `models/Cliente.php`, `MetodoPago.php`, `Cart.php`, `Product.php`, `Inventario.php`, `Venta.php`
- `views/cliente/procesar_pago.php` — vista del checkout
- `views/cliente/pago_exitoso.php` — destino tras pago exitoso
- `$_SESSION['pago_exitoso']` — guarda factura e ID de pedido para mostrar en la vista

---

## Acciones

| Action | Qué hace |
|---|---|
| `?action=guardar_metodo` | Guarda tarjeta/cuenta del cliente |
| `?action=editar_metodo` | Edita un método de pago |
| `?action=eliminar_metodo` | Elimina un método de pago |
| `?action=guardar_direccion` | Guarda dirección de envío |
| `?action=editar_direccion` | Edita la dirección |
| `?action=eliminar_direccion` | Borra la dirección |
| `?action=confirmar_pago` | **Procesa el pago completo** |

---

## `confirmar_pago` — lo más crítico

Hace todo en una sola transacción:
1. Valida que haya dirección y método de pago seleccionado
2. Verifica stock de cada producto
3. Crea el pedido, detalle, descuenta stock y registra el pago
4. Si algo falla → `rollBack()`, el cliente ve un error y no se cobra nada

---

## Si se daña este archivo
- El cliente no puede guardar ni editar métodos de pago
- No puede guardar su dirección de envío
- **El pago no se puede completar** — impacto directo en ventas
