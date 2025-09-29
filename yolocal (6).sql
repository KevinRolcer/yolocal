-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
<<<<<<< HEAD
-- Tiempo de generación: 26-09-2025 a las 06:25:45
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30
=======
-- Tiempo de generación: 23-09-2025 a las 02:11:33
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12
>>>>>>> main

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

<<<<<<< HEAD
=======
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

>>>>>>> main
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
<<<<<<< HEAD
  `ID_Categoria` int(5) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `Relevancia` enum('1','2','3') NOT NULL DEFAULT '1',
=======
  `GoogleMaps` varchar(500) NOT NULL,
  `ID_Categoria` int(5) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `estado` tinyint(4) DEFAULT 1,
  `Relevancia` enum('1','2','3','') NOT NULL DEFAULT '1',
>>>>>>> main
  `Rutaicono` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `negocios`
--

<<<<<<< HEAD
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
(46, 30, 'fgjhgyfyhrt', '', '', '', '', '', '', '', '', 8, '2025-09-20 20:15:47', 1, '1', ''),
(272, 30, 'Taquería El Buen Pastor', 'Los mejores tacos al pastor de la región.', 'Calle Hidalgo 123, Centro', '2221234567', 'pastor@email.com', 'https://elbuenpastor.com', 'https://facebook.com/pastorbueno', 'https://instagram.com/pastorbueno', '', 1, '2025-01-15 10:30:00', 1, '3', '/uploads/iconos/tacos.png'),
(273, 29, 'Papelería El Estudiante', 'Todo para tus tareas y trabajos escolares.', 'Avenida Reforma 45, Col. Estudiantil', '2227654321', 'papeleria@email.com', 'https://papeleriaestudiante.com', 'https://facebook.com/estudiantepapel', '', '', 6, '2025-02-20 09:00:00', 1, '2', '/uploads/iconos/papeleria.png'),
(274, 23, 'Tlapalería La Tuerca Floja', 'Herramientas, tornillos, pintura y todo lo que necesitas.', 'Calle 5 de Mayo 88, Barrio de San Miguel', '2225556677', 'tlapaleria@email.com', '', 'https://facebook.com/latuercafloja', '', '', 9, '2025-03-10 11:15:00', 1, '1', '/uploads/iconos/tlapaleria.png'),
(275, 30, 'Abarrotes Doña Pelos', 'La tienda de la esquina con todo lo que buscas.', 'Esquina Morelos y Juárez, Centro', '2228889900', 'abarrotes@email.com', '', '', '', '', 10, '2024-11-05 18:00:00', 1, '3', '/uploads/iconos/abarrotes.png'),
(276, 29, 'Estética Unisex Glamour', 'Cortes de cabello, tintes, peinados y maquillaje.', 'Plaza Principal Local 5, Centro', '2221112233', 'glamour@email.com', 'https://glamourunisex.com', 'https://facebook.com/glamourestetica', 'https://instagram.com/glamourestetica', 'https://tiktok.com/@glamour', 6, '2025-04-01 12:45:00', 1, '2', '/uploads/iconos/estetica.png'),
(277, 23, 'Refaccionaria El Pistón', 'Partes y refacciones para todas las marcas de autos.', 'Carretera Federal Km. 82', '2223334455', 'refaccionaria@email.com', '', 'https://facebook.com/elpistonref', '', '', 8, '2025-05-22 14:00:00', 1, '1', '/uploads/iconos/refaccionaria.png'),
(278, 30, 'Cocina Económica Doña Lucha', 'El auténtico sazón casero. Menú del día variado.', 'Calle Zapata 21, Col. Obrera', '2229998877', 'lucha@email.com', '', 'https://facebook.com/cocinadonalucha', '', '', 1, '2025-06-30 08:30:00', 1, '3', '/uploads/iconos/cocina.png'),
(279, 29, 'Lavandería Burbujas', 'Lavado y secado por kilo. Servicio de planchaduría.', 'Avenida Independencia 150', '2224567890', 'burbujas@email.com', '', '', 'https://instagram.com/lavaburbujas', '', 6, '2025-07-11 10:00:00', 1, '1', '/uploads/iconos/lavanderia.png'),
(280, 23, 'Ferretería San José', 'La herramienta y material más resistente.', 'Mercado Municipal Local 12, Centro', '2220123456', 'ferreteria.sj@email.com', '', '', '', '', 2, '2024-12-18 07:00:00', 1, '2', '/uploads/iconos/fruteria.png'),
(281, 30, 'Carnicería El Torito', 'Los mejores cortes de res, puerco y pollo.', 'Calle Aldama 33, Barrio de la Luz', '2227890123', 'torito@email.com', '', 'https://facebook.com/eltoritocarnes', '', '', 10, '2025-08-02 09:20:00', 1, '2', '/uploads/iconos/carniceria.png'),
(282, 29, 'Panadería El Trigo de Oro', 'Pan dulce y bolillos recién horneados.', 'Calle Aquiles Serdán 5, Centro', '2228765432', 'trigo@email.com', '', '', 'https://instagram.com/trigodeoro', '', 1, '2025-01-25 06:45:00', 1, '3', '/uploads/iconos/panaderia.png'),
(283, 23, 'Consultorio Dental Sonrisa Feliz', 'Limpiezas, resinas, blanqueamientos y más.', 'Avenida Juárez 500, Interior 3', '2226549870', 'sonrisa@email.com', 'https://sonrisafeliz.dental', 'https://facebook.com/sonrisafelizdental', '', '', 6, '2025-09-01 16:00:00', 1, '2', '/uploads/iconos/dental.png'),
(284, 30, 'Cerrajería El Llavero', 'Duplicado de llaves y aperturas de emergencia.', 'Calle del Candado 13', '2221239876', 'llavero@email.com', '', '', '', '', 8, '2025-02-14 11:55:00', 1, '1', '/uploads/iconos/cerrajeria.png'),
(285, 29, 'Veterinaria Mi Mascota', 'Consultas, vacunas, estética canina y alimentos.', 'Boulevard del Perro Feliz 4, Col. Animalitos', '2225432109', 'mascota@email.com', 'https://mimascotavet.com', 'https://facebook.com/mimascotavet', 'https://instagram.com/mimascotavet', '', 6, '2025-03-19 13:10:00', 1, '3', '/uploads/iconos/veterinaria.png'),
(286, 23, 'Pinturas El Clavo de Oro', 'Todo para la construcción y el hogar.', 'Calle del Martillo 22, Col. Industrial', '2228907654', 'pinturas@email.com', '', 'https://facebook.com/clavodeoro', '', '', 9, '2024-10-10 08:00:00', 1, '1', '/uploads/iconos/ferreteria.png'),
(287, 30, 'Pollería Hermanos Sánchez', 'Pollo fresco de granja, entero o en piezas.', 'Mercado Municipal Local 25, Centro', '2227651234', 'pollos@email.com', '', '', '', '', 10, '2025-04-28 07:30:00', 1, '2', '/uploads/iconos/polleria.png'),
(288, 29, 'Nevería La Michoacana', 'Aguas frescas, paletas de hielo y helados.', 'Frente al Zócalo, Portal Juárez 1', '2226543210', 'michoacana@email.com', '', 'https://facebook.com/michoacanamoyotzingo', 'https://instagram.com/michoacanamoyotzingo', 'https://tiktok.com/@lamichoacana', 1, '2025-05-15 12:00:00', 1, '3', '/uploads/iconos/neveria.png'),
(289, 23, 'Cybercafé @Net', 'Renta de computadoras e internet de alta velocidad.', 'Calle Tecnológica 101, Col. Universitaria', '2225438765', 'cyber@email.com', '', '', '', '', 8, '2025-06-05 10:40:00', 1, '1', '/uploads/iconos/cyber.png'),
(290, 30, 'Pizzería Donatello', 'Las mejores pizzas a la leña, con ingredientes frescos.', 'Avenida del Sabor 456', '2224327659', 'pizza@email.com', 'https://pizzeriadonatello.com', 'https://facebook.com/donatellopizza', 'https://instagram.com/donatellopizza', '', 1, '2025-07-21 19:30:00', 1, '2', '/uploads/iconos/pizza.png'),
(291, 29, 'Zapatería El Girasol', 'Zapatos para toda la familia.', 'Calle de las Flores 8, Col. Jardín', '2223216547', 'zapatos@email.com', '', 'https://facebook.com/floreriagirasol', 'https://instagram.com/floreriagirasol', '', 10, '2025-08-18 09:00:00', 1, '2', '/uploads/iconos/floreria.png'),
(292, 23, 'Gimnasio Acero', 'Pesas, cardio, y clases grupales. Transforma tu cuerpo.', 'Calle del Músculo 7, Col. Deportiva', '2222109876', 'gym@email.com', 'https://gymacero.com', 'https://facebook.com/gymacero', 'https://instagram.com/gymacero', 'https://tiktok.com/@gymacero', 6, '2024-09-01 06:00:00', 1, '3', '/uploads/iconos/gym.png'),
(293, 30, 'Taller Mecánico \"El Gato\"', 'Servicio de afinación, frenos y suspensión.', 'Calle de la Llave Inglesa 19', '2221098765', 'mecanico@email.com', '', 'https://facebook.com/tallerelgato', '', '', 8, '2025-09-12 09:00:00', 1, '1', '/uploads/iconos/mecanico.png'),
(294, 23, 'Cafetería El Descanso', 'El mejor café de grano de la región y postres caseros.', 'Callejón de la Calma 5, Centro', '2223124567', 'descanso@email.com', '', 'https://facebook.com/cafedescanso', '', '', 1, '2024-01-20 08:00:00', 1, '2', '/uploads/iconos/default.png'),
(295, 29, 'Pinturas El Pincelazo', 'Igualación de colores por computadora y todo para el pintor.', 'Avenida del Color 120, San Miguel', '2224235678', 'pincelazo@email.com', 'https://elpincelazo.com', '', 'https://instagram.com/elpincelazo', '', 9, '2024-01-22 10:15:00', 1, '1', '/uploads/iconos/default.png'),
(296, 30, 'Zapatería El Buen Paso', 'Calzado de piel para dama y caballero. Comodidad y estilo.', 'Plaza de la Constitución 14', '2225346789', 'buenpaso@email.com', '', 'https://facebook.com/zapatosebp', '', '', 10, '2024-01-25 11:30:00', 1, '3', '/uploads/iconos/default.png'),
(297, 23, 'Eléctrica El Chispazo', 'Material eléctrico para instalaciones residenciales y comerciales.', 'Calle de la Luz 78', '2226457890', 'chispazo@email.com', '', '', '', '', 8, '2024-01-28 09:00:00', 1, '1', '/uploads/iconos/default.png'),
(298, 29, 'Papelería El Compás', 'Listas de útiles escolares, monografías y enmicados.', 'Rincón del Estudiante 33', '2227568901', 'compas@email.com', '', '', '', '', 6, '2024-02-01 08:45:00', 1, '2', '/uploads/iconos/default.png'),
(299, 30, 'Ferretería La Viga', 'Todo para el constructor. Cemento, varilla y herramientas.', 'Camino Real 450', '2228679012', 'laviga@email.com', 'https://ferreterialaviga.com', 'https://facebook.com/lavigaferre', '', '', 2, '2024-02-05 07:30:00', 1, '3', '/uploads/iconos/default.png'),
(300, 23, 'Café Aromas de la Tierra', 'Café orgánico y métodos de extracción artesanales.', 'Pasaje del Sabor 8', '2229780123', 'aromas@email.com', '', '', 'https://instagram.com/aromasdelatierra', '', 1, '2024-02-10 09:00:00', 1, '3', '/uploads/iconos/default.png'),
(301, 29, 'Zapatería Infantil Pasitos', 'El mejor calzado para los pequeños. Durabilidad y confort.', 'Calle de los Niños 16', '2220891234', 'pasitos@email.com', '', '', 'https://instagram.com/pasitoskids', '', 10, '2024-02-14 10:00:00', 1, '2', '/uploads/iconos/default.png'),
(302, 30, 'Impermeabilizantes \"El Muro Seco\"', 'Protege tu hogar de la humedad. Venta y aplicación.', 'Calle de la Gota 99', '2221902345', 'muroseco@email.com', '', 'https://facebook.com/muroseco', '', '', 9, '2024-02-18 11:00:00', 1, '1', '/uploads/iconos/default.png'),
(303, 23, 'Ferretería El Tornillo de Oro', 'La más amplia variedad en tornillería y fijación.', 'Avenida de la Industria 210', '2222013456', 'tornillo@email.com', '', '', '', '', 2, '2024-02-22 08:20:00', 1, '2', '/uploads/iconos/default.png'),
(304, 29, 'El Rincón del Café', 'Un espacio para relajarte con una buena taza de café.', 'Plazuela del Recuerdo 2', '2223124567', 'rinconcafe@email.com', '', '', 'https://instagram.com/rincon.del.cafe', '', 1, '2024-02-28 15:00:00', 1, '3', '/uploads/iconos/default.png'),
(305, 30, 'Suministros Eléctricos \"El Foco\"', 'Focos, cables, interruptores y todo en iluminación LED.', 'Boulevard de la Energía 500', '2224235678', 'elfoco@email.com', 'https://elfoco.com', '', '', '', 8, '2024-03-03 10:30:00', 1, '2', '/uploads/iconos/default.png'),
(306, 23, 'Copiadoras y Papelería \"La Tinta\"', 'Servicio de copiado de alto volumen y venta de consumibles.', 'Calle de la Impresión 44', '2225346789', 'latinta@email.com', '', 'https://facebook.com/latintacopias', '', '', 6, '2024-03-07 09:10:00', 1, '1', '/uploads/iconos/default.png'),
(307, 29, 'Zapatería Confort \"El Descanso\"', 'Zapatos cómodos para largas jornadas de trabajo.', 'Avenida del Trabajo 88', '2226457890', 'descanso@email.com', '', '', '', '', 10, '2024-03-12 11:45:00', 1, '3', '/uploads/iconos/default.png'),
(308, 30, 'Pinturas \"El Rodillo Veloz\"', 'Asesoría profesional para renovar tus espacios.', 'Calle de la Fachada 123', '2227568901', 'rodillo@email.com', '', '', 'https://instagram.com/rodilloveloz', '', 9, '2024-03-16 09:00:00', 1, '2', '/uploads/iconos/default.png'),
(309, 23, 'Ferretería \"La Carretilla\"', 'Herramienta de jardinería y para la construcción.', 'Camino al Campo 12', '2228679012', 'carretilla@email.com', '', 'https://facebook.com/ferrecarretilla', '', '', 2, '2024-03-20 08:00:00', 1, '1', '/uploads/iconos/default.png'),
(310, 29, 'Café \"El Madrugador\"', 'Abierto desde las 6 AM con café recién hecho y pan.', 'Esquina del Gallo 1', '2229780123', 'madrugador@email.com', '', '', '', '', 1, '2024-03-25 06:00:00', 1, '3', '/uploads/iconos/default.png'),
(311, 30, 'Botas de Trabajo \"El Roble\"', 'Calzado industrial con casquillo de acero. Seguridad y durabilidad.', 'Calle de la Fábrica 50', '2220891234', 'elroble@email.com', '', '', '', '', 10, '2024-03-29 10:00:00', 1, '2', '/uploads/iconos/default.png'),
(312, 23, 'Cables y Conectores \"El Enlace\"', 'Todo tipo de cableado para tus proyectos eléctricos.', 'Privada del Circuito 9', '2221902345', 'enlace@email.com', '', '', '', '', 8, '2024-04-02 09:30:00', 1, '1', '/uploads/iconos/default.png'),
(313, 29, 'Artículos de Oficina \"El Clip\"', 'Todo para organizar tu espacio de trabajo.', 'Avenida del Escritorio 110', '2222013456', 'elclip@email.com', 'https://elclipoficina.com', 'https://facebook.com/elclip', '', '', 6, '2024-04-06 10:20:00', 1, '2', '/uploads/iconos/default.png'),
(314, 30, 'Cafetería \"Grano Molido\"', 'Selección de granos de Veracruz y Chiapas molidos al momento.', 'Calle del Sabor 72', '2223124567', 'granomolido@email.com', '', '', '', 'https://tiktok.com/@granomolido', 1, '2024-04-11 08:50:00', 1, '3', '/uploads/iconos/default.png'),
(315, 23, 'Zapatería \"La Elegancia\"', 'Calzado de vestir y para eventos especiales.', 'Paseo de la Gala 40', '2224235678', 'elegancia@email.com', '', 'https://facebook.com/laeleganciazapatos', 'https://instagram.com/laelegancia', '', 10, '2024-04-15 12:00:00', 1, '2', '/uploads/iconos/default.png'),
(316, 29, 'Barnices y Solventes \"La Gota\"', 'Productos para el cuidado y acabado de la madera.', 'Calle del Carpintero 18', '2225346789', 'lagota@email.com', '', '', '', '', 9, '2024-04-19 09:40:00', 1, '1', '/uploads/iconos/default.png'),
(317, 30, 'Herrajes y Chapas \"El Cerrojo\"', 'Todo para la seguridad de tus puertas y ventanas.', 'Callejón de la Seguridad 3', '2226457890', 'cerrojo@email.com', '', '', '', '', 2, '2024-04-23 10:00:00', 1, '2', '/uploads/iconos/default.png'),
(318, 23, 'Café Internet \"Navegantes\"', 'Espacio tranquilo para trabajar o estudiar. Velocidad garantizada.', 'Avenida de la Conexión 300', '2227568901', 'navegantes@email.com', '', '', '', '', 8, '2024-04-28 09:00:00', 1, '1', '/uploads/iconos/default.png'),
(319, 29, 'Papelería y Mercería \"El Hilo\"', 'Estambres, hilos, y todo para tus manualidades.', 'Rincón de la Creatividad 21', '2228679012', 'elhilo@email.com', '', 'https://facebook.com/elhilomerceria', '', '', 6, '2024-05-02 11:15:00', 1, '2', '/uploads/iconos/default.png'),
(320, 30, 'Zapatería Deportiva \"El Corredor\"', 'Tenis para correr, futbol y entrenamiento de las mejores marcas.', 'Calle de la Pista 55', '2229780123', 'elcorredor@email.com', 'https://elcorredor.com', '', 'https://instagram.com/elcorredor', '', 10, '2024-05-07 10:30:00', 1, '3', '/uploads/iconos/default.png'),
(321, 23, 'Ferretería \"El Martillo\"', 'Herramienta manual y eléctrica para profesionales.', 'Calle del Esfuerzo 91', '2220891234', 'elmartillo@email.com', '', '', '', '', 2, '2024-05-12 08:00:00', 1, '2', '/uploads/iconos/default.png'),
(322, 29, 'Cafetería y Postres \"Dulce Aroma\"', 'Frappés, pasteles y un ambiente acogedor.', 'Plazuela del Antojo 11', '2221902345', 'dulcearoma@email.com', '', '', 'https://instagram.com/dulce.aroma.cafe', 'https://tiktok.com/@dulcearoma', 1, '2024-05-17 14:00:00', 1, '3', '/uploads/iconos/default.png'),
(323, 30, 'Impermeabilizantes y Pinturas \"El Sello\"', 'Soluciones integrales contra goteras y humedad.', 'Avenida de la Protección 150', '2222013456', 'elsello@email.com', '', '', '', '', 9, '2024-05-21 09:25:00', 1, '1', '/uploads/iconos/default.png'),
(324, 23, 'Material Eléctrico \"El Voltaje\"', 'Transformadores, centros de carga y más.', 'Parque Industrial Lote 14', '2223124567', 'voltaje@email.com', '', '', '', '', 8, '2024-05-26 09:00:00', 1, '2', '/uploads/iconos/default.png'),
(325, 29, 'Mochilas y Útiles \"El Regreso\"', 'Todo para el regreso a clases. Amplio surtido.', 'Feria Escolar Puesto 5', '2224235678', 'elregreso@email.com', '', '', '', '', 6, '2024-05-31 10:00:00', 1, '1', '/uploads/iconos/default.png'),
(326, 30, 'Sandalias y Huaraches \"La Playita\"', 'El calzado más fresco para la temporada de calor.', 'Calle del Sol 24', '2225346789', 'playita@email.com', '', '', '', '', 10, '2024-06-04 11:20:00', 1, '2', '/uploads/iconos/default.png'),
(327, 23, 'Ferretería y Plomería \"El Tubo\"', 'Conexiones, tuberías y todo para el plomero.', 'Calle del Agua 48', '2226457890', 'eltubo@email.com', '', 'https://facebook.com/eltuboplomeria', '', '', 2, '2024-06-09 08:40:00', 1, '3', '/uploads/iconos/default.png'),
(328, 29, 'Café Frío \"El Iglú\"', 'Especialistas en cold brew y bebidas frías a base de café.', 'Paseo de la Brisa 7', '2227568901', 'iglu@email.com', '', '', 'https://instagram.com/iglu.cafe', '', 1, '2024-06-13 13:00:00', 1, '2', '/uploads/iconos/default.png'),
(329, 30, 'Aerosoles y Esmaltes \"El Spray\"', 'Pintura en aerosol de todos los colores y acabados.', 'Calle del Graffiti 81', '2228679012', 'spray@email.com', '', '', '', 'https://tiktok.com/@elspray', 9, '2024-06-18 10:50:00', 1, '1', '/uploads/iconos/default.png'),
(330, 23, 'Lámparas y Candiles \"La Luz\"', 'Iluminación decorativa para interior y exterior.', 'Avenida del Diseño 190', '2229780123', 'laluz@email.com', 'https://laluziluminacion.com', '', 'https://instagram.com/laluz.deco', '', 8, '2024-06-22 11:00:00', 1, '3', '/uploads/iconos/default.png'),
(331, 29, 'Papelería Técnica \"El Escalímetro\"', 'Artículos para estudiantes de arquitectura y diseño.', 'Junto a la Universidad de Diseño', '2220891234', 'escalimetro@email.com', '', '', '', '', 6, '2024-06-27 09:00:00', 1, '2', '/uploads/iconos/default.png'),
(332, 30, 'Tenis Urbanos \"La Suela\"', 'Las marcas de sneakers que están en tendencia.', 'Callejón de la Moda 13', '2221902345', 'lasuela@email.com', '', '', 'https://instagram.com/lasuela.sneakers', 'https://tiktok.com/@lasuela', 10, '2024-07-01 12:30:00', 1, '3', '/uploads/iconos/default.png'),
(333, 23, 'Ferretería \"La Esquina\"', 'La solución rápida para tus emergencias caseras.', 'Esquina de la Herramienta 1', '2222013456', 'laesquina@email.com', '', '', '', '', 2, '2024-07-06 08:00:00', 1, '3', '/uploads/iconos/default.png'),
(334, 29, 'Café de Olla \"El Jarrito\"', 'El sabor tradicional del café de olla con piloncillo y canela.', 'Portal de la Abuela 4', '2223124567', 'jarrito@email.com', '', 'https://facebook.com/eljarritocafe', '', '', 1, '2024-07-10 07:30:00', 1, '2', '/uploads/iconos/default.png'),
(335, 30, 'Pinturas Vinílicas \"El Muro\"', 'La mejor calidad en pintura para tus paredes.', 'Avenida de la brocha 43', '2224235678', 'elmuro@email.com', '', '', '', '', 9, '2024-07-15 09:15:00', 1, '1', '/uploads/iconos/default.png'),
(336, 23, 'Eléctrica Industrial \"El Contactor\"', 'Material para instalaciones de alta y baja tensión.', 'Calle de la Potencia 77', '2225346789', 'contactor@email.com', '', '', '', '', 8, '2024-07-19 08:30:00', 1, '2', '/uploads/iconos/default.png'),
(337, 29, 'Cuadernos y Libretas \"El Apunte\"', 'Extenso surtido en cuadernos para todas las materias.', 'Calle del Saber 29', '2226457890', 'apunte@email.com', '', '', '', '', 6, '2024-07-24 10:00:00', 1, '1', '/uploads/iconos/default.png'),
(338, 30, 'Zapatería \"El Tacón Dorado\"', 'Zapatillas y calzado de fiesta para lucir espectacular.', 'Avenida del Glamour 101', '2227568901', 'tacon@email.com', '', '', 'https://instagram.com/tacondorado', '', 10, '2024-07-29 11:40:00', 1, '3', '/uploads/iconos/default.png'),
(339, 23, 'Tornillos y Birlos \"El Roscado\"', 'Especialistas en tornillería milimétrica y estándar.', 'Calle de la Precisión 15', '2228679012', 'roscado@email.com', '', '', '', '', 2, '2024-08-02 09:00:00', 1, '1', '/uploads/iconos/default.png'),
(340, 29, 'Café y Libros \"El Lector\"', 'Disfruta de un buen libro acompañado de un excelente café.', 'Rincón de la Cultura 1', '2229780123', 'lector@email.com', 'https://ellectorcafe.com', 'https://facebook.com/ellectorcafe', '', '', 1, '2024-08-07 13:20:00', 1, '2', '/uploads/iconos/default.png'),
(341, 30, 'Igualado de Pintura \"El Matiz\"', 'Trae tu muestra y te igualamos el color exacto.', 'Avenida de la Paleta 60', '2220891234', 'matiz@email.com', '', '', '', '', 9, '2024-08-11 10:10:00', 1, '2', '/uploads/iconos/default.png'),
(342, 23, 'Iluminación \"El Reflector\"', 'Reflectores y lámparas para exteriores y jardines.', 'Calle de la Farola 34', '2221902345', 'reflector@email.com', '', '', '', '', 8, '2024-08-16 11:00:00', 1, '1', '/uploads/iconos/default.png'),
(343, 29, 'Papelería \"El Papiro\"', 'Cartulinas, fomi y material didáctico.', 'Calle de las Manualidades 41', '2222013456', 'papiro@email.com', '', '', '', '', 6, '2024-08-21 09:30:00', 1, '2', '/uploads/iconos/default.png'),
(344, 30, 'Outlet de Calzado \"El Remate\"', 'Zapatos de marca a precios de liquidación.', 'Bodega de Ofertas 1, Salida a la autopista', '2223124567', 'remate@email.com', '', 'https://facebook.com/rematecalzado', '', '', 10, '2024-08-26 10:00:00', 1, '1', '/uploads/iconos/default.png'),
(345, 23, 'Plomería y Gas \"El Sifón\"', 'Calentadores, estufas y todo para tu instalación de gas.', 'Calle de la Flama 19', '2224235678', 'sifon@email.com', '', '', '', '', 2, '2024-08-31 08:50:00', 1, '2', '/uploads/iconos/default.png'),
(346, 29, 'Cafetería Express \"El Vaso\"', 'Café para llevar, rápido y delicioso.', 'Esquina de la Prisa 2', '2225346789', 'vaso@email.com', '', '', '', 'https://tiktok.com/@cafe.elvaso', 1, '2024-09-04 07:00:00', 1, '3', '/uploads/iconos/default.png'),
(347, 30, 'Brochas y Accesorios \"La Cerda\"', 'Todo tipo de brochas, rodillos y accesorios para pintar.', 'Pasadizo del Pintor 6', '2226457890', 'lacerda@email.com', '', '', '', '', 9, '2024-09-09 09:00:00', 1, '1', '/uploads/iconos/default.png'),
(348, 23, 'Fusibles y Pastillas \"El Cortocircuito\"', 'Protege tus aparatos con material eléctrico de calidad.', 'Avenida de la Seguridad 15', '2227568901', 'cortocircuito@email.com', '', '', '', '', 8, '2024-09-13 10:45:00', 1, '2', '/uploads/iconos/default.png'),
(349, 29, 'Regalos y Envolturas \"El Moño\"', 'El toque final para tus regalos. Papel y cajas de todo tipo.', 'Calle del Obsequio 30', '2228679012', 'elmono@email.com', '', '', 'https://instagram.com/elmono.regalos', '', 6, '2024-09-18 11:30:00', 1, '2', '/uploads/iconos/default.png'),
(350, 30, 'Reparación de Calzado \"El Maestro\"', 'Cambio de suelas, tapas y costura. Dejamos tus zapatos como nuevos.', 'Portal de los Zapateros 3', '2229780123', 'maestro@email.com', '', '', '', '', 10, '2024-09-22 09:20:00', 1, '1', '/uploads/iconos/default.png'),
(351, 23, 'Ferretería \"El Taladro\"', 'La mejor selección de taladros y rotomartillos.', 'Calle de la Perforación 22', '2220891234', 'taladro@email.com', '', 'https://facebook.com/ferretaladro', '', '', 2, '2024-09-27 08:15:00', 1, '3', '/uploads/iconos/default.png'),
(352, 29, 'Cafetería \"La Tertulia\"', 'El lugar perfecto para la plática y el buen café.', 'Rincón de los Amigos 5', '2221902345', 'tertulia@email.com', '', '', 'https://instagram.com/latertulia.cafe', '', 1, '2024-10-01 16:00:00', 1, '2', '/uploads/iconos/default.png'),
(353, 30, 'Pintura Automotriz \"El Bicentenario\"', 'Igualado de color por código para todas las marcas de autos.', 'Avenida del Taller 200', '2222013456', 'bicentenario@email.com', '', '', '', '', 9, '2024-10-06 09:00:00', 1, '1', '/uploads/iconos/default.png'),
(354, 23, 'Cajas y Registros \"La Conexión\"', 'Material eléctrico para obra negra.', 'Calle del Albañil 51', '2223124567', 'conexion@email.com', '', '', '', '', 8, '2024-10-10 10:00:00', 1, '2', '/uploads/iconos/default.png'),
(355, 29, 'Copias \"El Clon\"', 'Servicio de fotocopiado rápido y económico. Blanco y negro y a color.', 'Frente a la Presidencia Municipal', '2224235678', 'elclon@email.com', '', '', '', '', 6, '2024-10-15 09:50:00', 1, '1', '/uploads/iconos/default.png'),
(356, 30, 'Calzado Escolar \"El Recreo\"', 'Zapatos y tenis para el uniforme escolar. Todas las tallas.', 'Avenida del Estudiante 99', '2225346789', 'recreo@email.com', '', '', '', '', 10, '2024-10-20 10:20:00', 1, '2', '/uploads/iconos/default.png'),
(357, 23, 'Ferretería \"El Ancla\"', 'Anclas, taquetes y todo para fijar en cualquier superficie.', 'Muro de Carga 18', '2226457890', 'elancla@email.com', '', '', '', '', 2, '2024-10-25 08:30:00', 1, '1', '/uploads/iconos/default.png'),
(358, 29, 'Churros y Chocolate \"El Azúcar\"', 'Churros recién hechos acompañados de chocolate caliente.', 'Plaza Principal, Kiosko 2', '2227568901', 'azucar@email.com', '', '', 'https://instagram.com/churros.elazucar', 'https://tiktok.com/@churros.azucar', 1, '2024-10-30 18:00:00', 1, '3', '/uploads/iconos/default.png'),
(359, 30, 'Impermeabilizantes \"Sin Goteras\"', 'Garantía por escrito en nuestros trabajos de impermeabilización.', 'Techo Seguro 21', '2228679012', 'singoteras@email.com', 'https://singoteras.com', 'https://facebook.com/singoteras', '', '', 9, '2024-11-03 09:00:00', 1, '2', '/uploads/iconos/default.png'),
(360, 23, 'Herramienta Eléctrica \"La Chispa\"', 'Venta y reparación de herramienta eléctrica de todas las marcas.', 'Avenida del Motor 140', '2229780123', 'lachispa@email.com', '', '', '', '', 8, '2024-11-08 10:00:00', 1, '3', '/uploads/iconos/default.png'),
(361, 29, 'Forrado de Libros \"El Protector\"', 'Forramos tus libros y cuadernos con plástico contact.', 'Calle de la Biblioteca 3', '2220891234', 'protector@email.com', '', '', '', '', 6, '2024-11-12 11:10:00', 1, '1', '/uploads/iconos/default.png'),
(362, 30, 'Suelas y Tacones \"El Remiendo\"', 'Reparación profesional de todo tipo de calzado.', 'Rincón del Zapatero 1', '2221902345', 'remiendo@email.com', '', '', '', '', 10, '2024-11-17 09:45:00', 1, '1', '/uploads/iconos/default.png'),
(363, 23, 'Ferretería \"El Nivel\"', 'Todo para el albañil y el carpintero.', 'Calle de la Plomada 66', '2222013456', 'elnivel@email.com', '', '', '', '', 2, '2024-11-21 08:00:00', 1, '2', '/uploads/iconos/default.png'),
(364, 29, 'Frappés y Smoothies \"El Frutal\"', 'Bebidas refrescantes hechas con fruta 100% natural.', 'Paseo de la Juventud 19', '2223124567', 'frutal@email.com', '', '', 'https://instagram.com/elfrutal.frappes', '', 1, '2024-11-26 12:00:00', 1, '2', '/uploads/iconos/default.png'),
(365, 30, 'Pinturas y Esmaltes \"La Brocha Gorda\"', 'La mejor calidad y rendimiento en cada cubeta.', 'Avenida del Acabado 350', '2224235678', 'brochagorda@email.com', '', 'https://facebook.com/labrochagorda', '', '', 9, '2024-11-30 09:00:00', 1, '3', '/uploads/iconos/default.png'),
(366, 23, 'Apagadores y Contactos \"El Switch\"', 'Diseños modernos y clásicos para tus instalaciones.', 'Pared del Enchufe 8', '2225346789', 'elswitch@email.com', '', '', '', '', 8, '2024-12-05 11:25:00', 1, '1', '');
=======
INSERT INTO `negocios` (`ID_Negocio`, `ID_Usuario`, `nombre_negocio`, `DescripcionN`, `Direccion`, `Telefono`, `CorreoN`, `SitioWeb`, `Facebook`, `Instagram`, `TikTok`, `GoogleMaps`, `ID_Categoria`, `fecha_registro`, `estado`, `Relevancia`, `Rutaicono`) VALUES
(4, 28, 'La Patona', 'Bar-Dedicado a la ofrecer la mejor calidad en platillos, mientras disfrutas de las mejores bebidas de la región', 'Enrique Segoviano #34', '2345678909', 'lapatona@gmail.com', 'dasdsa', 'dsadsa', 'dsadas', 'sii', 'https://maps.app.goo.gl/hftktLG2WoK2CsuJ8', 1, '2025-09-10 22:21:23', 0, '', ''),
(5, 23, 'El patrón', 'Disfruta de nuestros deliciosos Bufet Desayuno, Bufet Comida, Bufet Antojo Mexicano', 'Libertad Nte 605 frente al DIF municipal de San Martín Texmelucan.', '2481557389', 'elpatron@gmail.com', 'https://listado.mercadolibre.com.mx/carros-en-venta-en-puebla', 'https://www.restaurants10.com/MX/San-Mart%C3%ADn-Texmelucan-de-Labastida/100470161919017/El-Patr%C3%', 'https://www.restaurants10.com/MX/San-Mart%C3%ADn-Texmelucan-de-Labastida/100470161919017/El-Patr%C3%', '', '', 1, '2025-09-10 22:21:23', 1, '', ''),
(6, 28, 'Postres Yara', 'En Yara Gourmet elaboran todo tipo de pasteles y postres. Desde individuales, versiones tradicionales como también en sus apuestas personalizadas con originales mezclas de sabores y montajes que dejan sin aliento.', 'Av. Fuentes de Morelia, 330 58088 Morelia (Michoacán)', '2481823454', 'YaraGourmet@gmail.com', 'https://claude.ai/chat/34ebd873-3b9f-4293-b92a-d8461f730874', 'https://claude.ai/chat/34ebd873-3b9f-4293-b92a-d8461f730874', 'https://claude.ai/chat/34ebd873-3b9f-4293-b92a-d8461f730874', '', '', 1, '2025-09-10 22:21:23', 1, '3', ''),
(7, 29, 'La Rockola', '', '', '2481557890', '', '', '', '', '', '', 6, '2025-09-10 22:21:23', 0, '1', '../assets/uploads/iconos/icono_68c9fd4880b40_images.jfif'),
(14, 28, 'Zapateria Nuñez', 'Nos dedicamos a zapatos', 'Libertad Nte 605 frente al DIF municipal de San Martín Texmelucan.', '3214324324', '', '', '', '', 'http://localhost/yolocal//index.php?pag=ventas', '', 1, '2025-09-10 22:21:23', 1, '3', '../assets/uploads/iconos/icono_68c9fbff8e8c9_3-4-1.png'),
(17, 23, 'PanchaBochooo', '', '', '3214324324', 'elpatron@gmail.com', 'http://localhost/yolocal//index.php?pag=ventas', '', '', '', 'https://maps.app.goo.gl/hftktLG2WoK2CsuJ8', 1, '2025-09-10 22:21:23', 0, '3', '');
>>>>>>> main

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
<<<<<<< HEAD
(30, 366, '../assets/uploads/apagadores_y_contactos__el_switch_/img_68d19f2c586f30.73496654.jpg'),
(31, 366, '../assets/uploads/apagadores_y_contactos__el_switch_/img_68d1a1b83e10c0.75782174.png'),
(32, 366, '../assets/uploads/apagadores_y_contactos__el_switch_/img_68d1a1b83e8637.57325823.jpeg'),
(33, 366, '../assets/uploads/apagadores_y_contactos__el_switch_/img_68d1a1b83ee731.43204513.png'),
(34, 365, '../assets/uploads/pinturas_y_esmaltes__la_brocha_gorda_/img_68d1a2f9576752.23335546.png'),
(35, 365, '../assets/uploads/pinturas_y_esmaltes__la_brocha_gorda_/img_68d1a2f958ca23.85980938.jpg'),
(36, 365, '../assets/uploads/pinturas_y_esmaltes__la_brocha_gorda_/img_68d1a2f9593848.97774964.jpeg'),
(37, 365, '../assets/uploads/pinturas_y_esmaltes__la_brocha_gorda_/img_68d1a2f95991a3.46771463.png'),
(38, 364, '../assets/uploads/frapp__s_y_smoothies__el_frutal_/img_68d1a45e460842.03600367.jpg'),
(39, 364, '../assets/uploads/frapp__s_y_smoothies__el_frutal_/img_68d1a45e477a91.17168930.jpg'),
(40, 364, '../assets/uploads/frapp__s_y_smoothies__el_frutal_/img_68d1a45e47d7c3.10615559.jpg');
=======
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
>>>>>>> main

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

<<<<<<< HEAD
=======
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

>>>>>>> main
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
<<<<<<< HEAD
(23, 'yolocal2@gmail.com', '$2y$10$gZOGWNaRtC55PmJ.XrOd7.zGT6OxsSs3dhFZ1CJILTCUOVfd3ns7i', 'admin', 'Cuenta', 'Secundaria', 'Administracion'),
(29, 'YoLocal@gmail.com', '$2y$10$kpjvqEqCZyJHqKDnANfCeunFc6kRqcKANYAHkOritHhYoMWNgF9gK', 'admin', 'Marco', 'Yo', 'Local'),
(30, 'acuariosanmartin@outlook.com', '$2y$10$gg5J2vBrLFp0dmoK2fWxCuv9rGU/NeodQKKYE6/ECa30Ei7NT6C5e', 'negocio', 'Mary Carmen', 'Monge', 'Uriostegui');
=======
(23, 'saavedraaratthhjh@gmail.com', '$2y$10$gZOGWNaRtC55PmJ.XrOd7.zGT6OxsSs3dhFZ1CJILTCUOVfd3ns7i', 'negocio', 'Arath', 'Saavedra', 'Cabrera'),
(28, 'angeldomisal@gmail.com', '$2y$10$gZOGWNaRtC55PmJ.XrOd7.zGT6OxsSs3dhFZ1CJILTCUOVfd3ns7i', 'admin', 'Yara', 'Domingues', 'Salazarr'),
(29, 'kevinastrid@gmail.com', '$2y$10$kpjvqEqCZyJHqKDnANfCeunFc6kRqcKANYAHkOritHhYoMWNgF9gK', 'negocio', 'Kevis Astrid', 'Roldan', 'Cervantes'),
(30, 'saavedraaratthh@gmail.com', '$2y$10$jXSiL78oOXcgsYewxFaAEuwtEe7ViM2ZwMxxuOxCRjMk/DkhyIGGW', 'admin', 'Yara', 'Domingues', 'Salazarr');
>>>>>>> main

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
<<<<<<< HEAD
=======
-- Indices de la tabla `trabajos`
--
ALTER TABLE `trabajos`
  ADD PRIMARY KEY (`ID_Trabajo`),
  ADD KEY `ID_Negocio` (`ID_Negocio`);

--
>>>>>>> main
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
<<<<<<< HEAD
  MODIFY `ID_Categoria` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
=======
  MODIFY `ID_Categoria` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
>>>>>>> main

--
-- AUTO_INCREMENT de la tabla `horarios`
--
ALTER TABLE `horarios`
  MODIFY `ID_Horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `negocios`
--
ALTER TABLE `negocios`
<<<<<<< HEAD
  MODIFY `ID_Negocio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=367;
=======
  MODIFY `ID_Negocio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
>>>>>>> main

--
-- AUTO_INCREMENT de la tabla `negocio_imagenes`
--
ALTER TABLE `negocio_imagenes`
<<<<<<< HEAD
  MODIFY `ID_Imagen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;
=======
  MODIFY `ID_Imagen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;
>>>>>>> main

--
-- AUTO_INCREMENT de la tabla `promociones`
--
ALTER TABLE `promociones`
<<<<<<< HEAD
  MODIFY `ID_Promocion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
=======
  MODIFY `ID_Promocion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `trabajos`
--
ALTER TABLE `trabajos`
  MODIFY `ID_Trabajo` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
>>>>>>> main

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
<<<<<<< HEAD
  MODIFY `ID_Usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
=======
  MODIFY `ID_Usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
>>>>>>> main

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
<<<<<<< HEAD
=======

--
-- Filtros para la tabla `trabajos`
--
ALTER TABLE `trabajos`
  ADD CONSTRAINT `trabajos_ibfk_1` FOREIGN KEY (`ID_Negocio`) REFERENCES `negocios` (`ID_Negocio`);
>>>>>>> main
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
