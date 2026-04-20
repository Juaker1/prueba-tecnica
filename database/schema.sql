
CREATE DATABASE IF NOT EXISTS prueba_tecnica
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE prueba_tecnica;

-- ----------------------------------------------------------
-- TABLA: usuarios
-- Almacena tanto administradores como usuarios internos.
-- La columna `rol` determina los permisos de cada cuenta.
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id         INT          AUTO_INCREMENT PRIMARY KEY,
    nombre     VARCHAR(100) NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    rol        ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario',
    created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- TABLA: solicitudes
-- Registra cada solicitud administrativa creada por un usuario.
-- El estado avanza mediante acciones del administrador.
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS solicitudes (
    id                  INT          AUTO_INCREMENT PRIMARY KEY,
    usuario_id          INT          NOT NULL,
    nombre_solicitante  VARCHAR(100) NOT NULL,
    correo              VARCHAR(150) NOT NULL,
    tipo                ENUM(
                            'academica',
                            'certificado',
                            'actualizacion_datos',
                            'otra'
                        )            NOT NULL,
    descripcion         TEXT         NOT NULL,
    estado              ENUM(
                            'pendiente',
                            'en_revision',
                            'aprobada',
                            'rechazada'
                        )            NOT NULL DEFAULT 'pendiente',
    comentario          TEXT             NULL,
    created_at          TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
                                     ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_solicitudes_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- ÍNDICES adicionales para las consultas de filtrado
-- ----------------------------------------------------------
CREATE INDEX idx_solicitudes_estado ON solicitudes (estado);
CREATE INDEX idx_solicitudes_tipo   ON solicitudes (tipo);
CREATE INDEX idx_solicitudes_correo ON solicitudes (correo);

