# ProveedorController.php — Controlador de Proveedores

## Ubicación
`controllers/ProveedorController.php`

## ¿Qué hace?
Gestiona el CRUD de proveedores desde el panel administrador. Solo el administrador puede acceder a este módulo.

## Modelos que usa
| Modelo | Para qué |
|---|---|
| `Proveedor` | Todas las operaciones sobre la tabla `proveedor` |

## Métodos
| Método | Action en index.php | Descripción |
|---|---|---|
| `guardar()` | `guardar_proveedor` | Crea un nuevo proveedor |
| `editar()` | `editar_proveedor` | Actualiza nombre, correo y teléfono |
| `eliminar()` | `eliminar_proveedor` | Elimina si no tiene productos asociados |

## Validación al eliminar
Antes de eliminar, verifica si el proveedor tiene productos asociados en la tabla `productos`. Si los tiene, retorna `['bloqueado' => true]` y muestra una alerta de advertencia.

## Flujo
```
views/admin/proveedores/proveedores.php
    → Formulario POST → index.php?action=guardar_proveedor
        → ProveedorController::guardar()
            → Proveedor::crear($nombre, $correo, $telefono)
            → redirect con alerta de éxito

    → Botón eliminar → index.php?action=eliminar_proveedor&id=X
        → ProveedorController::eliminar()
            → Proveedor::eliminar($id)
                → Verifica productos asociados
                → Si bloqueado: alerta warning
                → Si libre: DELETE FROM proveedor
```

## Conectado con
- `index.php` (router)
- `models/Proveedor.php`
- `views/admin/proveedores/proveedores.php`
