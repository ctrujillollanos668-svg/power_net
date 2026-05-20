# controllers/ProductController.php

**Qué hace:** CRUD de productos e imágenes desde el panel admin.

---

## Conectado con
- `models/Product.php`
- `views/admin/productos/productos.php` — destino de todas las redirecciones
- `public/uploads/` — donde se guardan las imágenes subidas

---

## Acciones

| Action URL | Qué hace |
|---|---|
| `?action=guardar_producto` | Crea producto y sube imágenes |
| `?action=editar_producto` | Actualiza datos y agrega nuevas imágenes |
| `?action=toggle_producto` | Activa/desactiva visibilidad en el catálogo |
| `?action=eliminar_producto` | Elimina si no tiene pedidos; si tiene, bloquea |
| `?action=eliminar_imagen` | Borra una imagen individual del producto |

---

## Si se daña este archivo
- El admin no puede crear ni editar productos
- Las imágenes no se pueden subir ni eliminar
- El catálogo del cliente no se ve afectado (solo lectura)
