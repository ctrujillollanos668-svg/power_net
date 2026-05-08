# Envio.php — Modelo de Envíos

## Ubicación
`models/Envio.php`

## Tabla en BD
`envio`

## Columnas principales
| Columna | Tipo | Descripción |
|---|---|---|
| `id_envio` | INT PK | Identificador único |
| `empresa_envios` | VARCHAR | Nombre de la empresa de envío |
| `estado` | VARCHAR | en_camino / entregado |
| `costo` | DECIMAL | Costo del envío |
| `fecha_hora` | DATETIME | Fecha de creación del envío |
| `direccion_envio` | VARCHAR | Dirección de destino |
| `id_pedido` | INT FK | Pedido al que pertenece |

## Métodos
| Método | Descripción |
|---|---|
| `crear($id_pedido, $empresa, $direccion, $costo)` | Crea un envío con estado `en_camino` |
| `actualizarEstado($id_pedido, $estado)` | Actualiza el estado del envío por id_pedido |
| `obtenerPorPedido($id_pedido)` | Trae el envío de un pedido |
| `existePorPedido($id_pedido)` | Verifica si ya existe envío para ese pedido |

## Flujo de creación automática
Cuando el admin cambia el estado de un pedido a "enviado" en `pedidos.php`, se llama automáticamente a `Envio::crear()` si no existe ya un envío para ese pedido.

```
Admin → cambiar estado a "enviado"
    → pedidos.php → GET ?cambiar_estado=1&estado=enviado
        → UPDATE pedido SET estado_pedido = 'enviado'
        → Envio::existePorPedido($id) → false
            → Envio::crear($id, 'Power Net Envíos', $direccion_cliente, 0)
```

## ¿Quién lo usa?
| Archivo | Para qué |
|---|---|
| `EnvioController.php` | CRUD de envíos desde el admin |
| `views/admin/pedidos/pedidos.php` | Crea envío al cambiar estado |
| `views/admin/envios/envios.php` | Panel de gestión de envíos |
| `views/cliente/mis_pedidos.php` | Muestra estado del envío al cliente |

## Conectado con
- `config/Database.php`
- `tabla: envio`
- `tabla: pedido`
