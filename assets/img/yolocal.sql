-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 21-09-2025 a las 04:28:07
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

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
  `ID_Categoria` int(5) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `Relevancia` enum('1','2','3') NOT NULL DEFAULT '1',
  `Rutaicono` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `negocios`
--

INSERT INTO `negocios` (`ID_Negocio`, `ID_Usuario`, `nombre_negocio`, `DescripcionN`, `Direccion`, `Telefono`, `CorreoN`, `SitioWeb`, `Facebook`, `Instagram`, `TikTok`, `ID_Categoria`, `fecha_registro`, `estado`, `Relevancia`, `Rutaicono`) VALUES
(32, 30, 'Oxxo', '', '', '', '', '', '', '', '', 1, '2025-09-14 22:50:46', 1, '3', ''),
(34, 30, 'Papas11', '', '', '', '', '', '', '', '', 2, '2025-09-14 22:56:51', 1, '2', '../assets/uploads/iconos/icono_68cf555b80973_526775559_17930683326079782_896374747334815688_n.jpg'),
(35, 30, 'Papas', '', '', '', '', '', '', '', '', 1, '2025-09-14 22:50:59', 1, '1', ''),
(39, 30, '1233', '', '', '', '', '', '', '', '', 6, '2025-09-20 20:15:02', 1, '1', ''),
(40, 30, 'dfssfs', '', '', '', '', '', '', '', '', 6, '2025-09-20 20:15:07', 1, '1', ''),
(41, 30, 'dfgdfg', '', '', '', '', '', '', '', '', 8, '2025-09-20 20:15:13', 1, '1', ''),
(42, 30, 'asefds', '', '', '', '', '', '', '', '', 6, '2025-09-20 20:15:19', 1, '1', ''),
(43, 30, 'sedfsge', '', '', '', '', '', '', '', '', 2, '2025-09-20 20:15:25', 1, '1', ''),
(44, 30, 'dfssdf', '', '', '', '', '', '', '', '', 6, '2025-09-20 20:15:31', 1, '1', ''),
(45, 30, 'gfhfghj', '', '', '', '', '', '', '', '', 10, '2025-09-20 20:15:40', 1, '1', ''),
(46, 30, 'fgjhgyfyhrt', '', '', '', '', '', '', '', '', 8, '2025-09-20 20:15:47', 1, '1', '');

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
(21, 35, '../assets/uploads/papas/img_68cb9604bac851.14177486.jpeg'),
(22, 34, '../assets/uploads/papas11/img_68cf556e7065d6.02074919.jpg'),
(23, 34, '../assets/uploads/papas11/img_68cf556e7555d7.19138930.png'),
(24, 34, '../assets/uploads/papas11/img_68cf556e75d0f4.72634931.png'),
(25, 34, '../assets/uploads/papas11/img_68cf556e764802.07208631.jpg');

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
(23, 'yolocal2@gmail.com', '$2y$10$gZOGWNaRtC55PmJ.XrOd7.zGT6OxsSs3dhFZ1CJILTCUOVfd3ns7i', 'admin', 'Cuenta', 'Secundaria', 'Administracion'),
(29, 'YoLocal@gmail.com', '$2y$10$kpjvqEqCZyJHqKDnANfCeunFc6kRqcKANYAHkOritHhYoMWNgF9gK', 'admin', 'Marco', 'Yo', 'Local'),
(30, 'acuariosanmartin@outlook.com', '$2y$10$gg5J2vBrLFp0dmoK2fWxCuv9rGU/NeodQKKYE6/ECa30Ei7NT6C5e', 'negocio', 'Mary Carmen', 'Monge', 'Uriostegui');

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
  MODIFY `ID_Categoria` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `ID_Horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `negocios`
--
ALTER TABLE `negocios`
  MODIFY `ID_Negocio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=272;

--
-- AUTO_INCREMENT de la tabla `negocio_imagenes`
--
ALTER TABLE `negocio_imagenes`
  MODIFY `ID_Imagen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `promociones`
--
ALTER TABLE `promociones`
  MODIFY `ID_Promocion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `ID_Usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
