# OfertaController.php — Controlador de Ofertas

## Ubicación
`controllers/OfertaController.php`

## ¿Qué hace?
Gestiona el CRUD de ofertas desde el panel administrador. Una oferta vincula un producto con un precio especial y un rango de fechas de vigencia.

## Modelos que usa
| Modelo | Para qué |
|---|---|
| `Oferta` | Crear y desactivar ofertas |
| `Product` | Referencia al producto en oferta |
| `Database` | Activar y editar ofertas directamente |

## Métodos
| Método | Action en index.php | Descripción |
|---|---|---|
| `guardar()` | `guardar_oferta` | Crea una nueva oferta. Desactiva la anterior del mismo producto |
| `desactivar()` | `desactivar_oferta` | Pone `estado = 0` a una oferta |
| `activar()` | `activar_oferta` | Pone `estado = 1` a una oferta |
| `editar()` | `editar_oferta` | Actualiza precio, descuento y fechas |

## Validaciones en guardar()
- Todos los campos son obligatorios
- `fecha_fin` debe ser mayor o igual a `fecha_inicio`
- Antes de crear, desactiva todas las ofertas activas del mismo producto

## Flujo — Crear oferta
```
views/admin/ofertas/ofertas.php (formulario POST)
    → index.php?action=guardar_oferta
        → OfertaController::guardar()
            → UPDATE oferta SET estado=0 WHERE id_producto=X AND estado=1
            → Oferta::crear($id_producto, $precio, $descuento, $inicio, $fin)
            → redirect con alerta de éxito
```

## Impacto en el cliente
Cuando una oferta está activa (`estado=1` y dentro del rango de fechas), el producto aparece con precio tachado y precio de oferta en:
- `views/cliente/home.php`
- `views/cliente/ofertas.php`
- `views/cliente/detalle_producto.php`

El precio de oferta también se aplica al confirmar el pago en `PagoController::confirmarPago()`.

## Conectado con
- `index.php` (router)
- `models/Oferta.php`
- `models/Product.php`
- `config/Database.php`
- `views/admin/ofertas/ofertas.php`
- `views/cliente/ofertas.php`
- `views/cliente/home.php`
