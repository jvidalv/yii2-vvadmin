-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-07-2019 a las 20:57:23
-- Versión del servidor: 10.1.37-MariaDB
-- Versión de PHP: 7.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `fempoble`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `poble_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tipo` varchar(100) NOT NULL DEFAULT 'media',
  `path` varchar(500) NOT NULL,
  `file_name` varchar(500) NOT NULL,
  `titol` varchar(500) DEFAULT NULL,
  `descripcio` varchar(1000) DEFAULT NULL,
  `slug` varchar(250) NOT NULL,
  `es_imatge` int(11) DEFAULT '0',
  `borrat` int(11) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `poble_id` (`poble_id`),
  ADD KEY `user_id_2` (`user_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `media_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `media_ibfk_2` FOREIGN KEY (`poble_id`) REFERENCES `poble` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
ALTER TABLE `poble` CHANGE `logo` `media_id` INT NULL;
ALTER TABLE `user` CHANGE `imatge` `media_id` INT NULL;
ALTER TABLE `poble` ADD FOREIGN KEY (`media_id`) REFERENCES `media`(`id`) ON DELETE SET NULL ON UPDATE NO ACTION;
ALTER TABLE `user` CHANGE `created_at` `created_at` INT(11) NULL;
ALTER TABLE `user` CHANGE `updated_at` `updated_at` INT(11) NULL;
ALTER TABLE `user` DROP FOREIGN KEY `user_ibfk_1`; ALTER TABLE `user` ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`media_id`) REFERENCES `media`(`id`) ON DELETE SET NULL ON UPDATE NO ACTION;
CREATE TABLE `fempoble`.`festivitat` ( `id` INT NOT NULL AUTO_INCREMENT , `user_id` INT NOT NULL , `poble_id` INT NOT NULL , `activa` TINYINT NULL DEFAULT '0' , `te_dies_previs` TINYINT NULL DEFAULT '0' , `te_dies_posteriors` TINYINT NULL DEFAULT '0' , `es_festa_major` TINYINT NULL DEFAULT '0' , `es_visible_movil` TINYINT NULL DEFAULT '1' , `nom` VARCHAR(150) NOT NULL , `descripcio` VARCHAR(5000) NULL , `dia_inici` DATE NOT NULL , `dia_fi` DATE NOT NULL , `borrat` TINYINT NULL DEFAULT '0' , `slug` VARCHAR(120) NULL , `updated_at` INT NULL , `created_at` INT NULL , `categoria_id` INT NULL , PRIMARY KEY (`id`), INDEX (`user_id`), INDEX (`poble_id`), INDEX (`categoria_id`)) ENGINE = InnoDB;
ALTER TABLE `festivitat` ADD FOREIGN KEY (`updated_at`) REFERENCES `user`(`id`) ON DELETE NO ACTION ON UPDATE NO ACTION; ALTER TABLE `festivitat` ADD FOREIGN KEY (`poble_id`) REFERENCES `poble`(`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `festivitat` CHANGE `es_visible_movil` `es_visible_movil` TINYINT(4) NULL DEFAULT '0';
ALTER TABLE `festivitat` DROP FOREIGN KEY `festivitat_ibfk_1`; ALTER TABLE `festivitat` ADD CONSTRAINT `festivitat_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
CREATE TABLE `fempoble`.`festivitat_has_dia` ( `id` INT NOT NULL AUTO_INCREMENT , `festivitat_id` INT NOT NULL , `user_id` INT NOT NULL , `es_dia_previ` TINYINT NULL DEFAULT '0' , `es_dia_posterior` TINYINT NULL DEFAULT '0' , `nom` VARCHAR(150) NOT NULL , `descripcio` VARCHAR(10000) NULL , `data_dia` DATE NOT NULL , `updated_at` INT NULL , `created_at` INT NULL , PRIMARY KEY (`id`), INDEX (`festivitat_id`), INDEX (`user_id`)) ENGINE = InnoDB;
ALTER TABLE `festivitat_has_dia` ADD FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE NO ACTION ON UPDATE NO ACTION; ALTER TABLE `festivitat_has_dia` ADD FOREIGN KEY (`festivitat_id`) REFERENCES `festivitat`(`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
CREATE TABLE `fempoble`.`festivitat_has_event` ( `id` INT NOT NULL AUTO_INCREMENT , `dia_id` INT NOT NULL , `festivitat_id` INT NOT NULL , `user_id` INT NOT NULL , `poble_id` INT NOT NULL , `categoria_id` INT NOT NULL , `es_visible_movil` TINYINT NULL DEFAULT '1' , `es_dia_previ` TINYINT NULL DEFAULT '0' , `es_dia_posterior` TINYINT NULL DEFAULT '0' , `nom` VARCHAR(250) NOT NULL , `descripcio` VARCHAR(2000) NULL , `localitzacio` VARCHAR(250) NULL , `dia_inici` DATE NULL , `hora_inici` TIME NOT NULL , `hora_fi` TIME NULL , `latitude` VARCHAR(50) NULL , `longitude` VARCHAR(50) NULL , `borrat` TINYINT NULL DEFAULT '0' , `slug` VARCHAR(250) NULL , `updated_at` INT NULL , `created_at` INT NULL , PRIMARY KEY (`id`), INDEX (`dia_id`), INDEX (`festivitat_id`), INDEX (`user_id`), INDEX (`poble_id`), INDEX (`categoria_id`)) ENGINE = InnoDB;
ALTER TABLE `festivitat_has_event` ADD FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE NO ACTION ON UPDATE NO ACTION; ALTER TABLE `festivitat_has_event` ADD FOREIGN KEY (`poble_id`) REFERENCES `poble`(`id`) ON DELETE NO ACTION ON UPDATE NO ACTION; ALTER TABLE `festivitat_has_event` ADD FOREIGN KEY (`dia_id`) REFERENCES `festivitat_has_dia`(`id`) ON DELETE NO ACTION ON UPDATE NO ACTION; ALTER TABLE `festivitat_has_event` ADD FOREIGN KEY (`festivitat_id`) REFERENCES `festivitat`(`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
ALTER TABLE `festivitat_has_event` DROP FOREIGN KEY `festivitat_has_event_ibfk_3`; ALTER TABLE `festivitat_has_event` ADD CONSTRAINT `festivitat_has_event_ibfk_3` FOREIGN KEY (`dia_id`) REFERENCES `festivitat_has_dia`(`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
ALTER TABLE `festivitat_has_event` ADD `organitzador` VARCHAR(150) NULL AFTER `descripcio`;
ALTER TABLE `festivitat_has_dia` ADD `nom_especial` VARCHAR(150) NULL AFTER `nom`;
ALTER TABLE `poble` ADD `es_visible_mobil` TINYINT NULL DEFAULT '1' AFTER `media_id`;
ALTER TABLE `user` ADD `actiu` TINYINT NULL DEFAULT '1' AFTER `media_id`;
CREATE TABLE `fempoble`.`contacta` ( `id` INT NOT NULL AUTO_INCREMENT , `user_id` INT NULL , `poble_id` INT NULL , `visibilitat` TINYINT NULL , `origen` TINYINT NULL , `nom` VARCHAR(250) NULL , `email` VARCHAR(250) NULL , `telefon` VARCHAR(100) NULL , `missatge` VARCHAR(2000) NULL , `llegit` TINYINT NULL DEFAULT '0' , `borrat` TINYINT NULL DEFAULT '0' , `updated_at` INT NULL , `created_at` INT NULL , PRIMARY KEY (`id`), INDEX (`user_id`), INDEX (`poble_id`)) ENGINE = InnoDB;
ALTER TABLE `contacta` ADD FOREIGN KEY (`poble_id`) REFERENCES `poble`(`id`) ON DELETE NO ACTION ON UPDATE NO ACTION; ALTER TABLE `contacta` ADD FOREIGN KEY (`user_id`) REFERENCES `user`(`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
