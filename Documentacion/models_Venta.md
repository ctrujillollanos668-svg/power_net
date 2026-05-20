# models/Venta.php

**Qué hace:** Registra ventas cerradas y provee estadísticas para el admin.

---

## Conectado con
- `config/Database.php`
- `controllers/PagoController.php` — llama `registrar()` al confirmar el pago
- `controllers/VentaController.php` — gestión admin
- Tablas: `venta`, `pedido`, `detalle_pedido`, `cliente`, `persona`, `pago`, `productos`

---

## Métodos clave

| Método | Cuándo se usa |
|---|---|
| `registrar()` | Al confirmar un pago (automático) |
| `obtenerTodas()` | Panel admin de ventas |
| `ventasPorDia()` | Gráficas del dashboard |
| `topProductos()` | Dashboard admin |
| `filtrar()` | Búsqueda en panel admin |

---

## Si se daña este archivo
- Las ventas no se registran al pagar (impacto en reportes)
- El panel de ventas del admin no carga
- El pago en sí sigue funcionando (el pedido y el pago se crean antes de llamar a este modelo)
