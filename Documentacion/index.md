# index.php — Router Principal

## ¿Qué es?
Es el **punto de entrada único** de toda la aplicación cliente. Todas las URLs del cliente pasan por aquí mediante el parámetro `?action=`.

## Ubicación
`public/index.php`

## ¿Qué hace?
1. Inicia la sesión PHP
2. Carga todos los controllers y modelos base
3. Instancia todos los controllers al inicio
4. Ejecuta un `switch($action)` que enruta cada petición al controller correspondiente
5. Prepara variables para las vistas
6. Renderiza el HTML con header, vista activa y footer

## Conexiones — Controllers que instancia
| Controller | Variable |
|---|---|
| UsuarioController | `$userController` |
| ProductController | `$productController` |
| CategoryController | `$categoryController` |
| CartController | `$cartController` |
| ClienteController | `$clienteCtrl` |
| PagoController | `$pagoCtrl` |
| DevolucionController | `$devCtrl` |
| EnvioController | `$envCtrl` |
| OfertaController | `$ofCtrl` |
| ProveedorController | `$provCtrl` |
| VentaController | `$ventaCtrl` |

## Rutas principales (`?action=`)
| Action | Controller / Vista |
|---|---|
| `login` | UsuarioController::login() |
| `register` | UsuarioController::register() |
| `logout` | UsuarioController::logout() |
| `guardar_producto` | ProductController::guardar() |
| `guardar_categoria` | CategoryController::guardar() |
| `agregar_carrito` | CartController::agregar() |
| `procesar_pago` | ClienteController::procesarPago() |
| `confirmar_pago` | PagoController::confirmarPago() |
| `mis_pedidos` | ClienteController::misPedidos() |
| `mis_favoritos` | ClienteController::misFavoritos() |
| `mis_devoluciones` | ClienteController::misDevoluciones() |
| `toggle_favorito` | ClienteController::toggleFavorito() |
| `medios_pago` | ClienteController::mediosPago() |
| `ofertas` | Vista ofertas con modelo Oferta |
| `detalle_producto` | Vista detalle con modelo Product |
| `factura` | Vista factura con modelos Pedido + User |
| `solicitar_devolucion` | DevolucionController::solicitar() |
| `procesar_devolucion` | DevolucionController::procesar() |
| `guardar_envio` | EnvioController::guardar() |
| `guardar_oferta` | OfertaController::guardar() |
| `eliminar_venta` | VentaController::eliminar() |
| `guardar_proveedor` | ProveedorController::guardar() |

## Vistas que incluye
- `views/cliente/partials/header.php`
- `views/cliente/partials/footer.php`
- `views/cliente/home.php` (default)
- `views/cliente/carrito.php`
- `views/cliente/procesar_pago.php`
- `views/cliente/mis_pedidos.php`
- `views/cliente/mis_favoritos.php`
- `views/cliente/mis_devoluciones.php`
- `views/cliente/ofertas.php`
- `views/cliente/detalle_producto.php`
- `views/cliente/pago_exitoso.php`
- `views/cliente/perfil.php`
- `views/cliente/medios_pago.php`
- `views/auth/login.php` (modal)

## Flujo general
```
Navegador → index.php?action=X
    → switch($action)
        → Controller::metodo()
            → Modelo::consulta()
                → Base de datos
            → $GLOBALS['variable'] = datos
    → include vista.php
        → HTML al navegador
```
