# models/MetodoPago.php

**Qué hace:** Guarda y gestiona las tarjetas/cuentas de pago del cliente.

---

## Conectado con
- `config/Database.php`
- `controllers/PagoController.php` — guarda, edita y elimina métodos
- `controllers/ClienteController.php` — carga los métodos en el checkout
- Tabla: `metodo_pago`

---

## Si se daña este archivo
- El cliente no puede guardar ni ver sus métodos de pago
- El checkout no puede listar los métodos disponibles → no se puede pagar
