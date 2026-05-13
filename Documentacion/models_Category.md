# models/Category.php

**Qué hace:** CRUD de categorías con validación de productos asignados.

---

## Conectado con
- `config/Database.php`
- `controllers/CategoryController.php` — CRUD admin
- `public/index.php` — carga categorías activas para los filtros del home
- Tablas: `categoria`, `productos`

---

## Si se daña este archivo
- El admin no puede gestionar categorías
- Los filtros del catálogo cliente no cargan las categorías
