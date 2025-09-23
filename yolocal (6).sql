-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-09-2025 a las 02:11:33
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `yolocal`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `ID_Categoria` int(5) NOT NULL,
  `Descripcion` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`ID_Categoria`, `Descripcion`) VALUES
(1, 'Cafeteria'),
(2, 'Ferreteria'),
(6, 'Papelerias'),
(8, 'Electricas'),
(9, 'Pinturas'),
(10, 'Zapateria');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios`
--

CREATE TABLE `horarios` (
  `ID_Horario` int(11) NOT NULL,
  `ID_Negocio` int(11) NOT NULL,
  `dia_semana` enum('Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo') NOT NULL,
  `hora_apertura` time NOT NULL,
  `hora_cierre` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `horarios`
--

INSERT INTO `horarios` (`ID_Horario`, `ID_Negocio`, `dia_semana`, `hora_apertura`, `hora_cierre`) VALUES
(13, 7, 'Martes', '21:05:00', '09:05:00'),
(14, 7, 'Lunes', '22:08:00', '22:08:00'),
(15, 6, 'Miércoles', '09:00:00', '21:00:00'),
(16, 7, 'Lunes', '10:17:00', '22:17:00'),
(17, 7, 'Lunes', '22:58:00', '22:57:00'),
(18, 7, 'Martes', '08:00:00', '21:00:00'),
(19, 7, 'Miércoles', '11:30:00', '21:15:00'),
(20, 7, 'Jueves', '21:48:00', '06:48:00'),
(21, 7, 'Viernes', '12:48:00', '09:48:00'),
(22, 17, 'Lunes', '06:30:00', '21:00:00'),
(23, 4, 'Lunes', '21:11:00', '19:14:00'),
(24, 7, 'Sábado', '21:15:00', '19:18:00'),
(25, 5, 'Lunes', '09:41:00', '20:41:00'),
(26, 6, 'Lunes', '08:50:00', '20:50:00'),
(27, 7, 'Domingo', '07:43:00', '00:43:00'),
(28, 17, 'Lunes', '23:09:00', '21:11:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `negocios`
--

CREATE TABLE `negocios` (
  `ID_Negocio` int(11) NOT NULL,
  `ID_Usuario` int(11) NOT NULL,
  `nombre_negocio` varchar(100) NOT NULL,
  `DescripcionN` varchar(300) NOT NULL,
  `Direccion` varchar(200) NOT NULL,
  `Telefono` varchar(15) NOT NULL,
  `CorreoN` varchar(200) NOT NULL,
  `SitioWeb` varchar(200) NOT NULL,
  `Facebook` varchar(200) NOT NULL,
  `Instagram` varchar(200) NOT NULL,
  `TikTok` varchar(300) NOT NULL,
  `GoogleMaps` varchar(500) NOT NULL,
  `ID_Categoria` int(5) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `estado` tinyint(4) DEFAULT 1,
  `Relevancia` enum('1','2','3','') NOT NULL DEFAULT '1',
  `Rutaicono` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `negocios`
--

INSERT INTO `negocios` (`ID_Negocio`, `ID_Usuario`, `nombre_negocio`, `DescripcionN`, `Direccion`, `Telefono`, `CorreoN`, `SitioWeb`, `Facebook`, `Instagram`, `TikTok`, `GoogleMaps`, `ID_Categoria`, `fecha_registro`, `estado`, `Relevancia`, `Rutaicono`) VALUES
(4, 28, 'La Patona', 'Bar-Dedicado a la ofrecer la mejor calidad en platillos, mientras disfrutas de las mejores bebidas de la región', 'Enrique Segoviano #34', '2345678909', 'lapatona@gmail.com', 'dasdsa', 'dsadsa', 'dsadas', 'sii', 'https://maps.app.goo.gl/hftktLG2WoK2CsuJ8', 1, '2025-09-10 22:21:23', 0, '', ''),
(5, 23, 'El patrón', 'Disfruta de nuestros deliciosos Bufet Desayuno, Bufet Comida, Bufet Antojo Mexicano', 'Libertad Nte 605 frente al DIF municipal de San Martín Texmelucan.', '2481557389', 'elpatron@gmail.com', 'https://listado.mercadolibre.com.mx/carros-en-venta-en-puebla', 'https://www.restaurants10.com/MX/San-Mart%C3%ADn-Texmelucan-de-Labastida/100470161919017/El-Patr%C3%', 'https://www.restaurants10.com/MX/San-Mart%C3%ADn-Texmelucan-de-Labastida/100470161919017/El-Patr%C3%', '', '', 1, '2025-09-10 22:21:23', 1, '', ''),
(6, 28, 'Postres Yara', 'En Yara Gourmet elaboran todo tipo de pasteles y postres. Desde individuales, versiones tradicionales como también en sus apuestas personalizadas con originales mezclas de sabores y montajes que dejan sin aliento.', 'Av. Fuentes de Morelia, 330 58088 Morelia (Michoacán)', '2481823454', 'YaraGourmet@gmail.com', 'https://claude.ai/chat/34ebd873-3b9f-4293-b92a-d8461f730874', 'https://claude.ai/chat/34ebd873-3b9f-4293-b92a-d8461f730874', 'https://claude.ai/chat/34ebd873-3b9f-4293-b92a-d8461f730874', '', '', 1, '2025-09-10 22:21:23', 1, '3', ''),
(7, 29, 'La Rockola', '', '', '2481557890', '', '', '', '', '', '', 6, '2025-09-10 22:21:23', 0, '1', '../assets/uploads/iconos/icono_68c9fd4880b40_images.jfif'),
(14, 28, 'Zapateria Nuñez', 'Nos dedicamos a zapatos', 'Libertad Nte 605 frente al DIF municipal de San Martín Texmelucan.', '3214324324', '', '', '', '', 'http://localhost/yolocal//index.php?pag=ventas', '', 1, '2025-09-10 22:21:23', 1, '3', '../assets/uploads/iconos/icono_68c9fbff8e8c9_3-4-1.png'),
(17, 23, 'PanchaBochooo', '', '', '3214324324', 'elpatron@gmail.com', 'http://localhost/yolocal//index.php?pag=ventas', '', '', '', 'https://maps.app.goo.gl/hftktLG2WoK2CsuJ8', 1, '2025-09-10 22:21:23', 0, '3', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `negocio_imagenes`
--

CREATE TABLE `negocio_imagenes` (
  `ID_Imagen` int(11) NOT NULL,
  `ID_Negocio` int(11) NOT NULL,
  `ruta_imagen` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `negocio_imagenes`
--

INSERT INTO `negocio_imagenes` (`ID_Imagen`, `ID_Negocio`, `ruta_imagen`) VALUES
(19, 17, '../assets/uploads/panchabocho/img_68bf917c1b96b0.50724594.webp'),
(20, 17, '../assets/uploads/panchabocho/img_68bf917c1e27f4.20136815.jfif'),
(21, 14, '../assets/uploads/zapateria_nu__ez/img_68bf91a08758b2.61657992.png'),
(22, 14, '../assets/uploads/zapateria_nu__ez/img_68bf91a094ac06.27516607.webp'),
(23, 7, '../assets/uploads/la_rockola/img_68bf91dc0503d1.30879656.jpg'),
(24, 4, '../assets/uploads/la_patona/img_68bf921ec2af92.63447086.jpg'),
(25, 5, '../assets/uploads/el_patr__n/img_68bf92a30a5299.12275009.jfif'),
(26, 6, '../assets/uploads/postres_yara/img_68bf92bc6c70c2.10067895.jfif'),
(27, 4, '../assets/uploads/la_patona/img_68bf934a96ab80.72124761.jpg'),
(28, 5, '../assets/uploads/el_patr__n/img_68bf9395e87280.63938617.jfif');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promociones`
--

CREATE TABLE `promociones` (
  `ID_Promocion` int(11) NOT NULL,
  `ID_Negocio` int(11) NOT NULL,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `cantidad` int(4) DEFAULT NULL,
  `Canjeados` int(11) NOT NULL,
  `Descargados` int(11) NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `PromoMiercoles` tinyint(1) NOT NULL,
  `Estatus` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `promociones`
--

INSERT INTO `promociones` (`ID_Promocion`, `ID_Negocio`, `titulo`, `descripcion`, `cantidad`, `Canjeados`, `Descargados`, `fecha_fin`, `PromoMiercoles`, `Estatus`) VALUES
(1, 4, 'Paquete-llenes', 'Compra una parrillada especial y de regalo 2 chelas (Corona 600ml)', 50, 0, 0, '2025-09-11', 0, 1),
(5, 4, 'Paquete_mojes', 'Compra un paquete tiburon y la entrada al establecimiento es gratis', 49, 0, 0, '2025-08-22', 0, 1),
(6, 6, 'Promo Vainillosa', 'Todos los postres sabor vainilla 10% de descuento aplicando este cupon', 45, 3, 7, '2025-09-23', 0, 1),
(7, 5, 'Mariscos Fritos', 'Sabados de maricos fritos al 2x1', 21, 0, 0, '2025-08-22', 0, 0),
(8, 6, 'Ahorro Chocolatoso', 'Compra 10 docenas y la 11 es gratis', 53, 0, 0, '2025-08-20', 0, 1),
(10, 5, 'Promo Buffet', 'Miercoles de YoLocal, niños gratis', 56, 0, 0, '2025-08-22', 1, 0),
(11, 7, 'Sounds2', 'En la compra de una guitarra, te llevas un juego de cuerdas gratis', 35, 0, 0, '2025-08-30', 0, 1),
(12, 4, 'Viernes de Cubetazo', '2x1 en productos seleccionados', 49, 0, 0, '2025-08-30', 1, 0),
(16, 4, 'Promo Ejemplo', 'dtdf', 1, 0, 5, '2025-09-26', 1, 1),
(17, 7, 'PromoMas', 'dsadsad', NULL, 0, 0, NULL, 0, 0),
(18, 7, 'PromoMas', 'dsadsad', NULL, 0, 0, NULL, 0, 0),
(19, 4, 'Paquete_mojessss', 'fdsfds', 9, 1, 1, '2025-09-14', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajos`
--

CREATE TABLE `trabajos` (
  `ID_Trabajo` int(5) NOT NULL,
  `Titulo` varchar(30) NOT NULL,
  `Descripcion` varchar(500) NOT NULL,
  `ID_Negocio` int(5) NOT NULL,
  `Estatus` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `trabajos`
--

INSERT INTO `trabajos` (`ID_Trabajo`, `Titulo`, `Descripcion`, `ID_Negocio`, `Estatus`) VALUES
(1, 'Chambe Lan', 'Trabajar de bailarin en un', 4, 0),
(2, 'Paquete_mojes', 'csadsa', 4, 1),
(3, 'Paquete_mojes', 'dsadsa', 4, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `ID_Usuario` int(11) NOT NULL,
  `Correo` varchar(50) NOT NULL,
  `contra` varchar(255) NOT NULL,
  `tipo_usuario` enum('admin','negocio') NOT NULL,
  `Nombre` varchar(25) NOT NULL,
  `ApellidoP` varchar(25) NOT NULL,
  `ApellidoM` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`ID_Usuario`, `Correo`, `contra`, `tipo_usuario`, `Nombre`, `ApellidoP`, `ApellidoM`) VALUES
(23, 'saavedraaratthhjh@gmail.com', '$2y$10$gZOGWNaRtC55PmJ.XrOd7.zGT6OxsSs3dhFZ1CJILTCUOVfd3ns7i', 'negocio', 'Arath', 'Saavedra', 'Cabrera'),
(28, 'angeldomisal@gmail.com', '$2y$10$gZOGWNaRtC55PmJ.XrOd7.zGT6OxsSs3dhFZ1CJILTCUOVfd3ns7i', 'admin', 'Yara', 'Domingues', 'Salazarr'),
(29, 'kevinastrid@gmail.com', '$2y$10$kpjvqEqCZyJHqKDnANfCeunFc6kRqcKANYAHkOritHhYoMWNgF9gK', 'negocio', 'Kevis Astrid', 'Roldan', 'Cervantes'),
(30, 'saavedraaratthh@gmail.com', '$2y$10$jXSiL78oOXcgsYewxFaAEuwtEe7ViM2ZwMxxuOxCRjMk/DkhyIGGW', 'admin', 'Yara', 'Domingues', 'Salazarr');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`ID_Categoria`);

--
-- Indices de la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD PRIMARY KEY (`ID_Horario`),
  ADD KEY `ID_Negocio` (`ID_Negocio`);

--
-- Indices de la tabla `negocios`
--
ALTER TABLE `negocios`
  ADD PRIMARY KEY (`ID_Negocio`),
  ADD KEY `ID_Usuario` (`ID_Usuario`),
  ADD KEY `ID_Categoria` (`ID_Categoria`);

--
-- Indices de la tabla `negocio_imagenes`
--
ALTER TABLE `negocio_imagenes`
  ADD PRIMARY KEY (`ID_Imagen`),
  ADD KEY `ID_Negocio` (`ID_Negocio`);

--
-- Indices de la tabla `promociones`
--
ALTER TABLE `promociones`
  ADD PRIMARY KEY (`ID_Promocion`),
  ADD KEY `ID_Negocio` (`ID_Negocio`);

--
-- Indices de la tabla `trabajos`
--
ALTER TABLE `trabajos`
  ADD PRIMARY KEY (`ID_Trabajo`),
  ADD KEY `ID_Negocio` (`ID_Negocio`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`ID_Usuario`),
  ADD UNIQUE KEY `Correo` (`Correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `ID_Categoria` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `ID_Horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `negocios`
--
ALTER TABLE `negocios`
  MODIFY `ID_Negocio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `negocio_imagenes`
--
ALTER TABLE `negocio_imagenes`
  MODIFY `ID_Imagen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `promociones`
--
ALTER TABLE `promociones`
  MODIFY `ID_Promocion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `trabajos`
--
ALTER TABLE `trabajos`
  MODIFY `ID_Trabajo` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `ID_Usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `horarios`
--
ALTER TABLE `horarios`
  ADD CONSTRAINT `horarios_ibfk_1` FOREIGN KEY (`ID_Negocio`) REFERENCES `negocios` (`ID_Negocio`);

--
-- Filtros para la tabla `negocios`
--
ALTER TABLE `negocios`
  ADD CONSTRAINT `negocios_ibfk_1` FOREIGN KEY (`ID_Usuario`) REFERENCES `usuarios` (`ID_Usuario`),
  ADD CONSTRAINT `negocios_ibfk_2` FOREIGN KEY (`ID_Categoria`) REFERENCES `categorias` (`ID_Categoria`);

--
-- Filtros para la tabla `negocio_imagenes`
--
ALTER TABLE `negocio_imagenes`
  ADD CONSTRAINT `negocio_imagenes_ibfk_1` FOREIGN KEY (`ID_Negocio`) REFERENCES `negocios` (`ID_Negocio`) ON DELETE CASCADE;

--
-- Filtros para la tabla `promociones`
--
ALTER TABLE `promociones`
  ADD CONSTRAINT `promociones_ibfk_1` FOREIGN KEY (`ID_Negocio`) REFERENCES `negocios` (`ID_Negocio`);

--
-- Filtros para la tabla `trabajos`
--
ALTER TABLE `trabajos`
  ADD CONSTRAINT `trabajos_ibfk_1` FOREIGN KEY (`ID_Negocio`) REFERENCES `negocios` (`ID_Negocio`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
