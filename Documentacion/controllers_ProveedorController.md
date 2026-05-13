# controllers/ProveedorController.php

**Qué hace:** CRUD de proveedores desde el panel admin.

---

## Conectado con
- `models/Proveedor.php`
- `views/admin/proveedores/proveedores.php`

---

## Acciones

| Action URL | Qué hace |
|---|---|
| `?action=guardar_proveedor` | Crea proveedor |
| `?action=editar_proveedor` | Edita nombre, correo y teléfono |
| `?action=eliminar_proveedor` | Elimina si no tiene productos asociados |

---

## Si se daña este archivo
- El admin no puede gestionar proveedores
- No afecta al cliente ni al catálogo
