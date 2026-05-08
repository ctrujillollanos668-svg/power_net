# CategoryController.php — Controlador de Categorías

## Ubicación
`controllers/CategoryController.php`

## ¿Qué hace?
Gestiona el CRUD de categorías desde el panel administrador. Incluye validación para no desactivar una categoría que tenga productos asignados.

## Modelos que usa
| Modelo | Para qué |
|---|---|
| `Category` | Todas las operaciones sobre la tabla `categoria` |

## Métodos
| Método | Action en index.php | Descripción |
|---|---|---|
| `guardar()` | `guardar_categoria` | Crea una nueva categoría (POST) |
| `editar()` | `editar_categoria` | Actualiza nombre y descripción (POST) |
| `toggle()` | `toggle_categoria` | Activa/desactiva. Bloquea si tiene productos asignados |

## Flujo — Toggle con validación
```
Admin hace clic en activar/desactivar
    → index.php?action=toggle_categoria&id=X
        → CategoryController::toggle()
            → Category::toggleEstado($id)
                → Verifica COUNT(*) en productos WHERE id_categoria = X
                → Si tiene productos: retorna ['bloqueado'=>true, 'productos'=>[...]]
                → Si no tiene: UPDATE categoria SET estado = IF(estado=1,0,1)
            → $_SESSION['alert'] con mensaje de éxito o advertencia
            → redirect a la vista de categorías
```

## Alertas que genera
| Situación | Tipo | Mensaje |
|---|---|---|
| Categoría desactivada con éxito | `success` | "Estado actualizado" |
| Categoría tiene productos | `warning` | Lista los productos asignados |

## Conectado con
- `index.php` (router)
- `models/Category.php`
- `views/admin/categoria/categorias.php` (vista de origen y destino)
