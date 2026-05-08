# OrderController.php — Controlador de Pedidos (Admin)

## Ubicación
`controllers/OrderController.php`

## ¿Qué hace?
Controller heredado del flujo original de pedidos. Gestiona la creación de pedidos y el procesamiento de pago desde el lado del administrador.

## Nota
La lógica principal de creación de pedidos y pago fue migrada a `PagoController::confirmarPago()`. Este controller se mantiene por compatibilidad con el flujo admin (`action=guardar_pedido`).

## Modelos que usa
| Modelo | Para qué |
|---|---|
| `Order` | Crear pedido y agregar detalle |
| `Product` | Obtener precio y descontar stock |
| `Cart` | Obtener items del carrito |
| `Cliente` | Obtener id_cliente del usuario |
| `MetodoPago` | Guardar método de pago |

## Métodos
| Método | Action en index.php | Descripción |
|---|---|---|
| `guardar()` | `guardar_pedido` | Crea pedido desde el carrito de sesión |
| `procesarPago()` | *(interno)* | Crea pedido + guarda método de pago |

## Conectado con
- `index.php` (router)
- `models/Order.php`
- `models/Product.php`
- `models/Cart.php`
- `models/Cliente.php`
- `models/MetodoPago.php`
- `views/admin/pedidos/pedidos.php`
