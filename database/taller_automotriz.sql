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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `documento`, `telefono`, `email`, `fecha_registro`) VALUES
(1, 'Diego Lopez', '45678912', '987654321', 'diego@correo.com', '2026-10-27 19:21:36'),
(2, 'Sara Mendoza', '78912345', '912345678', 'sara@correo.com', '2026-10-27 19:21:36'),
(3, 'Xhamin Mancilla', '12345678', '998877665', 'xhamin@correo.com', '2026-10-27 19:21:36');

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
(1, 3, 'Revisión y cambio de cableado principal', '2027-04-20', 'Activa');

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ordenes_servicio`
--

INSERT INTO `ordenes_servicio` (`id`, `vehiculo_id`, `descripcion_falla`, `estado`, `fecha_ingreso`) VALUES
(1, 1, 'Frenos largos, requiere revisión de pastillas y líquido.', 'Pendiente', '2026-10-27 19:21:36'),
(2, 2, 'Cambio de aceite y filtros generales.', 'En Proceso', '2026-10-27 19:21:36'),
(3, 3, 'Falla en el sistema eléctrico, luces parpadean.', 'Terminado', '2026-10-27 19:21:36');

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `repuestos`
--

INSERT INTO `repuestos` (`id`, `codigo`, `descripcion`, `categoria`, `stock`, `precio_unitario`) VALUES
(1, 'REP-001', 'Pastillas de Freno Toyota', 'Frenos', 15, '85.50'),
(2, 'REP-002', 'Aceite Sintético 5W-30', 'Lubricantes', 30, '120.00'),
(3, 'REP-003', 'Filtro de Aire Nissan', 'Filtros', 25, '45.00');

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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `vehiculos`
--

INSERT INTO `vehiculos` (`id`, `cliente_id`, `placa`, `marca`, `modelo`, `anio`) VALUES
(1, 1, 'W1A-123', 'Toyota', 'Hilux', 2020),
(2, 2, 'F4B-456', 'Nissan', 'Sentra', 2018),
(3, 3, 'C7C-789', 'Hyundai', 'Tucson', 2022);

ALTER TABLE `vehiculos`
  ADD CONSTRAINT `fk_vehiculos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`);

ALTER TABLE `ordenes_servicio`
  ADD CONSTRAINT `fk_ordenes_vehiculo` FOREIGN KEY (`vehiculo_id`) REFERENCES `vehiculos` (`id`);

ALTER TABLE `garantias`
  ADD CONSTRAINT `fk_garantias_orden` FOREIGN KEY (`orden_id`) REFERENCES `ordenes_servicio` (`id`);
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
