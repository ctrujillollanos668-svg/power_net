# models/Pago.php

**Qué hace:** Inserta registros en la tabla `pago`.

---

## Conectado con
- `config/Database.php`
- En el flujo real de compra, el pago se inserta directamente en `PagoController::confirmarPago()` con PDO, no con este modelo

---

## Si se daña este archivo
Impacto mínimo. El pago real usa PDO directo en `PagoController`, no este modelo.
