# Favorito.php — Modelo de Favoritos

## Ubicación
`models/Favorito.php`

## Tabla en BD
`favorito`

## Columnas principales
| Columna | Tipo | Descripción |
|---|---|---|
| `id_favorito` | INT PK | Identificador único |
| `id_usuario` | INT FK | Usuario que marcó el favorito |
| `id_producto` | INT FK | Producto marcado |
| `creado_en` | DATETIME | Fecha en que se agregó |

## Métodos
| Método | Descripción |
|---|---|
| `toggle($id_usuario, $id_producto)` | Si existe lo elimina (retorna false); si no existe lo agrega (retorna true) |
| `obtenerIdsPorUsuario($id_usuario)` | Array de id_producto favoritos del usuario |
| `obtenerProductosPorUsuario($id_usuario)` | Productos completos con nombre de categoría |
| `contarPorProducto($id_producto)` | Cuántos usuarios tienen ese producto en favoritos |

## Flujo — Toggle (AJAX)
```
Cliente hace clic en ❤️ en home.php o detalle_producto.php
    → fetch POST index.php?action=toggle_favorito
        → ClienteController::toggleFavorito()
            → Favorito::toggle($id_usuario, $id_producto)
                → SELECT id_favorito WHERE id_usuario=? AND id_producto=?
                → Si existe: DELETE → retorna false
                → Si no existe: INSERT → retorna true
            → Favorito::contarPorProducto($id_producto)
            → JSON: { favorito: bool, total: int }
        → JS actualiza ícono ❤️/♡ sin recargar la página
```

## Marcado en el home
Al cargar el home, `index.php` llama a `Favorito::obtenerIdsPorUsuario()` para saber qué productos ya son favoritos del usuario y marcar el corazón en rojo.

## ¿Quién lo usa?
| Archivo | Para qué |
|---|---|
| `ClienteController.php` | Toggle y listar favoritos |
| `index.php` | Obtener IDs para marcar corazones en el home |
| `views/cliente/mis_favoritos.php` | Mostrar lista de favoritos |
| `views/cliente/home.php` | Mostrar corazón activo/inactivo |

## Conectado con
- `config/Database.php`
- `tabla: favorito`
- `tabla: productos`
- `tabla: categoria`
