# controllers/OfertaController.php

**Qué hace:** Gestiona las ofertas de productos desde el panel admin.

---

## Conectado con
- `models/Oferta.php`
- `config/Database.php` — desactiva ofertas anteriores directamente
- `views/admin/ofertas/ofertas.php`
- Las ofertas se aplican automáticamente en el catálogo cliente vía `Product::filtrar()`

---

## Acciones

| Action URL | Qué hace |
|---|---|
| `?action=guardar_oferta` | Crea oferta (desactiva la anterior del mismo producto) |
| `?action=editar_oferta` | Actualiza precio, descuento y fechas |
| `?action=activar_oferta` | Activa una oferta desactivada |
| `?action=desactivar_oferta` | Desactiva una oferta activa |

---

## Si se daña este archivo
- El admin no puede crear ni gestionar ofertas
- Las ofertas ya activas siguen mostrándose al cliente (no se rompe el catálogo)
