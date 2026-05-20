# models/Devolucion.php

**Qué hace:** Crea y gestiona solicitudes de devolución con su detalle de productos.

---

## Conectado con
- `config/Database.php`
- `controllers/DevolucionController.php` — toda la lógica de devoluciones
- Tablas: `devolucion`, `detalle_devolucion`, `pedido`, `cliente`, `persona`, `productos`

---

## Estados de una devolución
`pendiente` → `aprobada` o `rechazada` → `completada`

---

## Si se daña este archivo
- El cliente no puede solicitar devoluciones
- El admin no puede ver ni gestionar las solicitudes
