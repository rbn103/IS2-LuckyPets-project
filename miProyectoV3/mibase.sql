-- Elimina la BD en caso exista
DROP DATABASE IF EXISTS miBase1;

-- Crear la base de datos
CREATE DATABASE miBase1;
USE miBase1;

-- Crear tabla Roles
CREATE TABLE Roles (
  rol_ID INT PRIMARY KEY,
  nombre_rol VARCHAR(100) NOT NULL,
  descripcion VARCHAR(255) NULL
);

-- Crear tabla Permisos
CREATE TABLE Permisos (
  permiso_id INT PRIMARY KEY,
  nombre_permiso VARCHAR(100) NOT NULL,
  descripcion VARCHAR(255) NULL
);

-- Crear tabla roles_permisos (relación muchos a muchos)
CREATE TABLE roles_permisos (
  rol_ID INT NOT NULL,
  permiso_id INT NOT NULL,
  PRIMARY KEY (rol_ID, permiso_id),
  CONSTRAINT FK_roles_permisos_Roles FOREIGN KEY (rol_ID) REFERENCES Roles(rol_ID),
  CONSTRAINT FK_roles_permisos_Permisos FOREIGN KEY (permiso_id) REFERENCES Permisos(permiso_id)
);

-- Crear tabla Usuarios
CREATE TABLE Usuarios (
  id_trabajador INT PRIMARY KEY,
  nombre_usuario VARCHAR(100) NOT NULL,
  RUC VARCHAR(20) NULL,
  rol_ID INT NOT NULL,
  EMAIL VARCHAR(100) NOT NULL,
  CONTRASEÑA VARCHAR(255) NOT NULL,
  estado VARCHAR(20) NULL,
  ultimo_log DATETIME NULL,
  CONSTRAINT FK_Usuarios_Roles FOREIGN KEY (rol_ID) REFERENCES Roles(rol_ID)
);

-- Insertar datos de Roles
INSERT INTO Roles (rol_ID, nombre_rol, descripcion) VALUES
(1, 'Gerente general', 'Control total del sistema, puede gestionar usuarios y configuraciones.'),
(2, 'Jefe de inventario', 'Puede crear, editar y publicar contenido. No puede gestionar usuarios.'),
(3, 'Administrador', 'Puede crear, editar y publicar contenido. No puede gestionar usuarios.'),
(4, 'Comprador', 'Puede ver contenido y gestionar su propio perfil.'),
(5, 'Supervisor almacén', 'Puede crear, editar y publicar contenido. No puede gestionar usuarios.'),
(6, 'coordinador', 'Puede crear, editar y publicar contenido. No puede gestionar usuarios.'),
(7, 'personal administrativo', 'Puede ver contenido y gestionar su propio perfil.');

-- Insertar datos de Permisos
INSERT INTO Permisos (permiso_id, nombre_permiso, descripcion) VALUES
(100, 'ver_dashboard', 'Permite ver el dashboard'),
(101, 'crear_usuarios', 'Permite crear nuevos usuarios'),
(102, 'editar_contenido', 'Permite editar contenido'),
(103, 'eliminar_contenido', 'Permite eliminar contenido'),
(104, 'gestionar_roles', 'Permite gestionar roles'),
(105, 'ver_reportes', 'Permite ver reportes');

-- Insertar usuarios ejemplo (solo algunos, según tus datos)
INSERT INTO Usuarios (id_trabajador, nombre_usuario, RUC, rol_ID, EMAIL, CONTRASEÑA, estado, ultimo_log) VALUES
(1, 'gerente_general', '20123456780', 1, 'gerente@sistema.com', 'hash_de_contraseña_1', 'activo', '2023-10-24 11:10:18'),
(2, 'jefe_inventario', '20123456781', 2, 'jefe.inventario@sistema.com', 'hash_de_contraseña_2', 'activo', '2023-10-26 09:00:23'),
(3, 'admin', '20123456782', 3, 'admin@sistema.com', 'hash_de_contraseña_3', 'activo', '2023-09-20 12:00:00'),
(4, 'comprador1', '20123456783', 4, 'comprador1@sistema.com', 'hash_de_contraseña_4', 'activo', '2023-10-25 19:45:31'),
(5, 'superv_almacen', '20123456784', 5, 'superv.almacen@sistema.com', 'hash_de_contraseña_5', 'activo', '2023-10-26 08:55:05'),
(6, 'coordinador', '20123456785', 6, 'coordinador@sistema.com', 'hash_de_contraseña_6', 'activo', '2023-10-26 06:50:11'),
(7, 'pers_admin', '20123456786', 7, 'personal.admin@sistema.com', 'hash_de_contraseña_7', 'activo', '2023-10-24 17:20:45'),
(8, 'romina.nnz', '20123456788', 2, 'rominanlp@gmail.com', 'hash_de_contraseña_9', 'activo', '2023-10-25 21:15:22'),
(9, 'esteban.v', '20123456789', 4, 'esteban22lp@gmail.com', 'hash_de_contraseña_10', 'activo', '2023-10-26 09:30:00'),
(10, 'melissa.acs', '20123456790', 7, 'melissarlp@gmail.com', 'hash_de_contraseña_11', 'activo', '2023-11-18 21:15:56'),
(11, 'pablo.ft', '20123456791', 6, 'pablo014lp@gmail.com', 'hash_de_contraseña_12', 'activo', '2023-11-19 10:27:34'),
(12, 'angelica.tb', '20123456792', 6, 'angelica1lp@gmail.com', 'hash_de_contraseña_13', 'activo', '2023-11-20 14:30:22'),
(13, 'kelly.in', '20123456793', 3, 'kelly1nlp@gmail.com', 'hash_de_contraseña_14', 'activo', '2023-11-21 16:35:12'),
(14, 'juan.hr', '20123456794', 5, 'juanlp@gmail.com', 'hash_de_contraseña_15', 'activo', '2023-11-17 08:15:46');

-- Ejemplo para roles_permisos (relación roles y permisos)
INSERT INTO roles_permisos (rol_ID, permiso_id) VALUES
(1, 100), (1, 101), (1, 102), (1, 103), (1, 104), (1, 105), -- Gerente general todo acceso
(2, 102), (2, 105), -- Jefe Inventario
(3, 102), (3, 105), -- Administrador
(4, 105); -- Comprador solo ver reportes

-- Crear tabla HistorialLogin
CREATE TABLE HistorialLogin (
  id_intento INT AUTO_INCREMENT PRIMARY KEY,
  usuario INT NOT NULL,
  fecha_hora DATETIME NOT NULL,
  IP_Origen VARCHAR(50) NULL,
  Resultado VARCHAR(50) NULL,
  CONSTRAINT FK_HistorialLogin_Usuarios FOREIGN KEY (usuario) REFERENCES Usuarios(id_trabajador)
);

-- Crear tabla ReseteoContraseña
CREATE TABLE ReseteoContraseña (
  id_token INT AUTO_INCREMENT PRIMARY KEY,
  usuario INT NOT NULL,
  token VARCHAR(255) NOT NULL,
  fecha_emision DATETIME NOT NULL,
  fecha_expiracion DATETIME NOT NULL,
  estado VARCHAR(20) NULL,
  IP_Origen VARCHAR(50) NULL,
  CONSTRAINT FK_ReseteoContraseña_Usuarios FOREIGN KEY (usuario) REFERENCES Usuarios(id_trabajador)
);
-- Bloqueo
CREATE TABLE BloqueoUsuarios (
  id_bloqueo INT AUTO_INCREMENT PRIMARY KEY,
  usuario INT NOT NULL,
  fecha_bloqueo DATETIME NOT NULL,
  fecha_desbloqueo DATETIME NOT NULL,
  intentos_fallidos INT DEFAULT 0,
  IP_Origen VARCHAR(50) NULL,
  CONSTRAINT FK_BloqueoUsuarios_Usuarios FOREIGN KEY (usuario) REFERENCES Usuarios(id_trabajador)
);
