# Documentacion del Sistema Power Net

Esta documentacion resume los archivos principales del proyecto para que puedas ubicar rapido que hace cada parte.

## 1) Punto de entrada y rutas

- `public/index.php`: Front controller principal. Inicia sesion, instancia controladores, ejecuta rutas y renderiza vistas cliente.
- `routes/web.php`: Router web central (`dispatchWebRoutes`). Mapea `action` a metodos de controladores.

## 2) Configuracion

- `config/Database.php`: Conexion PDO y acceso a base de datos.

## 3) Controladores (capa de casos de uso)

- `controllers/UsuarioController.php`: Registro, login, logout, perfil y rol.
- `controllers/ProductController.php`: CRUD de productos e imagenes.
- `controllers/CategoryController.php`: CRUD de categorias.
- `controllers/CartController.php`: Operaciones del carrito (agregar, aumentar, disminuir, vaciar).
- `controllers/ClienteController.php`: Vistas y flujos cliente (perfil, pedidos, favoritos, pagos, factura, ofertas, detalle).
- `controllers/PagoController.php`: Metodos de pago, direccion y confirmacion de pago.
- `controllers/DevolucionController.php`: Solicitudes y gestion de devoluciones.
- `controllers/EnvioController.php`: Gestion de envios y cambios de estado.
- `controllers/OfertaController.php`: Crear, activar, editar y desactivar ofertas.
- `controllers/ProveedorController.php`: CRUD de proveedores.
- `controllers/VentaController.php`: Gestion de ventas y estado.
- `controllers/OrderController.php`: Guardado de pedidos desde flujo admin.

## 4) Modelos (acceso a datos)

- `models/User.php`: Usuarios (consulta por ID y datos de cuenta).
- `models/Category.php`: Categorias.
- `models/Pago.php`: Entidad/persistencia de pagos.
- `models/Cart.php`: Estado del carrito en sesion.
- `models/Product.php`: Productos, imagenes y relacionados.
- `models/Venta.php`: Ventas.
- `models/Cliente.php`: Perfil cliente y direccion.
- `models/Pedido.php`: Pedidos y detalle de pedido.
- `models/Oferta.php`: Ofertas activas e historial.
- `models/MetodoPago.php`: Metodos de pago del cliente.
- `models/Favorito.php`: Favoritos del cliente.
- `models/Inventario.php`: Inventario y stock.
- `models/Proveedor.php`: Proveedores.
- `models/Devolucion.php`: Devoluciones.
- `models/Envio.php`: Envios.
- `models/Order.php`: Operaciones de orden/pedido para backend admin.

## 5) Vistas cliente

- `views/cliente/home.php`: Home, hero, filtros y grid de productos.
- `views/cliente/detalle_producto.php`: Detalle del producto + relacionados.
- `views/cliente/carrito.php`: Carrito de compras.
- `views/cliente/procesar_pago.php`: Checkout/confirmacion de compra.
- `views/cliente/pago_exitoso.php`: Confirmacion de pago exitoso.
- `views/cliente/ofertas.php`: Landing de ofertas activas.
- `views/cliente/factura.php`: Documento de factura (HTML completo para imprimir/PDF).
- `views/cliente/perfil.php`: Perfil del cliente.
- `views/cliente/datos_cuenta.php`: Datos de cuenta.
- `views/cliente/seguridad.php`: Seguridad de cuenta.
- `views/cliente/medios_pago.php`: Gestion de medios de pago.
- `views/cliente/mis_pedidos.php`: Historial de pedidos del cliente.
- `views/cliente/mis_devoluciones.php`: Historial y estado de devoluciones.
- `views/cliente/mis_favoritos.php`: Lista de productos favoritos.
- `views/cliente/solicitar_devolucion.php`: Formulario para nueva devolucion.
- `views/cliente/partials/header.php`: Header, menus, buscador, alertas y asistente virtual.
- `views/cliente/partials/footer.php`: Footer del sitio.

## 6) Vistas auth

- `views/auth/login.php`: Formulario modal de login/registro.
- `views/auth/recuperar.php`: Solicitud de recuperacion de contraseña.
- `views/auth/reset_password.php`: Cambio de contraseña por token.

## 7) Vistas admin

- `views/admin/dashboard.php`: Panel principal admin.
- `views/admin/productos/productos.php`: Gestion de productos.
- `views/admin/categoria/categorias.php`: Gestion de categorias.
- `views/admin/pedidos/pedidos.php`: Gestion de pedidos.
- `views/admin/ofertas/ofertas.php`: Gestion de ofertas.
- `views/admin/proveedores/proveedores.php`: Gestion de proveedores.
- `views/admin/envios/envios.php`: Gestion de envios.
- `views/admin/inventario/inventario.php`: Gestion de inventario.
- `views/admin/pago/pago.php`: Gestion de pagos.
- `views/admin/pago/ventas.php`: Vista de ventas.
- `views/admin/pago/devolucion.php`: Vista admin de devoluciones.
- `views/admin/partials/header.php`: Header del modulo admin.
- `views/admin/partials/sidebar.php`: Sidebar del modulo admin.
- `views/admin/partials/auth_check.php`: Proteccion de acceso a rutas admin.

## 8) Assets front-end

- `public/assets/css/store.css`: Estilos globales de la tienda cliente.
- `public/assets/js/store.js`: JS de UI cliente (modales login/recuperar, favoritos y cantidades).

## 9) Base de datos y scripts

- `sql/power_net.sql`: Script principal de estructura/datos de la base.
- `Scripts/migrar_bd.php`: Script auxiliar para migracion de base de datos.

## 10) Flujo general (resumen rapido)

1. El navegador entra por `public/index.php`.
2. `index.php` llama `dispatchWebRoutes()` en `routes/web.php`.
3. El router ejecuta controladores segun `action`.
4. Los controladores llenan datos (muchas veces en `$GLOBALS`) y seleccionan vista.
5. `index.php` renderiza la vista cliente o corta flujo para respuestas especiales (ej: factura).
