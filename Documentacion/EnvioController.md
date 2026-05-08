# EnvioController.php — Controlador de Envíos

## Ubicación
`controllers/EnvioController.php`

## ¿Qué hace?
Gestiona los registros de envío desde el panel administrador. Cuando un pedido se marca como "enviado", se crea automáticamente un registro en la tabla `envio`.

## Modelos / dependencias que usa
| Dependencia | Para qué |
|---|---|
| `Envio` | Crear y actualizar registros de envío |
| `Database` | Actualizar estado del pedido directamente |

## Métodos
| Método | Action en index.php | Descripción |
|---|---|---|
| `guardar()` | `guardar_envio` | Crea un envío y actualiza el pedido a "enviado" |
| `actualizarEstado()` | `actualizar_estado_envio` | Cambia el estado del envío; si es "entregado" actualiza también el pedido |
| `eliminar()` | `eliminar_envio` | Elimina un registro de envío por ID |

## Flujo — Crear envío
```
views/admin/envios/envios.php (formulario POST)
    → index.php?action=guardar_envio
        → EnvioController::guardar()
            → Envio::crear($id_pedido, $empresa, $direccion, $costo)
            → UPDATE pedido SET estado_pedido = 'enviado' WHERE id_pedido = X
            → redirect a envios.php con alerta de éxito
```

## Flujo — Actualizar a entregado
```
Admin cambia estado del envío a "entregado"
    → index.php?action=actualizar_estado_envio (POST)
        → EnvioController::actualizarEstado()
            → UPDATE envio SET estado = 'entregado'
            → SELECT id_pedido FROM envio WHERE id_envio = X
            → UPDATE pedido SET estado_pedido = 'entregado'
```

## Relación con pedidos
El envío está directamente ligado al pedido mediante `id_pedido`. Cuando el estado del envío cambia a `entregado`, el pedido también se actualiza automáticamente.

## Conectado con
- `index.php` (router)
- `models/Envio.php`
- `config/Database.php`
- `views/admin/envios/envios.php`
- `views/admin/pedidos/pedidos.php` (también crea envíos al cambiar estado)
