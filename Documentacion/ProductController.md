# ProductController.php — Controlador de Productos

## Ubicación
`controllers/ProductController.php`

## ¿Qué hace?
Gestiona el CRUD completo de productos desde el panel administrador, incluyendo la subida y eliminación de imágenes múltiples.

## Modelos que usa
| Modelo | Para qué |
|---|---|
| `Product` | Todas las operaciones sobre la tabla `productos` e `imagenes_producto` |

## Métodos
| Método | Action en index.php | Descripción |
|---|---|---|
| `guardar()` | `guardar_producto` | Crea producto y sube imágenes a `public/uploads/` |
| `editar()` | `editar_producto` | Actualiza datos y agrega nuevas imágenes si se suben |
| `eliminar()` | `eliminar_producto` | Elimina si no tiene pedidos; si tiene, bloquea con alerta |
| `toggle()` | `toggle_producto` | Activa/desactiva disponibilidad |
| `eliminarImagen()` | `eliminar_imagen` | Elimina imagen individual del disco y de la BD |

## Flujo — Crear producto con imágenes
```
views/admin/productos/productos.php (formulario multipart POST)
    → index.php?action=guardar_producto
        → ProductController::guardar()
            → Product::crear($nombre, $descripcion, $precio, $stock, $categoria)
                → INSERT INTO productos → retorna $id_producto
            → Por cada imagen en $_FILES['imagenes']:
                → move_uploaded_file($tmp, public/uploads/timestamp_nombre.ext)
                → Product::guardarImagen($id_producto, $nombreArchivo)
                    → INSERT INTO imagenes_producto
            → redirect a productos.php
```

## Flujo — Eliminar producto
```
Admin hace clic en eliminar
    → index.php?action=eliminar_producto&id=X
        → ProductController::eliminar()
            → Product::eliminar($id)
                → SELECT COUNT(*) FROM detalle_pedido WHERE id_producto = X
                → Si tiene pedidos: retorna ['bloqueado'=>true, 'mensaje'=>'...']
                → Si no tiene: elimina imágenes físicas + DELETE FROM productos
            → $_SESSION['alert'] con resultado
```

## Almacenamiento de imágenes
- Las imágenes se guardan en `public/uploads/`
- El nombre del archivo es `timestamp_nombreoriginal.ext`
- La ruta relativa se guarda en la tabla `imagenes_producto`

## Conectado con
- `index.php` (router)
- `models/Product.php`
- `public/uploads/` (sistema de archivos)
- `views/admin/productos/productos.php`
