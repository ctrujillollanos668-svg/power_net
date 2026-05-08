# Database.php — Conexión a la Base de Datos

## ¿Qué es?
Clase singleton que gestiona la conexión PDO a MySQL. Es el único punto de conexión a la base de datos en todo el proyecto.

## Ubicación
`config/Database.php`

## ¿Qué hace?
- Crea una conexión PDO con los parámetros del servidor local
- Configura el modo de errores como excepciones (`PDO::ERRMODE_EXCEPTION`)
- Retorna la conexión mediante `getConnection()`

## Parámetros de conexión
| Parámetro | Valor |
|---|---|
| Host | `localhost` |
| Base de datos | `powernet` |
| Usuario | `root` |
| Contraseña | *(vacía)* |
| Charset | `utf8` |

## Métodos
| Método | Descripción |
|---|---|
| `getConnection()` | Retorna el objeto PDO activo |

## ¿Quién lo usa?
Todos los modelos del proyecto lo instancian en su constructor:
- `models/User.php`
- `models/Product.php`
- `models/Category.php`
- `models/Cliente.php`
- `models/Pedido.php`
- `models/Order.php`
- `models/Pago.php`
- `models/MetodoPago.php`
- `models/Favorito.php`
- `models/Devolucion.php`
- `models/Envio.php`
- `models/Inventario.php`
- `models/Oferta.php`
- `models/Proveedor.php`
- `models/Venta.php`

## Flujo
```
Modelo::__construct()
    → new Database()
    → getConnection()
        → new PDO(mysql:host=localhost;dbname=powernet)
            → $this->conn
```
