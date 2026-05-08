# Inventario.php — Modelo de Inventario

## Ubicación
`models/Inventario.php`

## Tabla en BD
`inventario`

## Columnas principales
| Columna | Tipo | Descripción |
|---|---|---|
| `id_inventario` | INT PK | Identificador único |
| `id_producto` | INT FK | Producto afectado |
| `tipo` | VARCHAR | `entrada` o `salida` |
| `cantidad` | INT | Unidades del movimiento |
| `stock_anterior` | INT | Stock antes del movimiento |
| `stock_nuevo` | INT | Stock después del movimiento |
| `motivo` | VARCHAR | Razón del movimiento |
| `id_pedido` | INT FK | Pedido relacionado (si aplica) |

## Métodos
| Método | Descripción |
|---|---|
| `registrarMovimiento($id_producto, $tipo, $cantidad, $motivo, $id_pedido)` | Registra cualquier movimiento de stock |
| `entrada($id_producto, $cantidad, $motivo)` | Suma stock + reactiva producto si estaba inactivo |
| `salida($id_producto, $cantidad, $id_pedido)` | Registra salida por compra de cliente |
| `obtenerMovimientos($limite)` | Lista los últimos N movimientos con nombre del producto |
| `obtenerPorProducto($id_producto)` | Historial de un producto específico |
| `resumenStock()` | Stock actual de todos los productos con valor de inventario |

## Flujo — Salida automática al comprar
```
Cliente confirma pago
    → PagoController::confirmarPago()
        → db->commit() (pedido + detalle + pago)
        → Por cada producto:
            → Inventario::salida($id_producto, $cantidad, $id_pedido)
                → registrarMovimiento('salida', ...)
                    → SELECT stock FROM productos
                    → INSERT INTO inventario (stock_anterior, stock_nuevo, ...)
```

## Flujo — Entrada manual (admin)
```
views/admin/inventario/inventario.php
    → Formulario de ajuste de stock
        → Inventario::entrada($id_producto, $cantidad, 'ajuste_admin')
            → UPDATE productos SET stock = stock + cantidad
            → Si stock > 0: UPDATE productos SET disponibilidad = 1
            → registrarMovimiento('entrada', ...)
```

## ¿Quién lo usa?
| Archivo | Para qué |
|---|---|
| `PagoController.php` | Registrar salida al confirmar compra |
| `views/admin/inventario/inventario.php` | Panel de gestión de inventario |

## Conectado con
- `config/Database.php`
- `tabla: inventario`
- `tabla: productos`
