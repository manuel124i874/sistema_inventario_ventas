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