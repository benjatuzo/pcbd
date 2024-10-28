CREATE DATABASE ProDuctos;
USE jybcomputerparts;

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    categoria VARCHAR(100) NOT NULL,
    descripcion TEXT,
    imagen VARCHAR(255)
);
