-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS usuarios2;

-- Seleccionar la base de datos
USE usuarios;

-- Crear la tabla de Usuarios
CREATE TABLE IF NOT EXISTS usuarios(     
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,             
    correo_electronico VARCHAR(255) NOT NULL,                              
    contraseña VARCHAR(255) NOT NULL
);
CREATE TABLE IF NOT EXISTS grupos(
    id_grupo INT AUTO_INCREMENT PRIMARY KEY,
    nombre_grupo VARCHAR(255) NOT NULL,
    g_descripcion VARCHAR(255),
    admin INT,
    FOREIGN KEY (admin) REFERENCES usuarios(id_usuario)
);

-- Crear la tabla de Tareas
CREATE TABLE IF NOT EXISTS tareas(  
    id_tarea INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,             
    t_descripcion VARCHAR(255),                              
    fecha_limite DATE,
    estado VARCHAR(255),
    id_usuario INT, -- Definimos id_usuario como INT, no como AUTO_INCREMENT
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario),-- Referencia a la tabla Usuarios
    id_grupo INT,
    FOREIGN KEY (id_grupo) REFERENCES grupos (id_grupo)
);

CREATE TABLE IF NOT EXISTS usuarios_grupos(
    id_grupo INT NOT NULL,
    id_usuario INT NOT NULL,
    PRIMARY KEY (id_grupo, id_usuario),
    CONSTRAINT fk_grupo FOREIGN KEY (id_grupo) REFERENCES grupos(id_grupo),
    CONSTRAINT fk_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);
CREATE TABLE IF NOT EXISTS tareas_grupos(
    id_grupo INT NOT NULL,
    id_tarea INT NOT NULL,
    PRIMARY KEY (id_grupo, id_tarea),
    CONSTRAINT fk_grupo2 FOREIGN KEY (id_grupo) REFERENCES grupos(id_grupo),
    CONSTRAINT fk_tarea FOREIGN KEY (id_tarea) REFERENCES tareas(id_tarea)
);
