SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS orden_detalles (
    id INT NOT NULL AUTO_INCREMENT,
    orden_id INT NOT NULL,
    repuesto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    consumido TINYINT(1) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uq_orden_repuesto (orden_id, repuesto_id),
    KEY idx_detalles_repuesto (repuesto_id),
    CONSTRAINT fk_detalles_orden FOREIGN KEY (orden_id) REFERENCES ordenes_servicio (id),
    CONSTRAINT fk_detalles_repuesto FOREIGN KEY (repuesto_id) REFERENCES repuestos (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS orden_historial (
    id INT NOT NULL AUTO_INCREMENT,
    orden_id INT NOT NULL,
    usuario_id INT NULL,
    estado_anterior ENUM('Pendiente','En Proceso','Terminado','Entregado') NULL,
    estado_nuevo ENUM('Pendiente','En Proceso','Terminado','Entregado') NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_historial_orden (orden_id),
    CONSTRAINT fk_historial_orden FOREIGN KEY (orden_id) REFERENCES ordenes_servicio (id),
    CONSTRAINT fk_historial_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS movimientos_inventario (
    id INT NOT NULL AUTO_INCREMENT,
    repuesto_id INT NOT NULL,
    orden_id INT NULL,
    usuario_id INT NULL,
    tipo ENUM('entrada','salida','ajuste') NOT NULL,
    cantidad INT NOT NULL,
    stock_anterior INT NOT NULL,
    stock_nuevo INT NOT NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_movimientos_repuesto (repuesto_id),
    CONSTRAINT fk_movimientos_repuesto FOREIGN KEY (repuesto_id) REFERENCES repuestos (id),
    CONSTRAINT fk_movimientos_orden FOREIGN KEY (orden_id) REFERENCES ordenes_servicio (id) ON DELETE SET NULL,
    CONSTRAINT fk_movimientos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pagos (
    id INT NOT NULL AUTO_INCREMENT,
    orden_id INT NOT NULL,
    usuario_id INT NULL,
    monto DECIMAL(10,2) NOT NULL,
    metodo ENUM('efectivo','tarjeta','transferencia','otro') NOT NULL DEFAULT 'efectivo',
    referencia VARCHAR(100) NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_pagos_orden (orden_id),
    CONSTRAINT fk_pagos_orden FOREIGN KEY (orden_id) REFERENCES ordenes_servicio (id),
    CONSTRAINT fk_pagos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO orden_historial (orden_id, estado_anterior, estado_nuevo)
SELECT o.id, NULL, o.estado
FROM ordenes_servicio o
WHERE NOT EXISTS (
    SELECT 1 FROM orden_historial h WHERE h.orden_id = o.id
);

SET FOREIGN_KEY_CHECKS = 1;
