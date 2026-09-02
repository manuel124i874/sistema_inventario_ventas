USE sistema_inventario;

-- Tabla para el módulo de Login y seguridad
CREATE TABLE usuarios (
id INT AUTO_INCREMENT PRIMARY KEY,
nombre_completo VARCHAR(100) NOT NULL,
usuario VARCHAR(50) NOT NULL UNIQUE,
password VARCHAR(255) NOT NULL,
rol VARCHAR(20) NOT NULL
);

-- Tabla para el módulo de Inventario de productos

CREATE TABLE productos (
id INT AUTO_INCREMENT PRIMARY KEY,
nombre_producto VARCHAR(100) NOT NULL,
categoria VARCHAR(50) NOT NULL,
stock INT NOT NULL,
precio DECIMAL(10, 2) NOT NULL
);



    -- Insertar usuarios por defecto del sistema
    INSERT INTO usuarios (nombre_completo, usuario, password, rol) VALUES
    ('Administrador Principal', 'admin', 'admin123', 'Administrador'),
    ('Cajero de Turno', 'cajero1', 'ventas2024', 'Cajero');

    -- Insertar lote de inventario inicial
    INSERT INTO productos (nombre_producto, categoria, stock, precio) VALUES
    ('Laptop Dell Inspiron 15', 'Computadoras', 10, 650.00),
    ('Mouse Inalámbrico Logitech', 'Accesorios', 25, 15.50),
    ('Impresora Epson EcoTank', 'Oficina', 5, 210.00),
    ('Resma de Papel Tamaño Carta', 'Papelería', 100, 4.25);


    -- CONSULTAS DE PRÁCTICA DEL MÓDULO (Guía 9)
    -- Actualización simultánea de precio y stock:
    -- UPDATE productos SET precio = 720.00, stock = 15 WHERE id = 1;
    -- Eliminación de producto descontinuado:
    -- DELETE FROM productos WHERE id = 4;


    -- Usar la base de datos del proyecto
USE sistema_inventario;

-- 1. Tabla para el módulo de Login y Seguridad
CREATE TABLE usuarios (
id INT AUTO_INCREMENT PRIMARY KEY,
nombre_completo VARCHAR(100) NOT NULL,
usuario VARCHAR(50) NOT NULL UNIQUE,
password VARCHAR(255) NOT NULL,
rol VARCHAR(20) NOT NULL
);

-- 2. NUEVA TABLA RAÍZ: Categorías del sistema
CREATE TABLE categorias (
id INT AUTO_INCREMENT PRIMARY KEY,
nombre_categoria VARCHAR(50) NOT NULL UNIQUE

-- 3. TABLA DEPENDIENTE MODIFICADA: Productos con Llave Foránea
CREATE TABLE productos (
id INT AUTO_INCREMENT PRIMARY KEY,
nombre_producto VARCHAR(100) NOT NULL,
categoria_id INT NOT NULL,
stock INT NOT NULL,
precio DECIMAL(10, 2) NOT NULL,
FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- 4. Inserción de los catálogos base (deben insertarse primero)
INSERT INTO categorias (nombre_categoria) VALUES
('Computadoras'),
('Accesorios'),
('Oficina');

-- 5. Inserción de Productos (Observa cómo ahora vinculamos usando números
enteros)
INSERT INTO productos (nombre_producto, categoria_id, stock, precio) VALUES
('Laptop Dell Inspiron 15', 1, 15, 720.00),
('Mouse Inalámbrico Logitech', 2, 25, 12.00);


SELECT 
    p.id AS 'ID Producto', 
    p.nombre_producto AS 'Producto', 
    c.nombre_categoria AS 'Categoría', 
    p.stock AS 'Existencias', 
    p.precio AS 'Precio Unitario'
FROM productos p
INNER JOIN categorias c ON p.categoria_id = c.id;


SELECT 
    p.id AS 'ID Producto', 
    p.nombre_producto AS 'Producto', 
    c.nombre_categoria AS 'Categoría', 
    p.stock AS 'Existencias', 
    p.precio AS 'Precio Unitario'
FROM productos p
INNER JOIN categorias c ON p.categoria_id = c.id 
WHERE c.nombre_categoria = 'Accesorios';
INSERT INTO proveedores (nombre_empresa, contacto, telefono, direccion) VALUES
('Tech Data El Salvador', 'Juan Pérez', '2255-8899', 'San Salvador, Col. Escalón'),
('Distribuidora de Papel', 'María Gómez', '2666-4433', 'San Miguel, Centro');

-- ====================================================================
-- MÓDULO DE COMPRAS: ARQUITECTURA MAESTRO-DETALLE (Guía 23)
-- ====================================================================

-- 1. Tabla Maestra de Compras (Cabecera de Factura)
CREATE TABLE compras (
id INT AUTO_INCREMENT PRIMARY KEY,
proveedor_id INT NOT NULL,
usuario_id INT NOT NULL,
fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
total DECIMAL(10, 2) NOT NULL,
FOREIGN KEY (proveedor_id) REFERENCES proveedores(id),
FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- 2. Tabla Detalle de Compras (Líneas de los productos ingresados)
CREATE TABLE detalle_compras (
id INT AUTO_INCREMENT PRIMARY KEY,
compra_id INT NOT NULL,
producto_id INT NOT NULL,
cantidad INT NOT NULL,
precio_compra DECIMAL(10, 2) NOT NULL,
FOREIGN KEY (compra_id) REFERENCES compras(id),
FOREIGN KEY (producto_id) REFERENCES productos(id)
);