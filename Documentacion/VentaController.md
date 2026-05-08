# VentaController.php — Controlador de Ventas

## Ubicación
`controllers/VentaController.php`

## ¿Qué hace?
Gestiona las operaciones administrativas sobre los registros de ventas: eliminar un registro y actualizar el estado de la venta (que a su vez actualiza el estado del pedido).

## Modelos que usa
| Modelo | Para qué |
|---|---|
| `Venta` | Eliminar y actualizar estado de ventas |

## Métodos
| Método | Action en index.php | Descripción |
|---|---|---|
| `eliminar()` | `eliminar_venta` | Elimina un registro de venta por ID |
| `actualizarEstado()` | `actualizar_estado_venta` | Actualiza el estado del pedido vinculado a la venta |

## Nota importante
`actualizarEstado()` no actualiza la tabla `venta` directamente, sino la tabla `pedido` mediante un JOIN. Esto mantiene sincronizados el estado del pedido y el registro de venta.

## Flujo
```
views/admin/pago/ventas.php
    → Botón eliminar → index.php?action=eliminar_venta&id=X
        → VentaController::eliminar()
            → Venta::eliminar($id)
                → DELETE FROM venta WHERE id_venta = X
            → redirect con alerta de éxito

    → Formulario cambiar estado → POST index.php?action=actualizar_estado_venta
        → VentaController::actualizarEstado()
            → Venta::actualizarEstado($id_venta, $estado)
                → UPDATE pedido p INNER JOIN venta v SET p.estado_pedido = ?
            → redirect con alerta de éxito
```

## Conectado con
- `index.php` (router)
- `models/Venta.php`
- `views/admin/pago/ventas.php`
