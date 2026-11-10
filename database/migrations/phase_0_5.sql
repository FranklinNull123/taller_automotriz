SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT NOT NULL AUTO_INCREMENT,
    usuario VARCHAR(80) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'recepcion', 'mecanico') NOT NULL DEFAULT 'recepcion',
    activo TINYINT(1) NOT NULL DEFAULT 1,
    debe_cambiar_password TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY uq_usuarios_usuario (usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO usuarios (usuario, password_hash, rol, activo, debe_cambiar_password)
VALUES ('admin', '$argon2id$v=19$m=65536,t=4,p=1$SXUzMVg1Snd6VkdTUU5nYg$BwhEfTBgKfbdeIgp63VJrVHrs/lyRcMw8ARTtkMG1Ls', 'admin', 1, 1)
ON DUPLICATE KEY UPDATE usuario = VALUES(usuario);

ALTER TABLE clientes ENGINE = InnoDB, CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE vehiculos ENGINE = InnoDB, CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE ordenes_servicio ENGINE = InnoDB, CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE repuestos ENGINE = InnoDB, CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE garantias ENGINE = InnoDB, CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE ordenes_servicio ADD COLUMN total DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER fecha_ingreso;

ALTER TABLE vehiculos
    ADD CONSTRAINT fk_vehiculos_cliente FOREIGN KEY (cliente_id) REFERENCES clientes (id);
ALTER TABLE ordenes_servicio
    ADD CONSTRAINT fk_ordenes_vehiculo FOREIGN KEY (vehiculo_id) REFERENCES vehiculos (id);
ALTER TABLE garantias
    ADD CONSTRAINT fk_garantias_orden FOREIGN KEY (orden_id) REFERENCES ordenes_servicio (id);

SET FOREIGN_KEY_CHECKS = 1;
