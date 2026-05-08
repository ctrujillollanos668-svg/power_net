# DevolucionController.php — Controlador de Devoluciones

## Ubicación
`controllers/DevolucionController.php`

## ¿Qué hace?
Maneja el ciclo completo de devoluciones: el cliente solicita, el admin aprueba/rechaza, y finalmente se procesa el reembolso.

## Modelos que usa
| Modelo | Para qué |
|---|---|
| `Devolucion` | CRUD de devoluciones y cambio de estado |
| `Pedido` | Verificar que el pedido esté entregado y obtener su detalle |
| `Cliente` | Obtener el id_cliente del usuario en sesión |

## Métodos
| Método | Action en index.php | Quién lo usa |
|---|---|---|
| `solicitar()` | `solicitar_devolucion` | Cliente (GET — muestra formulario) |
| `procesar()` | `procesar_devolucion` | Cliente (POST — guarda la solicitud) |
| `aprobar()` | `aprobar_devolucion` | Admin |
| `rechazar()` | `rechazar_devolucion` | Admin (POST con motivo) |
| `reembolso()` | `reembolso_devolucion` | Admin |

## Estados del ciclo de devolución
```
pendiente → aprobada → completada
pendiente → rechazada
```

## Validaciones en solicitar()
- El usuario debe estar logueado
- El pedido debe pertenecer al cliente
- El pedido debe estar en estado `entregado`
- No debe existir ya una devolución para ese pedido

## Flujo — Cliente solicita devolución
```
mis_pedidos.php → botón "Solicitar devolución"
    → index.php?action=solicitar_devolucion&id=X
        → DevolucionController::solicitar()
            → Pedido::obtenerDetalle($id_pedido)
            → include views/cliente/solicitar_devolucion.php
                → Cliente selecciona productos y motivo
                → POST index.php?action=procesar_devolucion
                    → DevolucionController::procesar()
                        → Devolucion::crear($id_pedido, $monto, $items)
                        → redirect a mis_pedidos con alerta de éxito
```

## Flujo — Admin gestiona devolución
```
views/admin/pago/devolucion.php
    → Botón Aprobar → SweetAlert2 → confirmarAprobar()
        → index.php?action=aprobar_devolucion&id=X
            → DevolucionController::aprobar()
                → Devolucion::cambiarEstado($id, 'aprobada')
                → redirect con alerta toast

    → Botón Rechazar → Modal con textarea
        → POST index.php?action=rechazar_devolucion
            → DevolucionController::rechazar()
                → Devolucion::cambiarEstado($id, 'rechazada', $motivo)

    → Botón Reembolso → SweetAlert2 → confirmarReembolso()
        → index.php?action=reembolso_devolucion&id=X
            → DevolucionController::reembolso()
                → Devolucion::cambiarEstado($id, 'completada')
```

## Conectado con
- `index.php` (router)
- `models/Devolucion.php`
- `models/Pedido.php`
- `models/Cliente.php`
- `views/cliente/solicitar_devolucion.php`
- `views/cliente/mis_devoluciones.php`
- `views/admin/pago/devolucion.php`
