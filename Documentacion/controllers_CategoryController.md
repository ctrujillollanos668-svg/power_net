# controllers/CategoryController.php

**Qué hace:** CRUD de categorías desde el panel admin.

---

## Conectado con
- `models/Category.php`
- `views/admin/categoria/categorias.php` — destino de redirecciones

---

## Acciones

| Action URL | Qué hace |
|---|---|
| `?action=guardar_categoria` | Crea categoría |
| `?action=editar_categoria` | Edita nombre y descripción |
| `?action=eliminar_categoria` | Elimina si no tiene productos asignados |
| `?action=toggle_categoria` | Activa/desactiva; bloquea si tiene productos |

---

## Si se daña este archivo
- El admin no puede gestionar categorías
- Los filtros del catálogo cliente siguen funcionando con las categorías ya existentes
