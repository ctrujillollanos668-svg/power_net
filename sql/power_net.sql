USE powernet;

-- -----------------------------
-- TABLA ROLES
-- -----------------------------
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255),
    estado ENUM('activo','inactivo') DEFAULT 'activo',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- -----------------------------
-- TABLA PERSONA
-- (AGREGADA DESDE EL DIAGRAMA)
-- -----------------------------
CREATE TABLE persona (
    id_persona INT AUTO_INCREMENT PRIMARY KEY,
    nombre_persona VARCHAR(100),
    telefono VARCHAR(20),
    documento VARCHAR(50)
);

-- -----------------------------
-- TABLA USUARIOS
-- -----------------------------
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    id_rol INT DEFAULT 2,
    id_persona INT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reset_token VARCHAR(255) NULL,
    reset_expira DATETIME NULL;
    FOREIGN KEY (id_rol) REFERENCES roles(id),
    FOREIGN KEY (id_persona) REFERENCES persona(id_persona)
);

-- -----------------------------
-- TABLA CLIENTE
-- -----------------------------
CREATE TABLE cliente (
    id_cliente INT AUTO_INCREMENT PRIMARY KEY,
    direccion VARCHAR(255),
    id_persona INT,
    FOREIGN KEY (id_persona) REFERENCES persona(id_persona)
);

-- -----------------------------
-- TABLA PROVEEDOR
-- -----------------------------
CREATE TABLE proveedor (
    id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    nombre_proveedor VARCHAR(100),
    correo VARCHAR(150),
    telefono VARCHAR(20)
);
-- -----------------------------
-- TABLA IMAGEN_PRODUCTOS
-- -----------------------------
CREATE TABLE imagenes_producto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    imagen VARCHAR(255) NOT NULL,

    FOREIGN KEY (producto_id) REFERENCES productos(id_producto)
    ON DELETE CASCADE
);
-- -----------------------------
-- TABLA CATEGORIA
-- -----------------------------
CREATE TABLE categoria (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(100),
    descripcion TEXT,
    estado BOOLEAN
);

-- -----------------------------
-- TABLA PRODUCTOS
-- -----------------------------
CREATE TABLE productos (
    id_producto   INT AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(100),
    descripcion   TEXT,
    stock         INT,
    disponibilidad BOOLEAN,
    precio        DECIMAL(10,2),
    imagen        VARCHAR(50),
    id_categoria  INT,
    id_proveedor  INT,
    FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria),
    FOREIGN KEY (id_proveedor) REFERENCES proveedor(id_proveedor)
);

-- -----------------------------
-- TABLA OFERTA
-- -----------------------------
CREATE TABLE oferta (
    id_oferta     INT AUTO_INCREMENT PRIMARY KEY,
    id_producto   INT NOT NULL,
    precio_oferta DECIMAL(10,2) NOT NULL,
    descuento     INT DEFAULT 0,
    fecha_inicio  DATE NOT NULL,
    fecha_fin     DATE NOT NULL,
    estado        TINYINT(1) DEFAULT 1,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto) ON DELETE CASCADE
);

-- -----------------------------
-- TABLA PEDIDO
-- -----------------------------
CREATE TABLE pedido (
    id_pedido INT AUTO_INCREMENT PRIMARY KEY,
    fecha_pedido DATETIME,
    total_pedido DECIMAL(10,2),
    estado_pedido VARCHAR(50),
    id_cliente INT,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente)
);

-- -----------------------------
-- TABLA DETALLE PEDIDO
-- -----------------------------
CREATE TABLE detalle_pedido (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_pedido INT,
    id_producto INT,
    precio_unitario DECIMAL(10,2),
    cantidad INT,
    subtotal DECIMAL(10,2),
    FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido),
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

-- -----------------------------
-- TABLA METODO_PAGO
-- -----------------------------
CREATE TABLE IF NOT EXISTS metodo_pago (
    id_metodo INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    numero VARCHAR(100) NOT NULL,
    titular VARCHAR(100) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente)
);

-- -----------------------------
-- TABLA PAGO
-- -----------------------------
CREATE TABLE pago (
    id_pago INT AUTO_INCREMENT PRIMARY KEY,
    monto DECIMAL(10,2),
    metodo_pago VARCHAR(50),
    fecha_pago DATETIME,
    factura VARCHAR(100),
    estado_pago VARCHAR(50),
    id_pedido INT,
    FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido)
);

-- -----------------------------
-- TABLA ENVIO
-- -----------------------------
CREATE TABLE envio (
    id_envio INT AUTO_INCREMENT PRIMARY KEY,
    empresa_envios VARCHAR(100),
    estado VARCHAR(50),
    costo DECIMAL(10,2),
    fecha_hora DATETIME,
    direccion_envio VARCHAR(255),
    id_pedido INT,
    FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido)
);

-- -----------------------------
-- TABLA DEVOLUCION
-- -----------------------------
CREATE TABLE devolucion (
    id_devolucion INT AUTO_INCREMENT PRIMARY KEY,
    fecha_devolucion DATETIME,
    monto_devolucion DECIMAL(10,2),
    id_pedido INT,
    FOREIGN KEY (id_pedido) REFERENCES pedido(id_pedido)
);

-- -----------------------------
-- TABLA DETALLE DEVOLUCION
-- -----------------------------
CREATE TABLE detalle_devolucion (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_devolucion INT,
    id_producto INT,
    cantidad INT,
    motivo TEXT,
    FOREIGN KEY (id_devolucion) REFERENCES devolucion(id_devolucion),
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

-- -----------------------------
-- ÍNDICES IMPORTANTES
-- -----------------------------
CREATE UNIQUE INDEX idx_usuario_email ON usuarios(email);
CREATE UNIQUE INDEX idx_detalle_pedido ON detalle_pedido(id_pedido, id_producto);