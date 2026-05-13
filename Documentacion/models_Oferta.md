# models/Oferta.php

**Qué hace:** Gestiona las ofertas activas. Las ofertas se aplican automáticamente en el catálogo.

---

## Conectado con
- `config/Database.php`
- `controllers/OfertaController.php` — CRUD de ofertas admin
- `controllers/ClienteController.php` — carga ofertas para la vista `ofertas.php`
- `models/Product.php` — `filtrar()` hace JOIN con `oferta` para aplicar precios
- `controllers/PagoController.php` — verifica precio de oferta al confirmar el pago
- Tablas: `oferta`, `productos`, `categoria`

---

## Si se daña este archivo
- La vista de ofertas no carga
- Los precios de oferta no se aplican en el catálogo ni en el checkout
- Los productos se muestran con su precio normal
