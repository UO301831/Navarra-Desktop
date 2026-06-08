-- navarra.sql - Creación de la base de datos de la central de reservas

CREATE DATABASE IF NOT EXISTS UO301831_DB CHARACTER SET utf8mb4;
USE UO301831_DB;

-- Borra las tablas si ya existen
DROP TABLE IF EXISTS reserva;
DROP TABLE IF EXISTS recurso;
DROP TABLE IF EXISTS usuario;
DROP TABLE IF EXISTS localidad;
DROP TABLE IF EXISTS tipo_recurso;

-- Tipos de recurso turístico 
CREATE TABLE tipo_recurso (
    id_tipo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL
);

-- Localidades de Navarra donde se ubican los recursos
CREATE TABLE localidad (
    id_localidad INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

-- Usuarios registrados que pueden hacer reservas
CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    telefono VARCHAR(20)
);

-- Recursos turísticos que se pueden reservar
CREATE TABLE recurso (
    id_recurso INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    id_tipo INT NOT NULL,
    id_localidad INT NOT NULL,
    plazas INT NOT NULL,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME NOT NULL,
    precio DECIMAL(8,2) NOT NULL,
    descripcion TEXT NOT NULL,
    FOREIGN KEY (id_tipo) REFERENCES tipo_recurso(id_tipo),
    FOREIGN KEY (id_localidad) REFERENCES localidad(id_localidad)
);

-- Reservas realizadas por los usuarios sobre los recursos.
-- Cada reserva es para un rango de fechas (fecha_inicio a fecha_fin) y un numero
-- de personas; el presupuesto se calcula como precio * dias * personas.
CREATE TABLE reserva (
    id_reserva INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_recurso INT NOT NULL,
    fecha_reserva DATETIME NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    num_personas INT NOT NULL,
    presupuesto DECIMAL(10,2) NOT NULL,
    estado VARCHAR(20) NOT NULL DEFAULT 'confirmada',
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario),
    FOREIGN KEY (id_recurso) REFERENCES recurso(id_recurso)
);
