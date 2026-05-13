# public/assets/css/store.css

**Qué es:** Estilos visuales de la tienda cliente. Tarjetas de producto, hero, grid, botones, favoritos.

---

## Conectado con
- `public/index.php` — se carga en el `<head>`
- `views/cliente/home.php` — usa `.pcard`, `.products-grid`, `.hero-banner`
- `views/cliente/*.php` — todas las vistas cliente usan estas clases

---

## Clases principales

| Clase | Dónde se usa |
|---|---|
| `.hero-banner` | Sección principal del home |
| `.products-grid` | Grid de tarjetas de producto |
| `.pcard` | Tarjeta de cada producto |
| `.pcard__btn--buy` | Botón "Comprar ahora" |
| `.pcard__btn--cart` | Botón "Agregar al carrito" |
| `.btn-favorito` | Botón corazón sobre la imagen |
| `.content-shell` | Panel principal con efecto glassmorphism |

---

## Si se daña este archivo
El sitio sigue funcionando pero se ve sin estilos propios. Bootstrap y Tailwind siguen activos, así que no queda completamente roto, solo feo.
