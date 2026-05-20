# models/Envio.php

**Qué hace:** Crea y actualiza registros de envío de pedidos.

---

## Conectado con
- `config/Database.php`
- `controllers/EnvioController.php`
- Tabla: `envio`

---

## Si se daña este archivo
- El admin no puede registrar envíos
- Los pedidos no cambian a estado `enviado` ni `entregado`
- El cliente nunca puede solicitar devoluciones
