-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Июн 02 2015 г., 21:08
-- Версия сервера: 5.5.43-0ubuntu0.14.04.1
-- Версия PHP: 5.5.9-1ubuntu4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `pm_base`
--

-- --------------------------------------------------------

--
-- Структура таблицы `sf_catalogs`
--

CREATE TABLE IF NOT EXISTS `sf_catalogs` (
  `id` varchar(40) NOT NULL,
  `group_id` varchar(40) NOT NULL,
  `name` varchar(50) NOT NULL,
  `comment` varchar(200) DEFAULT NULL,
  `state` enum('E','D') NOT NULL DEFAULT 'E',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Каталоги';

--
-- Дамп данных таблицы `sf_catalogs`
--

INSERT INTO `sf_catalogs` (`id`, `group_id`, `name`, `comment`, `state`) VALUES
('148E5B9D-87A0-3460-5BED-E934BF2BFB9E', 'C5E4566F-FEE3-E4B9-B4AB-805CBACF17D4', 'орпропропроп', '', 'E'),
('7C2B3F5E-CFA2-EC1C-690E-9453DE4B8C57', '7D175602-D308-069F-F235-5C0C9B434EC5', 'Каталог кукол', '', 'E'),
('8487EC10-195F-AB77-2EAB-9362098FF78C', '68A9EB06-23CE-D193-3D13-6AFA9BB64FF1', ' Тестовый каталог', '', 'E'),
('9118A53C-784F-6D30-816F-4A0A4DB379AF', '7D175602-D308-069F-F235-5C0C9B434EC5', 'Еще каталог', '', 'E'),
('AD1C058F-3E4C-8D8D-1673-8C0FA021C5F3', '7D175602-D308-069F-F235-5C0C9B434EC5', '23423423434', '', 'E'),
('B2A057B8-C0F2-C702-A824-70BF2390612E', '7D175602-D308-069F-F235-5C0C9B434EC5', 'ihjkhjkhhjk', '', 'E');

-- --------------------------------------------------------

--
-- Структура таблицы `sf_catalogs_groups`
--

CREATE TABLE IF NOT EXISTS `sf_catalogs_groups` (
  `id` varchar(40) NOT NULL,
  `name` varchar(50) NOT NULL,
  `state` enum('E','D') NOT NULL DEFAULT 'E',
  `comment` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Группы каталогов';

--
-- Дамп данных таблицы `sf_catalogs_groups`
--

INSERT INTO `sf_catalogs_groups` (`id`, `name`, `state`, `comment`) VALUES
('28A25589-746D-6454-BD36-1B904192FF0A', 'еще группа', 'E', 'ывааа'),
('43080700-8EB4-E873-5098-7A25F6A61748', '4', 'E', ''),
('5983134B-0E24-BAE3-19F5-DF25D5C1456F', '5', 'E', ''),
('68A9EB06-23CE-D193-3D13-6AFA9BB64FF1', 'Тестовая группа', 'E', 'А здесь коментарий'),
('7D175602-D308-069F-F235-5C0C9B434EC5', '1', 'E', ''),
('C5E4566F-FEE3-E4B9-B4AB-805CBACF17D4', '2', 'E', ''),
('C620424E-2F0F-2334-9805-8CB22DC218BF', '6', 'E', ''),
('E5E850DD-D5B2-0FE0-285E-541CEF1D5E75', '3', 'E', ''),
('F628C94A-E290-B017-DFF5-F3B8E12B0313', 'sdfsdfsdfsdfd', 'E', 'dddd');

-- --------------------------------------------------------

--
-- Структура таблицы `sf_catalogs_items`
--

CREATE TABLE IF NOT EXISTS `sf_catalogs_items` (
  `id` varchar(40) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(200) DEFAULT NULL,
  `cost` float NOT NULL DEFAULT '0',
  `state` enum('E','D') NOT NULL DEFAULT 'E',
  `comment` varchar(200) DEFAULT NULL,
  `catalog_id` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `sf_logins`
--

CREATE TABLE IF NOT EXISTS `sf_logins` (
  `id` varchar(40) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(32) NOT NULL,
  `state` enum('e','d') NOT NULL,
  `reg_date` datetime NOT NULL,
  `last_sign_date` datetime DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `user_type` enum('s','n','a') NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `second_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `birthdate` date NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Users accounts';

--
-- Дамп данных таблицы `sf_logins`
--

INSERT INTO `sf_logins` (`id`, `login`, `password`, `state`, `reg_date`, `last_sign_date`, `comment`, `user_type`, `first_name`, `second_name`, `last_name`, `birthdate`, `email`) VALUES
('F4D54A4F-A6B0-9B59-7011-1332C0125D99', 'kalashnikov.anton@gmail.com', '9ead28da605e9524c71c83468ee00278', 'e', '2014-10-05 21:34:19', NULL, NULL, 'a', '', NULL, '', '0000-00-00', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `sf_projects`
--

CREATE TABLE IF NOT EXISTS `sf_projects` (
  `id` varchar(40) NOT NULL,
  `project_name` varchar(100) NOT NULL,
  `plan_start_date` date NOT NULL,
  `plan_end_date` date NOT NULL,
  `fact_start_date` date DEFAULT NULL,
  `fact_end_date` date DEFAULT NULL,
  `comment` varchar(255) NOT NULL,
  `author` varchar(40) NOT NULL,
  `create_date` datetime NOT NULL,
  `state` enum('i','a','c') NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `sf_projects`
--

INSERT INTO `sf_projects` (`id`, `project_name`, `plan_start_date`, `plan_end_date`, `fact_start_date`, `fact_end_date`, `comment`, `author`, `create_date`, `state`) VALUES
('6F1249F9-4875-288F-86A4-395A261CC1B6', 'PM. Новейшее поколение', '2013-11-20', '2029-11-20', NULL, NULL, 'Всё что осталось в прошлом, то останется там навсегда.', 'F4D54A4F-A6B0-9B59-7011-1332C0125D99', '2014-11-16 02:02:10', 'i'),
('FDAAFAA0-C3A7-3950-5155-5CE6B5E272E9', 'ршгнолггншгншгнгшнгшншншгншн', '2017-01-20', '2021-01-20', NULL, NULL, 'гонгшегшенгегегенгенгегн', 'F4D54A4F-A6B0-9B59-7011-1332C0125D99', '2015-01-17 00:28:49', 'i');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
