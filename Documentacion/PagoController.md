# PagoController.php — Controlador de Pagos

## Ubicación
`controllers/PagoController.php`

## ¿Qué hace?
Es el controller más crítico del sistema. Gestiona métodos de pago, direcciones de envío del cliente, y ejecuta la **transacción completa de compra** con PDO.

## Modelos que usa
| Modelo | Para qué |
|---|---|
| `Cliente` | Obtener y crear el registro del cliente |
| `MetodoPago` | CRUD de métodos de pago |
| `Cart` | Obtener items del carrito |
| `Product` | Verificar stock y precio |
| `Inventario` | Registrar salida de stock |
| `Venta` | Registrar la venta cerrada |
| `Database` | Conexión PDO para la transacción |

## Métodos
| Método | Action en index.php | Descripción |
|---|---|---|
| `guardarMetodo()` | `guardar_metodo` | Agrega un método de pago al cliente |
| `editarMetodo()` | `editar_metodo` | Actualiza tipo, número y titular |
| `eliminarMetodo()` | `eliminar_metodo` | Elimina un método (verifica pertenencia) |
| `guardarDireccion()` | `guardar_direccion` | Guarda dirección + ciudad + departamento + teléfono |
| `editarDireccion()` | `editar_direccion` | Actualiza la dirección existente |
| `eliminarDireccion()` | `eliminar_direccion` | Borra la dirección del cliente |
| `confirmarPago()` | `confirmar_pago` | Ejecuta la transacción completa de compra |

## Flujo — confirmarPago() (transacción)
```
procesar_pago.php → formulario POST con metodo_guardado
    → index.php?action=confirmar_pago
        → PagoController::confirmarPago()
            1. Validar sesión y cliente
            2. Validar que tenga dirección
            3. Validar que haya seleccionado método de pago
            4. Obtener carrito y verificar stock de cada producto
            5. Aplicar precio de oferta si existe
            6. db->beginTransaction()
                a. INSERT INTO pedido
                b. INSERT INTO detalle_pedido (por cada producto)
                c. UPDATE productos SET stock = stock - cantidad
                d. UPDATE productos SET disponibilidad=0 si stock=0
                e. INSERT INTO pago (con número de factura FAC-XXXX)
            7. db->commit()
            8. Inventario::salida() por cada producto
            9. Venta::registrar()
            10. Cart::vaciar()
            11. $_SESSION['pago_exitoso'] = [id_pedido, factura, total]
            12. redirect a index.php?action=pago_exitoso
        → Si falla: db->rollBack() + alerta de error
```

## Seguridad
- Verifica que el método de pago pertenezca al cliente antes de editar/eliminar
- Usa transacción PDO para garantizar consistencia (si falla algo, todo se revierte)
- Genera número de factura único con `uniqid()`

## Conectado con
- `index.php` (router)
- `models/Cliente.php`
- `models/MetodoPago.php`
- `models/Cart.php`
- `models/Product.php`
- `models/Inventario.php`
- `models/Venta.php`
- `views/cliente/procesar_pago.php`
- `views/cliente/pago_exitoso.php`
