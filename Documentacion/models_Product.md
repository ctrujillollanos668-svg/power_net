# models/Product.php

**Qué hace:** Todo lo relacionado con productos: catálogo, filtros, imágenes, stock.

---

## Conectado con
- `config/Database.php`
- `controllers/ProductController.php` — CRUD admin
- `controllers/ClienteController.php` — catálogo y detalle
- `controllers/CartController.php` — datos del producto en el carrito
- `controllers/PagoController.php` — verifica stock y precio al pagar
- Tablas: `productos`, `imagenes_producto`, `categoria`, `oferta`, `detalle_pedido`

---

## Métodos clave

| Método | Qué hace |
|---|---|
| `filtrar()` | Filtra el catálogo del home (categoría, búsqueda, precio, ofertas) |
| `obtenerActivos()` | Solo productos con stock > 0 y disponibles |
| `obtenerPorId()` | Un producto por ID |
| `obtenerImagenes()` | Imágenes de un producto |
| `obtenerRelacionados()` | Hasta 4 productos de la misma categoría |
| `descontarStock()` | Resta stock; si llega a 0 oculta el producto |
| `eliminar()` | Bloquea si tiene pedidos; si no, elimina imágenes físicas y BD |

---

## Si se daña este archivo
- El catálogo del cliente no carga
- El carrito no puede mostrar los productos
- El pago no puede verificar el stock → no se puede comprar
