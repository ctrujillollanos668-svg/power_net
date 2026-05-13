# models/Cliente.php

**Qué hace:** Obtiene y actualiza el perfil del cliente (dirección, teléfono).

---

## Conectado con
- `config/Database.php`
- `controllers/ClienteController.php` y `PagoController.php` — lo usan constantemente
- Tablas: `cliente`, `persona`, `usuarios`

---

## Métodos clave

| Método | Qué hace |
|---|---|
| `obtenerPorUsuario($id_usuario)` | Busca el cliente del usuario logueado |
| `crearSiNoExiste($id_usuario)` | Crea el registro de cliente si no existe |
| `actualizarDireccion()` | Guarda dirección en `cliente` y teléfono en `persona` |

---

## Si se daña este archivo
- El checkout no puede obtener la dirección del cliente
- No se puede guardar ni editar la dirección de envío
- El pago puede fallar si no encuentra el `id_cliente`
