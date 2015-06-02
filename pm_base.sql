-- phpMyAdmin SQL Dump
-- version 4.3.12
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Май 30 2015 г., 05:35
-- Версия сервера: 10.0.16-MariaDB-log
-- Версия PHP: 5.5.23-pl0-gentoo

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
-- Структура таблицы `sf_catalogs_groups`
--

CREATE TABLE IF NOT EXISTS `sf_catalogs_groups` (
  `id` varchar(40) NOT NULL,
  `name` varchar(50) NOT NULL,
  `state` enum('E','D') NOT NULL DEFAULT 'E',
  `comment` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `sf_catalogs_groups`
--

INSERT INTO `sf_catalogs_groups` (`id`, `name`, `state`, `comment`) VALUES
('C2AAE643-6E66-1791-62CA-5FE7488AD01C', '', 'E', '');

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
  `email` varchar(50) DEFAULT NULL
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
  `state` enum('i','a','c') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `sf_projects`
--

INSERT INTO `sf_projects` (`id`, `project_name`, `plan_start_date`, `plan_end_date`, `fact_start_date`, `fact_end_date`, `comment`, `author`, `create_date`, `state`) VALUES
('6F1249F9-4875-288F-86A4-395A261CC1B6', 'PM. Новейшее поколение', '2013-11-20', '2029-11-20', NULL, NULL, 'Всё что осталось в прошлом, то останется там навсегда.', 'F4D54A4F-A6B0-9B59-7011-1332C0125D99', '2014-11-16 02:02:10', 'i'),
('FDAAFAA0-C3A7-3950-5155-5CE6B5E272E9', 'ршгнолггншгншгнгшнгшншншгншн', '2017-01-20', '2021-01-20', NULL, NULL, 'гонгшегшенгегегенгенгегн', 'F4D54A4F-A6B0-9B59-7011-1332C0125D99', '2015-01-17 00:28:49', 'i');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `sf_catalogs_groups`
--
ALTER TABLE `sf_catalogs_groups`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `sf_logins`
--
ALTER TABLE `sf_logins`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `sf_projects`
--
ALTER TABLE `sf_projects`
  ADD PRIMARY KEY (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
