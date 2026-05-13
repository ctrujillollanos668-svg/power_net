# models/Favorito.php

**Qué hace:** Agrega/quita productos de favoritos y consulta los favoritos del usuario.

---

## Conectado con
- `config/Database.php`
- `controllers/ClienteController.php` — toggle (AJAX) y vista `mis_favoritos`
- `public/assets/js/store.js` — llama al endpoint `?action=toggle_favorito` y actualiza el corazón
- Tabla: `favorito`

---

## Flujo del corazón
```
Click en ♡ (store.js) → POST ?action=toggle_favorito → ClienteController → Favorito::toggle() → responde JSON → store.js actualiza el ícono
```

---

## Si se daña este archivo
- Los favoritos no se pueden agregar ni quitar
- La vista "Mis favoritos" queda vacía
- El corazón en las tarjetas no responde (aunque el JS esté bien)
