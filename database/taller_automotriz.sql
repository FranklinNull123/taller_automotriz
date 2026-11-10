-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 27-10-2026 a las 19:31:00
-- Versión del servidor: 5.7.40
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `taller_automotriz`
--

DROP TABLE IF EXISTS `movimientos_inventario`;
DROP TABLE IF EXISTS `orden_historial`;
DROP TABLE IF EXISTS `orden_detalles`;
DROP TABLE IF EXISTS `pagos`;
DROP TABLE IF EXISTS `cotizaciones`;
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(80) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('admin','recepcion','mecanico') NOT NULL DEFAULT 'recepcion',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `debe_cambiar_password` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `usuarios` (`usuario`, `password_hash`, `rol`, `activo`, `debe_cambiar_password`)
VALUES ('admin', '$argon2id$v=19$m=65536,t=4,p=1$SXUzMVg1Snd6VkdTUU5nYg$BwhEfTBgKfbdeIgp63VJrVHrs/lyRcMw8ARTtkMG1Ls', 'admin', 1, 1)
ON DUPLICATE KEY UPDATE `usuario` = VALUES(`usuario`);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `documento` varchar(20) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `documento` (`documento`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `documento`, `telefono`, `email`, `fecha_registro`) VALUES
(1, 'Diego Lopez', '45678912', '987654321', 'diego@correo.com', '2026-10-27 19:21:36'),
(2, 'Sara Mendoza', '78912345', '912345678', 'sara@correo.com', '2026-10-27 19:21:36'),
(3, 'Carlos Ramirez', '12345678', '998877665', 'carlos@correo.com', '2026-10-27 19:21:36'),
(4, 'Lucia Torres', '33445566', '955443322', 'lucia@correo.com', '2026-11-02 09:15:00'),
(5, 'Mateo Castillo', '44556677', '966554433', 'mateo@correo.com', '2026-11-03 10:20:00'),
(6, 'Valeria Rojas', '55667788', '977665544', 'valeria@correo.com', '2026-11-04 11:10:00'),
(7, 'Jorge Navarro', '66778899', '988776655', 'jorge@correo.com', '2026-11-05 14:30:00'),
(8, 'Andrea Salazar', '77889900', '999887766', 'andrea@correo.com', '2026-11-06 16:45:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `garantias`
--

DROP TABLE IF EXISTS `garantias`;
CREATE TABLE IF NOT EXISTS `garantias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orden_id` int(11) NOT NULL,
  `servicio_realizado` text NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `estado` enum('Activa','Vencida','Reclamada') DEFAULT 'Activa',
  PRIMARY KEY (`id`),
  KEY `orden_id` (`orden_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `garantias`
--

INSERT INTO `garantias` (`id`, `orden_id`, `servicio_realizado`, `fecha_vencimiento`, `estado`) VALUES
(1, 3, 'Revisión y cambio de cableado principal', '2027-04-20', 'Activa'),
(2, 5, 'Cambio de kit de embrague', '2026-12-05', 'Activa'),
(3, 6, 'Servicio de frenos y purgado', '2027-01-18', 'Activa'),
(4, 8, 'Reparación de sistema de refrigeración', '2026-11-25', 'Activa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes_servicio`
--

DROP TABLE IF EXISTS `ordenes_servicio`;
CREATE TABLE IF NOT EXISTS `ordenes_servicio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vehiculo_id` int(11) NOT NULL,
  `descripcion_falla` text,
  `estado` enum('Pendiente','En Proceso','Terminado','Entregado') DEFAULT 'Pendiente',
  `fecha_ingreso` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `vehiculo_id` (`vehiculo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ordenes_servicio`
--

INSERT INTO `ordenes_servicio` (`id`, `vehiculo_id`, `descripcion_falla`, `estado`, `fecha_ingreso`, `total`) VALUES
(1, 1, 'Frenos largos, requiere revisión de pastillas y líquido.', 'Pendiente', '2026-11-01 08:30:00', 0.00),
(2, 2, 'Cambio de aceite y filtros generales.', 'En Proceso', '2026-11-02 09:00:00', 320.00),
(3, 3, 'Falla en el sistema eléctrico, luces parpadean.', 'Terminado', '2026-11-03 10:15:00', 480.00),
(4, 4, 'Ruido en suspensión delantera al pasar por baches.', 'Pendiente', '2026-11-04 11:20:00', 0.00),
(5, 5, 'Deslizamiento del embrague y dificultad para cambiar marchas.', 'En Proceso', '2026-11-05 08:45:00', 1250.00),
(6, 6, 'Vibración al frenar y desgaste irregular de pastillas.', 'Terminado', '2026-11-06 13:10:00', 690.00),
(7, 7, 'Mantenimiento preventivo de motor y revisión de niveles.', 'Entregado', '2026-10-28 15:00:00', 410.00),
(8, 8, 'Temperatura elevada y pérdida de líquido refrigerante.', 'En Proceso', '2026-11-07 09:40:00', 875.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `repuestos`
--

DROP TABLE IF EXISTS `repuestos`;
CREATE TABLE IF NOT EXISTS `repuestos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `stock` int(11) DEFAULT '0',
  `precio_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `repuestos`
--

INSERT INTO `repuestos` (`id`, `codigo`, `descripcion`, `categoria`, `stock`, `precio_unitario`) VALUES
(1, 'REP-001', 'Pastillas de Freno Toyota', 'Frenos', 15, '85.50'),
(2, 'REP-002', 'Aceite Sintético 5W-30', 'Lubricantes', 30, '120.00'),
(3, 'REP-003', 'Filtro de Aire Nissan', 'Filtros', 25, '45.00'),
(4, 'REP-004', 'Bujías de Encendido Bosch', 'Motor', 4, '38.90'),
(5, 'REP-005', 'Kit de Embrague Chevrolet', 'Transmisión', 2, '680.00'),
(6, 'REP-006', 'Líquido Refrigerante 1L', 'Refrigeración', 3, '22.50'),
(7, 'REP-007', 'Amortiguador Delantero', 'Suspensión', 8, '210.00'),
(8, 'REP-008', 'Correa de Distribución', 'Motor', 6, '145.00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vehiculos`
--

DROP TABLE IF EXISTS `vehiculos`;
CREATE TABLE IF NOT EXISTS `vehiculos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `placa` varchar(15) NOT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `modelo` varchar(50) DEFAULT NULL,
  `anio` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `placa` (`placa`),
  KEY `cliente_id` (`cliente_id`),
  CONSTRAINT `fk_vehiculos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`id`, `cliente_id`, `placa`, `marca`, `modelo`, `anio`) VALUES
(1, 1, 'W1A-123', 'Toyota', 'Hilux', 2020),
(2, 2, 'F4B-456', 'Nissan', 'Sentra', 2018),
(3, 3, 'C7C-789', 'Hyundai', 'Tucson', 2022),
(4, 4, 'A8D-234', 'Kia', 'Sportage', 2021),
(5, 5, 'B9E-567', 'Chevrolet', 'Tracker', 2023),
(6, 6, 'D2F-890', 'Mazda', 'CX-5', 2019),
(7, 7, 'E3G-145', 'Suzuki', 'Swift', 2020),
(8, 8, 'H4J-678', 'Volkswagen', 'T-Cross', 2022);

ALTER TABLE `vehiculos`
  ADD CONSTRAINT `fk_vehiculos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);

ALTER TABLE `ordenes_servicio`
  ADD CONSTRAINT `fk_ordenes_vehiculo` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`id`);

ALTER TABLE `garantias`
  ADD CONSTRAINT `fk_garantias_orden` FOREIGN KEY (`orden_id`) REFERENCES `ordenes_servicio` (`id`);

CREATE TABLE `orden_detalles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `orden_id` int NOT NULL,
  `repuesto_id` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `consumido` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_orden_repuesto` (`orden_id`,`repuesto_id`),
  KEY `idx_detalles_repuesto` (`repuesto_id`),
  CONSTRAINT `fk_detalles_orden` FOREIGN KEY (`orden_id`) REFERENCES `ordenes_servicio` (`id`),
  CONSTRAINT `fk_detalles_repuesto` FOREIGN KEY (`repuesto_id`) REFERENCES `repuestos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `orden_historial` (
  `id` int NOT NULL AUTO_INCREMENT,
  `orden_id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `estado_anterior` enum('Pendiente','En Proceso','Terminado','Entregado') DEFAULT NULL,
  `estado_nuevo` enum('Pendiente','En Proceso','Terminado','Entregado') NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_historial_orden` (`orden_id`),
  CONSTRAINT `fk_historial_orden` FOREIGN KEY (`orden_id`) REFERENCES `ordenes_servicio` (`id`),
  CONSTRAINT `fk_historial_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `movimientos_inventario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `repuesto_id` int NOT NULL,
  `orden_id` int DEFAULT NULL,
  `usuario_id` int DEFAULT NULL,
  `tipo` enum('entrada','salida','ajuste') NOT NULL,
  `cantidad` int NOT NULL,
  `stock_anterior` int NOT NULL,
  `stock_nuevo` int NOT NULL,
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_movimientos_repuesto` (`repuesto_id`),
  CONSTRAINT `fk_movimientos_repuesto` FOREIGN KEY (`repuesto_id`) REFERENCES `repuestos` (`id`),
  CONSTRAINT `fk_movimientos_orden` FOREIGN KEY (`orden_id`) REFERENCES `ordenes_servicio` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_movimientos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `pagos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `orden_id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo` enum('efectivo','tarjeta','transferencia','otro') NOT NULL DEFAULT 'efectivo',
  `referencia` varchar(100) DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pagos_orden` (`orden_id`),
  CONSTRAINT `fk_pagos_orden` FOREIGN KEY (`orden_id`) REFERENCES `ordenes_servicio` (`id`),
  CONSTRAINT `fk_pagos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cotizaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `orden_id` int NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `descuento` decimal(10,2) NOT NULL DEFAULT '0.00',
  `impuesto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado` enum('Pendiente','Aprobada','Rechazada') NOT NULL DEFAULT 'Pendiente',
  `aprobada_en` datetime DEFAULT NULL,
  `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_cotizacion_orden` (`orden_id`),
  CONSTRAINT `fk_cotizacion_orden` FOREIGN KEY (`orden_id`) REFERENCES `ordenes_servicio` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `cotizaciones` (`orden_id`, `subtotal`, `total`, `estado`)
SELECT o.id, o.total, o.total, 'Aprobada' FROM `ordenes_servicio` o;

INSERT INTO `orden_historial` (`orden_id`, `estado_anterior`, `estado_nuevo`)
SELECT o.id, NULL, o.estado FROM `ordenes_servicio` o;
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
