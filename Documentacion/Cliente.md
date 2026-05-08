# Cliente.php — Modelo del Cliente

## Ubicación
`models/Cliente.php`

## Tabla en BD
`cliente`

## Columnas principales
| Columna | Tipo | Descripción |
|---|---|---|
| `id_cliente` | INT PK | Identificador único del cliente |
| `id_persona` | INT FK | Vincula con la tabla `persona` |
| `direccion` | VARCHAR | Dirección de envío |

## Relación con otras tablas
```
usuarios → id_persona → persona
                           ↓
                        cliente (id_persona)
```
El modelo busca el cliente a través del `id_persona` del usuario en sesión.

## Métodos
| Método | Descripción |
|---|---|
| `obtenerPorUsuario($id_usuario)` | Busca el cliente vinculado al usuario (JOIN usuarios → persona → cliente) |
| `crearSiNoExiste($id_usuario)` | Crea el registro en `cliente` si no existe para ese usuario |
| `actualizarDireccion($id_cliente, $direccion, $telefono)` | Actualiza dirección en `cliente` y teléfono en `persona` |

## Flujo — obtenerPorUsuario()
```sql
SELECT * FROM cliente
WHERE id_persona = (
    SELECT id_persona FROM usuarios WHERE id = ?
)
```

## ¿Quién lo usa?
| Archivo | Para qué |
|---|---|
| `ClienteController.php` | Obtener datos del cliente para todas las vistas |
| `PagoController.php` | Verificar cliente antes del pago |
| `DevolucionController.php` | Obtener id_cliente para devoluciones |
| `OrderController.php` | Obtener id_cliente para pedidos |

## Conectado con
- `config/Database.php`
- `tabla: cliente`
- `tabla: usuarios`
- `tabla: persona`
