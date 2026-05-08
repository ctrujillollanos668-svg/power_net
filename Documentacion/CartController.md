# CartController.php — Controlador del Carrito

## Ubicación
`controllers/CartController.php`

## ¿Qué hace?
Gestiona todas las operaciones del carrito de compras. El carrito se almacena en `$_SESSION['carrito']` como un array `[id_producto => cantidad]`.

## Modelos que usa
| Modelo | Para qué |
|---|---|
| `Cart` | Operaciones sobre la sesión del carrito |
| `Product` | Obtener datos del producto (nombre, precio, imagen) |

## Métodos
| Método | Action en index.php | Descripción |
|---|---|---|
| `agregar()` | `agregar_carrito` | Agrega un producto. Si viene `ir_a_pago=1`, redirige directo al checkout |
| `aumentar()` | `aumentar_carrito` | Suma 1 unidad al producto en el carrito |
| `disminuir()` | `disminuir_carrito` | Resta 1 unidad; si llega a 0 lo elimina |
| `eliminar()` | `eliminar_carrito` | Elimina un producto del carrito |
| `vaciar()` | `vaciar_carrito` | Vacía todo el carrito |
| `ver()` | *(interno)* | Prepara datos para la vista carrito |

## Flujo — Agregar al carrito
```
Vista home.php (formulario POST)
    → index.php?action=agregar_carrito
        → CartController::agregar()
            → Cart::agregar($id_producto)
                → $_SESSION['carrito'][$id]++
            → redirect a la página anterior
```

## Flujo — Comprar ahora
```
Vista home.php (botón Comprar)
    → index.php?action=agregar_carrito (con ir_a_pago=1)
        → CartController::agregar()
            → Cart::agregar($id_producto)
            → redirect a index.php?action=procesar_pago
```

## Vista que renderiza
- `views/cliente/carrito.php`

## Conectado con
- `index.php` (router)
- `models/Cart.php`
- `models/Product.php`
- `views/cliente/carrito.php`
- `views/cliente/home.php` (formularios de origen)
