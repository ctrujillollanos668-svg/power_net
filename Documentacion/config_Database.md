# config/Database.php

**Qué es:** Conexión a la base de datos MySQL. Todos los modelos la usan.

---

## Conectado con
- Todos los modelos de `models/` — cada uno hace `new Database()` en su constructor

---

## Datos de conexión
- Host: `localhost`
- Base de datos: `powernet`
- Usuario: `root` / Contraseña: *(vacía)*

---

## Si se daña este archivo
**Todo el sitio deja de funcionar.** Ningún modelo puede conectarse a la BD.

## Si cambian las credenciales de MySQL
Editar las propiedades `$host`, `$db_name`, `$username`, `$password` en este archivo.
