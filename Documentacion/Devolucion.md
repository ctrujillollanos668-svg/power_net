# Devolucion.php — Modelo de Devoluciones

## Ubicación
`models/Devolucion.php`

## Tablas en BD
- `devolucion` — cabecera de la devolución
- `detalle_devolucion` — productos incluidos en la devolución

## Columnas — devolucion
| Columna | Tipo | Descripción |
|---|---|---|
| `id_devolucion` | INT PK | Identificador único |
| `fecha_devolucion` | DATETIME | Fecha de la solicitud |
| `monto_devolucion` | DECIMAL | Monto total a devolver |
| `id_pedido` | INT FK | Pedido al que pertenece |
| `estado` | VARCHAR | pendiente / aprobada / rechazada / completada |
| `motivo` | TEXT | Motivo general del cliente |
| `motivo_rechazo` | TEXT | Motivo del rechazo por el admin |

## Columnas — detalle_devolucion
| Columna | Tipo | Descripción |
|---|---|---|
| `id_detalle` | INT PK | Identificador único |
| `id_devolucion` | INT FK | Devolución a la que pertenece |
| `id_producto` | INT FK | Producto devuelto |
| `cantidad` | INT | Cantidad a devolver |
| `motivo` | VARCHAR | Motivo por producto |

## Métodos
| Método | Descripción |
|---|---|
| `crear($id_pedido, $monto, $items, $motivo)` | Inserta cabecera + detalle de productos |
| `existePorPedido($id_pedido)` | Verifica si ya hay devolución para ese pedido |
| `obtenerPorCliente($id_cliente)` | Lista devoluciones del cliente |
| `obtenerPorClienteConEstado($id_cliente)` | Lista con estado y datos del pedido |
| `obtenerDetalle($id_devolucion)` | Productos de una devolución con nombre |
| `obtenerTodas()` | Todas las devoluciones para el admin |
| `cambiarEstado($id, $estado, $motivo_rechazo)` | Actualiza estado y motivo de rechazo |
| `filtrar($estado, $desde, $hasta)` | Filtra para el panel admin |

## Estados del ciclo
```
pendiente ──→ aprobada ──→ completada
    └──────→ rechazada
```

## ¿Quién lo usa?
| Archivo | Para qué |
|---|---|
| `DevolucionController.php` | Toda la lógica de devoluciones |
| `views/admin/pago/devolucion.php` | Panel admin |
| `views/cliente/mis_devoluciones.php` | Vista del cliente |
| `views/cliente/solicitar_devolucion.php` | Formulario de solicitud |

## Conectado con
- `config/Database.php`
- `tabla: devolucion`
- `tabla: detalle_devolucion`
- `tabla: pedido`
- `tabla: cliente`
- `tabla: persona`
- `tabla: productos`
