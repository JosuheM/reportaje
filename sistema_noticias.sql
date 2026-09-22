-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-09-2026 a las 18:59:53
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
-- Base de datos: `sistema_noticias`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `autor`
--

CREATE TABLE `autor` (
  `autor_id` int(11) NOT NULL,
  `autor_nombre` varchar(100) NOT NULL,
  `autor_apellido_paterno` varchar(100) NOT NULL,
  `autor_apellido_materno` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `boletin_ntep`
--

CREATE TABLE `boletin_ntep` (
  `boletin_ntep_id` int(11) NOT NULL,
  `boletin_ntep_numero` varchar(50) DEFAULT NULL,
  `boletin_ntep_resumen` text DEFAULT NULL,
  `boletin_ntep_fecha_publicacion` datetime DEFAULT NULL,
  `imagen_id` int(11) DEFAULT NULL,
  `documento_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento`
--

CREATE TABLE `documento` (
  `documento_id` int(11) NOT NULL,
  `documento_nombre_archivo` varchar(255) NOT NULL,
  `documento_extension` varchar(10) NOT NULL,
  `documento_ruta` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `usuario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagen`
--

CREATE TABLE `imagen` (
  `imagen_id` int(11) NOT NULL,
  `imagen_nombre_archivo` varchar(255) NOT NULL,
  `imagen_extension` varchar(10) NOT NULL,
  `imagen_ruta` varchar(255) NOT NULL,
  `imagen_descripcion` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `usuario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `noticia_reciente`
--

CREATE TABLE `noticia_reciente` (
  `noticia_reciente_id` int(11) NOT NULL,
  `noticia_reciente_titulo` varchar(255) NOT NULL,
  `noticia_reciente_url` varchar(500) DEFAULT NULL,
  `noticia_reciente_fecha_publicacion` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `imagen_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `podcast`
--

CREATE TABLE `podcast` (
  `podcast_id` int(11) NOT NULL,
  `podcast_titulo` varchar(255) NOT NULL,
  `podcast_embebido` text DEFAULT NULL,
  `podcast_fecha_publicacion` datetime DEFAULT NULL,
  `imagen_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportaje`
--

CREATE TABLE `reportaje` (
  `reportaje_id` int(11) NOT NULL,
  `reportaje_titulo` varchar(255) NOT NULL,
  `resumen_corto` text DEFAULT NULL,
  `descripcion_detallada` text DEFAULT NULL,
  `reportaje_fecha_publicacion` datetime DEFAULT NULL,
  `destacado` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `autor_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `imagen_id` int(11) DEFAULT NULL,
  `documento_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportaje_imagen`
--

CREATE TABLE `reportaje_imagen` (
  `reportaje_imagen_id` int(11) NOT NULL,
  `reportaje_imagen_descripcion` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `reportaje_id` int(11) NOT NULL,
  `imagen_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `usuario_id` int(11) NOT NULL,
  `usuario_nombres` varchar(100) NOT NULL,
  `usuario_apellido_paterno` varchar(100) NOT NULL,
  `usuario_apellido_materno` varchar(100) DEFAULT NULL,
  `usuario_email` varchar(255) NOT NULL,
  `usuario_contrasena` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `rol` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`usuario_id`, `usuario_nombres`, `usuario_apellido_paterno`, `usuario_apellido_materno`, `usuario_email`, `usuario_contrasena`, `created_at`, `updated_at`, `rol`) VALUES
(1, 'Administrador', 'DDP', NULL, 'admin@dialogoydesarrollo.com.pe', '$2y$10$oQCow9z892hiU9SJT34iCelyROwpRLqA0DAZjn8Qa9Xkle76JRcHK', '2026-09-03 12:26:21', '2026-09-03 12:26:21', 'admin');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `video`
--

CREATE TABLE `video` (
  `video_id` int(11) NOT NULL,
  `video_titulo` varchar(255) NOT NULL,
  `video_fecha_publicacion` datetime DEFAULT NULL,
  `video_link_embebido` text DEFAULT NULL,
  `imagen_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `autor`
--
ALTER TABLE `autor`
  ADD PRIMARY KEY (`autor_id`);

--
-- Indices de la tabla `boletin_ntep`
--
ALTER TABLE `boletin_ntep`
  ADD PRIMARY KEY (`boletin_ntep_id`),
  ADD KEY `fk_boletin_imagen` (`imagen_id`),
  ADD KEY `fk_boletin_documento` (`documento_id`),
  ADD KEY `fk_boletin_usuario` (`usuario_id`);

--
-- Indices de la tabla `documento`
--
ALTER TABLE `documento`
  ADD PRIMARY KEY (`documento_id`),
  ADD KEY `fk_documento_usuario` (`usuario_id`);

--
-- Indices de la tabla `imagen`
--
ALTER TABLE `imagen`
  ADD PRIMARY KEY (`imagen_id`),
  ADD KEY `fk_imagen_usuario` (`usuario_id`);

--
-- Indices de la tabla `noticia_reciente`
--
ALTER TABLE `noticia_reciente`
  ADD PRIMARY KEY (`noticia_reciente_id`),
  ADD KEY `fk_noticia_imagen` (`imagen_id`),
  ADD KEY `fk_noticia_usuario` (`usuario_id`);

--
-- Indices de la tabla `podcast`
--
ALTER TABLE `podcast`
  ADD PRIMARY KEY (`podcast_id`),
  ADD KEY `fk_podcast_imagen` (`imagen_id`),
  ADD KEY `fk_podcast_usuario` (`usuario_id`);

--
-- Indices de la tabla `reportaje`
--
ALTER TABLE `reportaje`
  ADD PRIMARY KEY (`reportaje_id`),
  ADD KEY `fk_reportaje_autor` (`autor_id`),
  ADD KEY `fk_reportaje_usuario` (`usuario_id`),
  ADD KEY `fk_reportaje_imagen` (`imagen_id`),
  ADD KEY `fk_reportaje_documento` (`documento_id`);

--
-- Indices de la tabla `reportaje_imagen`
--
ALTER TABLE `reportaje_imagen`
  ADD PRIMARY KEY (`reportaje_imagen_id`),
  ADD KEY `fk_ri_reportaje` (`reportaje_id`),
  ADD KEY `fk_ri_imagen` (`imagen_id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`usuario_id`),
  ADD UNIQUE KEY `usuario_email` (`usuario_email`);

--
-- Indices de la tabla `video`
--
ALTER TABLE `video`
  ADD PRIMARY KEY (`video_id`),
  ADD KEY `fk_video_imagen` (`imagen_id`),
  ADD KEY `fk_video_usuario` (`usuario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `autor`
--
ALTER TABLE `autor`
  MODIFY `autor_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `boletin_ntep`
--
ALTER TABLE `boletin_ntep`
  MODIFY `boletin_ntep_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `documento`
--
ALTER TABLE `documento`
  MODIFY `documento_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `imagen`
--
ALTER TABLE `imagen`
  MODIFY `imagen_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `noticia_reciente`
--
ALTER TABLE `noticia_reciente`
  MODIFY `noticia_reciente_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `podcast`
--
ALTER TABLE `podcast`
  MODIFY `podcast_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reportaje`
--
ALTER TABLE `reportaje`
  MODIFY `reportaje_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reportaje_imagen`
--
ALTER TABLE `reportaje_imagen`
  MODIFY `reportaje_imagen_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `video`
--
ALTER TABLE `video`
  MODIFY `video_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `boletin_ntep`
--
ALTER TABLE `boletin_ntep`
  ADD CONSTRAINT `fk_boletin_documento` FOREIGN KEY (`documento_id`) REFERENCES `documento` (`documento_id`),
  ADD CONSTRAINT `fk_boletin_imagen` FOREIGN KEY (`imagen_id`) REFERENCES `imagen` (`imagen_id`),
  ADD CONSTRAINT `fk_boletin_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`);

--
-- Filtros para la tabla `documento`
--
ALTER TABLE `documento`
  ADD CONSTRAINT `fk_documento_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`);

--
-- Filtros para la tabla `imagen`
--
ALTER TABLE `imagen`
  ADD CONSTRAINT `fk_imagen_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`);

--
-- Filtros para la tabla `noticia_reciente`
--
ALTER TABLE `noticia_reciente`
  ADD CONSTRAINT `fk_noticia_imagen` FOREIGN KEY (`imagen_id`) REFERENCES `imagen` (`imagen_id`),
  ADD CONSTRAINT `fk_noticia_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`);

--
-- Filtros para la tabla `podcast`
--
ALTER TABLE `podcast`
  ADD CONSTRAINT `fk_podcast_imagen` FOREIGN KEY (`imagen_id`) REFERENCES `imagen` (`imagen_id`),
  ADD CONSTRAINT `fk_podcast_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`);

--
-- Filtros para la tabla `reportaje`
--
ALTER TABLE `reportaje`
  ADD CONSTRAINT `fk_reportaje_autor` FOREIGN KEY (`autor_id`) REFERENCES `autor` (`autor_id`),
  ADD CONSTRAINT `fk_reportaje_documento` FOREIGN KEY (`documento_id`) REFERENCES `documento` (`documento_id`),
  ADD CONSTRAINT `fk_reportaje_imagen` FOREIGN KEY (`imagen_id`) REFERENCES `imagen` (`imagen_id`),
  ADD CONSTRAINT `fk_reportaje_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`);

--
-- Filtros para la tabla `reportaje_imagen`
--
ALTER TABLE `reportaje_imagen`
  ADD CONSTRAINT `fk_ri_imagen` FOREIGN KEY (`imagen_id`) REFERENCES `imagen` (`imagen_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ri_reportaje` FOREIGN KEY (`reportaje_id`) REFERENCES `reportaje` (`reportaje_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `video`
--
ALTER TABLE `video`
  ADD CONSTRAINT `fk_video_imagen` FOREIGN KEY (`imagen_id`) REFERENCES `imagen` (`imagen_id`),
  ADD CONSTRAINT `fk_video_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`usuario_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
