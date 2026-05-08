# Category.php — Modelo de Categorías

## Ubicación
`models/Category.php`

## Tabla en BD
`categoria`

## Columnas principales
| Columna | Tipo | Descripción |
|---|---|---|
| `id_categoria` | INT PK | Identificador único |
| `nombre_categoria` | VARCHAR | Nombre de la categoría |
| `descripcion` | TEXT | Descripción |
| `estado` | TINYINT | 1=activa, 0=inactiva |

## Métodos
| Método | Descripción |
|---|---|
| `obtenerTodas()` | Trae todas las categorías (admin) |
| `obtenerActivas()` | Solo las activas (estado=1) para el cliente |
| `obtenerConConteo()` | Trae categorías con total de productos asignados |
| `guardar($nombre, $descripcion)` | Crea una nueva categoría |
| `editar($id, $nombre, $descripcion)` | Actualiza nombre y descripción |
| `toggle($id)` | Alterna estado sin validación |
| `toggleEstado($id)` | Alterna estado con validación de productos asignados |

## toggleEstado() — Lógica de validación
```
Si la categoría tiene productos asignados:
    → Retorna ['bloqueado'=>true, 'total'=>N, 'productos'=>[nombres]]
    → CategoryController muestra alerta con la lista de productos

Si no tiene productos:
    → UPDATE categoria SET estado = IF(estado=1,0,1)
    → Retorna ['bloqueado'=>false]
```

## ¿Quién lo usa?
| Archivo | Para qué |
|---|---|
| `CategoryController.php` | CRUD desde el admin |
| `views/cliente/partials/header.php` | Menú de categorías |
| `index.php` | Filtro de productos por categoría en el home |

## Conectado con
- `config/Database.php`
- `tabla: categoria`
- `tabla: productos` (para validar antes de desactivar)
