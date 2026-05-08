# Cart.php — Modelo del Carrito

## Ubicación
`models/Cart.php`

## ¿Qué es?
Clase estática que gestiona el carrito de compras usando `$_SESSION['carrito']`. No tiene conexión a base de datos — todo se almacena en la sesión del navegador.

## Estructura del carrito en sesión
```php
$_SESSION['carrito'] = [
    id_producto => cantidad,  // ej: [5 => 2, 12 => 1]
]
```

## Métodos estáticos
| Método | Descripción |
|---|---|
| `obtener()` | Retorna el array del carrito o `[]` si está vacío |
| `agregar($id)` | Agrega 1 unidad del producto; si ya existe, incrementa |
| `aumentar($id)` | Suma 1 unidad al producto |
| `disminuir($id)` | Resta 1 unidad; si llega a 0 lo elimina del carrito |
| `eliminar($id)` | Elimina completamente el producto del carrito |
| `vaciar()` | Destruye `$_SESSION['carrito']` |

## ¿Quién lo usa?
| Archivo | Para qué |
|---|---|
| `CartController.php` | Todas las operaciones del carrito |
| `ClienteController.php` | Obtener items para el checkout |
| `PagoController.php` | Obtener items y vaciar al confirmar pago |
| `index.php` | Calcular totales para la vista carrito |

## Ciclo de vida del carrito
```
Cliente agrega producto → Cart::agregar()
    → $_SESSION['carrito'][id]++

Cliente confirma pago → PagoController::confirmarPago()
    → Procesa la compra
    → Cart::vaciar()
        → unset($_SESSION['carrito'])
```
