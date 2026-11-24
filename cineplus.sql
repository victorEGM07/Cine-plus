-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-10-2026 a las 23:54:21
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
-- Base de datos: `cineplus`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `peliculas`
--

CREATE TABLE `peliculas` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `genero` varchar(60) NOT NULL,
  `año` int(11) NOT NULL,
  `director` varchar(100) NOT NULL,
  `calificacion` decimal(3,1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `peliculas`
--

INSERT INTO `peliculas` (`id`, `titulo`, `genero`, `año`, `director`, `calificacion`) VALUES
(1, 'Eso', 'Terror', 2001, 'ponchito', 9.8),
(3, 'Free Fire La pelicula', 'Accion y Drama', 2026, 'Manuel Alberto Ku itza', 9.5),
(4, 'Espstein y la isla perdida', 'suspeso,misterio', 2019, 'Jeffrey Espstein', 10.0),
(5, 'La proyectada', 'Amor', 2026, 'Irwing Miguel Archivor Lopez', 10.0),
(6, 'Roberto vs el jabon', 'Accion', 2000, 'Jose Roberto Hernandez', 10.0),
(7, 'manuel el moustro de hentai', 'Amor y terror', 2026, 'ponchito', 10.0),
(8, 'ponchito el ataque', 'Accion y Drama', 2017, 'pashito producciones', 9.0),
(9, 'Boku no piko', 'Accion y Drama', 2012, 'ponchito', 9.9),
(10, 'Cecyte vs Conalep', 'Accion', 2026, 'joel producciones', 10.0),
(11, 'Jeovani vs el rastrillo', 'Ciencia ficción', 2002, 'victor Dammer', 8.0),
(12, 'La remontada', 'Drama', 2021, 'Jose  PRODUCE', 9.8),
(13, 'KEVIN VS EL MEWING', 'sususpenso', 2027, 'la china', 10.0),
(14, 'manuel el niño polla', 'amor', 2006, 'ponchito', 10.0),
(15, 'tula vs el panadero', 'amor', 2009, 'joel espitia', 1.0),
(16, 'Titanic', 'amor', 2010, 'jose ignacio', 10.0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `peliculas`
--
ALTER TABLE `peliculas`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `peliculas`
--
ALTER TABLE `peliculas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
