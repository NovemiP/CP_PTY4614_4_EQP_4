-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-11-2024 a las 21:01:09
-- Versión del servidor: 11.5.2-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `stock_control`
--

DELIMITER $$
--
-- Funciones
--
CREATE DEFINER=`root`@`localhost` FUNCTION `calcular_total_existencia` () RETURNS INT(11) DETERMINISTIC BEGIN
    DECLARE total INT;
    
    SELECT SUM(existencia_actual) INTO total
    FROM inventario;
    
    RETURN COALESCE(total, 0);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `calcular_valorizacion_total` () RETURNS DECIMAL(10,2)  BEGIN
    DECLARE total_valorizacion DECIMAL(10, 2);
    
    SELECT IFNULL(SUM(existencia_actual * valor_unitario* 1.19), 0) INTO total_valorizacion
    FROM inventario
    LEFT JOIN producto ON inventario.producto_id_producto = producto.id_producto;

    RETURN total_valorizacion; -- retorna el valor total del inventario
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `contar_total_productos` () RETURNS INT(11) DETERMINISTIC BEGIN
    DECLARE total INT;
    SELECT COUNT(*) INTO total FROM producto;
    RETURN total;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL,
  `nombre_categoria` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nombre_categoria`, `descripcion`) VALUES
(21, 'Electrónicos', 'Productos tecnológicos come telefonos,pcs y tables.'),
(22, 'Muebles', 'Artículos para el hogar y oficina.'),
(23, 'Alimentos y bebidas', 'Productos comestibles y bebidas.'),
(24, 'Herramientas', 'Equipos y utensilios para trabajos manuales y construcción'),
(25, 'Productos de limpieza', 'Artículos de limpieza doméstica');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contacto` varchar(100) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `ciudad` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nombre`, `direccion`, `telefono`, `correo`, `contacto`, `estado`, `ciudad`) VALUES
(19, 'ElectroShop', 'Calle de la Tecnología 123, Local 10', '57303294', 'contacto@electroshop.com', 'Claudio Quintana', 'Activo', 'Concepción'),
(20, 'Almacen food', 'Av. Gourmet 88, Local 5', '93345655', 'info@alimentosgourmet.com', 'Camila Perez', 'Activo', 'Talcahuano'),
(21, 'Construcción Pro', 'Calle de la Obra 14, Local 7', '92333456', 'ventas@construccionpro.com', 'Erlind Halaand', 'Activo', 'San pedro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_factura`
--

CREATE TABLE `detalle_factura` (
  `id_detalle_factura` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `factura_id_factura` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_factura`
--

INSERT INTO `detalle_factura` (`id_detalle_factura`, `cantidad`, `factura_id_factura`) VALUES
(53, 50, 145),
(54, 50, 146),
(55, 20, 147);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_guia`
--

CREATE TABLE `detalle_guia` (
  `id_detalle_guia` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `guia_traslado_id_guia_traslado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_guia`
--

INSERT INTO `detalle_guia` (`id_detalle_guia`, `cantidad`, `guia_traslado_id_guia_traslado`) VALUES
(37, 40, 88),
(38, 15, 89);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura`
--

CREATE TABLE `factura` (
  `id_factura` int(11) NOT NULL,
  `nro_factura` varchar(50) NOT NULL,
  `fecha_emision` date NOT NULL,
  `total_facturado` decimal(15,2) NOT NULL,
  `usuario_id_usuario` int(11) NOT NULL,
  `inventario_id_inventario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `factura`
--

INSERT INTO `factura` (`id_factura`, `nro_factura`, `fecha_emision`, `total_facturado`, `usuario_id_usuario`, `inventario_id_inventario`) VALUES
(145, 'FCT113599', '2024-11-16', 56525000.00, 26, 349),
(146, 'FCT818476', '2024-11-16', 35700000.00, 26, 350),
(147, 'FCT317303', '2024-11-16', 1523200.00, 26, 351);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `guia_traslado`
--

CREATE TABLE `guia_traslado` (
  `id_guia_traslado` int(11) NOT NULL,
  `nro_guia` varchar(50) NOT NULL,
  `fecha_emision` date NOT NULL,
  `destino` varchar(255) NOT NULL,
  `cliente_id_cliente` int(11) NOT NULL,
  `usuario_id_usuario` int(11) NOT NULL,
  `inventario_id_inventario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `guia_traslado`
--

INSERT INTO `guia_traslado` (`id_guia_traslado`, `nro_guia`, `fecha_emision`, `destino`, `cliente_id_cliente`, `usuario_id_usuario`, `inventario_id_inventario`) VALUES
(88, 'GT718652', '2024-11-16', 'Calle de la Tecnología 123, Local 10', 19, 26, 349),
(89, 'GT944637', '2024-11-16', 'Calle de la Obra 14, Local 7', 21, 26, 351);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id_inventario` int(11) NOT NULL,
  `tipo_movimiento` varchar(50) NOT NULL,
  `fecha` date NOT NULL,
  `estado_inve` varchar(20) NOT NULL,
  `producto_id_producto` int(11) NOT NULL,
  `usuario_id_usuario` int(11) NOT NULL,
  `existencia_inicial` int(11) NOT NULL,
  `existencia_actual` int(11) NOT NULL,
  `registrado_por` varchar(100) NOT NULL,
  `valor_total` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id_inventario`, `tipo_movimiento`, `fecha`, `estado_inve`, `producto_id_producto`, `usuario_id_usuario`, `existencia_inicial`, `existencia_actual`, `registrado_por`, `valor_total`) VALUES
(349, 'Entrada', '2024-11-16', 'Activo', 72, 26, 50, 10, 'Alan Navarrete', 9500000.00),
(350, 'Entrada', '2024-11-16', 'Activo', 75, 26, 50, 50, 'Alan Navarrete', 35700000.00),
(351, 'Entrada', '2024-11-16', 'Activo', 74, 26, 20, 5, 'Alan Navarrete', 320000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimiento`
--

CREATE TABLE `movimiento` (
  `id_movimiento` int(11) NOT NULL,
  `movimiento` varchar(20) NOT NULL,
  `fecha_movimiento` date NOT NULL,
  `inventario_id_inventario` int(11) NOT NULL,
  `usuario_id_usuario` int(11) NOT NULL,
  `cliente_id_cliente` int(11) DEFAULT NULL,
  `salida_id_salida` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimiento`
--

INSERT INTO `movimiento` (`id_movimiento`, `movimiento`, `fecha_movimiento`, `inventario_id_inventario`, `usuario_id_usuario`, `cliente_id_cliente`, `salida_id_salida`) VALUES
(286, 'Entrada', '2024-11-16', 349, 26, NULL, NULL),
(287, 'Entrada', '2024-11-16', 350, 26, NULL, NULL),
(288, 'Salida', '2024-11-16', 349, 26, 19, 162),
(289, 'Entrada', '2024-11-16', 351, 26, NULL, NULL),
(290, 'Salida', '2024-11-16', 351, 26, 21, 163);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id_producto` int(11) NOT NULL,
  `cod_producto` varchar(50) NOT NULL,
  `nombre_producto` varchar(100) NOT NULL,
  `unidad_medida` varchar(50) NOT NULL,
  `valor_unitario` decimal(15,2) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `fecha_registro_prod` date NOT NULL,
  `categoria_id_categoria` int(11) NOT NULL,
  `ubicacion_id_ubicacion` int(11) NOT NULL,
  `proveedor_id_proveedor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id_producto`, `cod_producto`, `nombre_producto`, `unidad_medida`, `valor_unitario`, `estado`, `fecha_registro_prod`, `categoria_id_categoria`, `ubicacion_id_ubicacion`, `proveedor_id_proveedor`) VALUES
(72, 'PD-ea919', 'Iphone 16', 'Und.', 950000.00, 'Activo', '2024-11-16', 21, 1, 21),
(73, 'PD-a857e', 'Coca-cola', 'Caj.', 15000.00, 'Activo', '2024-11-16', 23, 2, 22),
(74, 'PD-72aaa', 'Taladro black', '', 64000.00, 'Activo', '2024-11-16', 21, 1, 21),
(75, 'PD-82f2d', 'Ipad pro', 'Und.', 600000.00, 'Activo', '2024-11-16', 21, 1, 21);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `id_proveedor` int(11) NOT NULL,
  `nombre_prove` varchar(100) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contacto` varchar(100) NOT NULL,
  `estado` varchar(20) NOT NULL,
  `ciudad` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`id_proveedor`, `nombre_prove`, `direccion`, `telefono`, `correo`, `contacto`, `estado`, `ciudad`) VALUES
(21, 'TechWorld ', 'Calle 45 #123, Ciudad Tech, Estado 6789', '96763537', 'contacto@techworld.com', 'Lautaro del campo', 'Activo', 'Concepción'),
(22, 'FreshFoods', 'Calle Alimentos 30, Zona Agro, Ciudad Nutri, Estado 1122', '94567821', 'contacto@freshfoods.com', 'Cristopher Nava', 'Activo', 'Concepción'),
(23, 'PowerTools ', 'Calle Herramientas 100, Zona Industrial, Ciudad Fuerte, Estado 1123', '93457893', 'ventas@powertoolsco.com', 'Martin Perez', 'Activo', 'Concepción');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salida`
--

CREATE TABLE `salida` (
  `id_salida` int(11) NOT NULL,
  `tipo_movimiento` varchar(50) NOT NULL,
  `cantidad_salida` int(11) NOT NULL,
  `fecha_salida` datetime NOT NULL,
  `registrado_por` varchar(100) NOT NULL,
  `inventario_id_inventario` int(11) NOT NULL,
  `cliente_id_cliente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `salida`
--

INSERT INTO `salida` (`id_salida`, `tipo_movimiento`, `cantidad_salida`, `fecha_salida`, `registrado_por`, `inventario_id_inventario`, `cliente_id_cliente`) VALUES
(162, 'Salida', 40, '2024-11-16 00:00:00', 'Alan Navarrete', 349, 19),
(163, 'Salida', 15, '2024-11-16 00:00:00', 'Alan Navarrete', 351, 21);

--
-- Disparadores `salida`
--
DELIMITER $$
CREATE TRIGGER `actualizar_existencia_tras_salida` AFTER INSERT ON `salida` FOR EACH ROW BEGIN
    UPDATE inventario
    SET existencia_actual = existencia_actual - NEW.cantidad_salida
    WHERE id_inventario = NEW.inventario_id_inventario;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicacion`
--

CREATE TABLE `ubicacion` (
  `id_ubicacion` int(11) NOT NULL,
  `nombre_zona` varchar(100) NOT NULL,
  `descripcion_ubi` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ubicacion`
--

INSERT INTO `ubicacion` (`id_ubicacion`, `nombre_zona`, `descripcion_ubi`) VALUES
(1, 'Zona General', 'Área destinada para almacenamiento de productos electrónicos.'),
(2, 'Zona Alimentos', 'Espacio dedicado a productos perecederos.'),
(3, 'Zona Herramientas y limpieza', 'Almacenamiento de productos de limpieza y químicos.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `rol` varchar(20) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `estado` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `apellido`, `correo`, `rol`, `contrasena`, `estado`) VALUES
(1, 'admin', 'stock', 'admin.stock@stock.cl', 'Administrador', '$2y$10$CAsWkIqChP9yaJFubx3imedFcUKp0IM.H1t0bPyKOMZtrsTOqmZ4a', 'Activo'),
(26, 'Alan', 'Navarrete', 'alan.navarrete@stock.cl', 'Operador', '$2y$10$lI6hFs0.DwAUYf8/LmOURuKp7KGF1rGe3tZpNSCKr50Zs/HiqyOgi', 'Activo');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_notificaciones_baja_existencia`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_notificaciones_baja_existencia` (
`nombre_producto` varchar(100)
,`existencia_actual` int(11)
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_notificaciones_baja_existencia`
--
DROP TABLE IF EXISTS `vista_notificaciones_baja_existencia`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vista_notificaciones_baja_existencia`  AS SELECT `producto`.`nombre_producto` AS `nombre_producto`, `inventario`.`existencia_actual` AS `existencia_actual` FROM (`inventario` join `producto` on(`inventario`.`producto_id_producto` = `producto`.`id_producto`)) WHERE `inventario`.`existencia_actual` < 10 ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`);

--
-- Indices de la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  ADD PRIMARY KEY (`id_detalle_factura`),
  ADD KEY `factura_id_factura` (`factura_id_factura`);

--
-- Indices de la tabla `detalle_guia`
--
ALTER TABLE `detalle_guia`
  ADD PRIMARY KEY (`id_detalle_guia`),
  ADD KEY `guia_traslado_id_guia_traslado` (`guia_traslado_id_guia_traslado`);

--
-- Indices de la tabla `factura`
--
ALTER TABLE `factura`
  ADD PRIMARY KEY (`id_factura`),
  ADD KEY `usuario_id_usuario` (`usuario_id_usuario`),
  ADD KEY `inventario_id_inventario` (`inventario_id_inventario`);

--
-- Indices de la tabla `guia_traslado`
--
ALTER TABLE `guia_traslado`
  ADD PRIMARY KEY (`id_guia_traslado`),
  ADD KEY `cliente_id_cliente` (`cliente_id_cliente`),
  ADD KEY `usuario_id_usuario` (`usuario_id_usuario`),
  ADD KEY `inventario_id_inventario` (`inventario_id_inventario`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id_inventario`),
  ADD KEY `usuario_id_usuario` (`usuario_id_usuario`),
  ADD KEY `producto_id_producto` (`producto_id_producto`);

--
-- Indices de la tabla `movimiento`
--
ALTER TABLE `movimiento`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `cliente_id_cliente` (`cliente_id_cliente`),
  ADD KEY `usuario_id_usuario` (`usuario_id_usuario`),
  ADD KEY `salida_id_salida` (`salida_id_salida`) USING BTREE,
  ADD KEY `movimiento_ibfk_2` (`inventario_id_inventario`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `categoria_id_categoria` (`categoria_id_categoria`),
  ADD KEY `ubicacion_id_ubicacion` (`ubicacion_id_ubicacion`),
  ADD KEY `proveedor_id_proveedor` (`proveedor_id_proveedor`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id_proveedor`);

--
-- Indices de la tabla `salida`
--
ALTER TABLE `salida`
  ADD PRIMARY KEY (`id_salida`),
  ADD KEY `inventario_id_inventario` (`inventario_id_inventario`),
  ADD KEY `cliente_id_cliente` (`cliente_id_cliente`);

--
-- Indices de la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  ADD PRIMARY KEY (`id_ubicacion`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  MODIFY `id_detalle_factura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT de la tabla `detalle_guia`
--
ALTER TABLE `detalle_guia`
  MODIFY `id_detalle_guia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de la tabla `factura`
--
ALTER TABLE `factura`
  MODIFY `id_factura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=148;

--
-- AUTO_INCREMENT de la tabla `guia_traslado`
--
ALTER TABLE `guia_traslado`
  MODIFY `id_guia_traslado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id_inventario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=352;

--
-- AUTO_INCREMENT de la tabla `movimiento`
--
ALTER TABLE `movimiento`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=291;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `salida`
--
ALTER TABLE `salida`
  MODIFY `id_salida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT de la tabla `ubicacion`
--
ALTER TABLE `ubicacion`
  MODIFY `id_ubicacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  ADD CONSTRAINT `detalle_factura_ibfk_1` FOREIGN KEY (`factura_id_factura`) REFERENCES `factura` (`id_factura`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_guia`
--
ALTER TABLE `detalle_guia`
  ADD CONSTRAINT `detalle_guia_ibfk_1` FOREIGN KEY (`guia_traslado_id_guia_traslado`) REFERENCES `guia_traslado` (`id_guia_traslado`) ON DELETE CASCADE;

--
-- Filtros para la tabla `factura`
--
ALTER TABLE `factura`
  ADD CONSTRAINT `factura_ibfk_1` FOREIGN KEY (`usuario_id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `factura_ibfk_2` FOREIGN KEY (`inventario_id_inventario`) REFERENCES `inventario` (`id_inventario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `guia_traslado`
--
ALTER TABLE `guia_traslado`
  ADD CONSTRAINT `guia_traslado_ibfk_1` FOREIGN KEY (`cliente_id_cliente`) REFERENCES `cliente` (`id_cliente`),
  ADD CONSTRAINT `guia_traslado_ibfk_2` FOREIGN KEY (`usuario_id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `guia_traslado_ibfk_3` FOREIGN KEY (`inventario_id_inventario`) REFERENCES `inventario` (`id_inventario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`usuario_id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `producto_id_producto` FOREIGN KEY (`producto_id_producto`) REFERENCES `producto` (`id_producto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimiento`
--
ALTER TABLE `movimiento`
  ADD CONSTRAINT `fk_salida` FOREIGN KEY (`salida_id_salida`) REFERENCES `salida` (`id_salida`),
  ADD CONSTRAINT `movimiento_ibfk_1` FOREIGN KEY (`cliente_id_cliente`) REFERENCES `cliente` (`id_cliente`),
  ADD CONSTRAINT `movimiento_ibfk_2` FOREIGN KEY (`inventario_id_inventario`) REFERENCES `inventario` (`id_inventario`) ON DELETE CASCADE,
  ADD CONSTRAINT `movimiento_ibfk_3` FOREIGN KEY (`usuario_id_usuario`) REFERENCES `usuario` (`id_usuario`);

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`categoria_id_categoria`) REFERENCES `categoria` (`id_categoria`),
  ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`ubicacion_id_ubicacion`) REFERENCES `ubicacion` (`id_ubicacion`),
  ADD CONSTRAINT `producto_ibfk_3` FOREIGN KEY (`proveedor_id_proveedor`) REFERENCES `proveedor` (`id_proveedor`);

--
-- Filtros para la tabla `salida`
--
ALTER TABLE `salida`
  ADD CONSTRAINT `fk_cliente_id_cliente` FOREIGN KEY (`cliente_id_cliente`) REFERENCES `cliente` (`id_cliente`),
  ADD CONSTRAINT `salida_ibfk_1` FOREIGN KEY (`inventario_id_inventario`) REFERENCES `inventario` (`id_inventario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `salida_ibfk_2` FOREIGN KEY (`cliente_id_cliente`) REFERENCES `cliente` (`id_cliente`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
