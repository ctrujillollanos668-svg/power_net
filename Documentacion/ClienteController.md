# ClienteController.php — Controlador del Cliente

## Ubicación
`controllers/ClienteController.php`

## ¿Qué hace?
Prepara los datos necesarios para todas las vistas del área del cliente autenticado. En lugar de retornar valores, escribe los datos en `$GLOBALS` para que el `index.php` los extraiga antes de incluir la vista.

## Modelos que usa
| Modelo | Para qué |
|---|---|
| `Cliente` | Obtener datos del cliente por usuario |
| `MetodoPago` | Listar métodos de pago del cliente |
| `Pedido` | Listar pedidos del cliente |
| `Favorito` | Listar y hacer toggle de favoritos |
| `Devolucion` | Listar devoluciones del cliente |
| `Cart` | Obtener items del carrito para el checkout |
| `Product` | Obtener datos de productos del carrito |

## Métodos
| Método | Action en index.php | Vista que activa |
|---|---|---|
| `mediosPago()` | `medios_pago` | `views/cliente/medios_pago.php` |
| `misPedidos()` | `mis_pedidos` | `views/cliente/mis_pedidos.php` |
| `misFavoritos()` | `mis_favoritos` | `views/cliente/mis_favoritos.php` |
| `misDevoluciones()` | `mis_devoluciones` | `views/cliente/mis_devoluciones.php` |
| `toggleFavorito()` | `toggle_favorito` | Responde JSON (AJAX) |
| `procesarPago()` | `procesar_pago` | `views/cliente/procesar_pago.php` |

## Variables que escribe en $GLOBALS
| Variable | Método | Usada en vista |
|---|---|---|
| `$GLOBALS['metodosMedios']` | mediosPago | medios_pago.php |
| `$GLOBALS['pedidosMis']` | misPedidos | mis_pedidos.php |
| `$GLOBALS['pedidoMisModel']` | misPedidos | mis_pedidos.php |
| `$GLOBALS['favoritosMis']` | misFavoritos | mis_favoritos.php |
| `$GLOBALS['favoritoIdsMis']` | misFavoritos | mis_favoritos.php |
| `$GLOBALS['devolucionesCli']` | misDevoluciones | mis_devoluciones.php |
| `$GLOBALS['metodosPago']` | procesarPago | procesar_pago.php |
| `$GLOBALS['direccionPago']` | procesarPago | procesar_pago.php |
| `$GLOBALS['tieneDireccion']` | procesarPago | procesar_pago.php |
| `$GLOBALS['itemsCarritoPago']` | procesarPago | procesar_pago.php |
| `$GLOBALS['totalCarritoPago']` | procesarPago | procesar_pago.php |

## Seguridad
Todos los métodos verifican `$_SESSION['usuario']`. Si no está logueado, redirigen a `index.php` con `$_SESSION['open_login'] = true` para abrir el modal de login automáticamente.

## Flujo — Toggle favorito (AJAX)
```
Cliente hace clic en ❤️
    → fetch POST index.php?action=toggle_favorito
        → ClienteController::toggleFavorito()
            → Favorito::toggle($id_usuario, $id_producto)
            → Responde JSON: { favorito: true/false, total: N }
        → JS actualiza el ícono en pantalla sin recargar
```

## Conectado con
- `index.php` (router — lo instancia como `$clienteCtrl`)
- `models/Cliente.php`
- `models/MetodoPago.php`
- `models/Pedido.php`
- `models/Favorito.php`
- `models/Devolucion.php`
- `models/Cart.php`
- `models/Product.php`
