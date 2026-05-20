# models/Inventario.php

**Qué hace:** Registra movimientos de stock (entradas y salidas) con trazabilidad.

---

## Conectado con
- `config/Database.php`
- `controllers/PagoController.php` — llama `salida()` después de cada compra
- Panel admin de inventario — usa `resumenStock()` y `obtenerMovimientos()`
- Tablas: `inventario`, `productos`, `categoria`

---

## Métodos clave

| Método | Cuándo se llama |
|---|---|
| `salida($id, $cantidad, $id_pedido)` | Al confirmar un pago |
| `entrada($id, $cantidad)` | Cuando el admin agrega stock |
| `resumenStock()` | Vista admin de inventario |

---

## Si se daña este archivo
- Los movimientos de inventario no se registran (pero el stock sí se descuenta, eso lo hace `PagoController` directamente)
- El admin no puede ver el historial de movimientos
- El panel de inventario no carga
