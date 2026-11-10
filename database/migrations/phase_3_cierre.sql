CREATE TABLE IF NOT EXISTS cotizaciones (
    id INT NOT NULL AUTO_INCREMENT,
    orden_id INT NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    descuento DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    impuesto DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    estado ENUM('Pendiente','Aprobada','Rechazada') NOT NULL DEFAULT 'Pendiente',
    aprobada_en DATETIME NULL,
    creado_en TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_cotizacion_orden (orden_id),
    CONSTRAINT fk_cotizacion_orden FOREIGN KEY (orden_id) REFERENCES ordenes_servicio (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO cotizaciones (orden_id, subtotal, total, estado)
SELECT o.id, o.total, o.total, 'Aprobada'
FROM ordenes_servicio o
WHERE NOT EXISTS (SELECT 1 FROM cotizaciones c WHERE c.orden_id = o.id);
