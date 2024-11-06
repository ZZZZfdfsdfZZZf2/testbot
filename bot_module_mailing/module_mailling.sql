-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Май 13 2022 г., 18:15
-- Версия сервера: 5.5.68-MariaDB
-- Версия PHP: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `clicker`
--

-- --------------------------------------------------------

--
-- Структура таблицы `module_mailling`
--

CREATE TABLE IF NOT EXISTS `module_mailling` (
  `id` int(11) NOT NULL,
  `admin_chat_id` text NOT NULL,
  `message_id` int(11) NOT NULL,
  `status` text NOT NULL,
  `user_id_max` int(11) NOT NULL,
  `user_id_last` int(11) NOT NULL,
  `count_all` int(11) NOT NULL,
  `count_ok` int(11) NOT NULL,
  `count_block` int(11) NOT NULL,
  `count_error` int(11) NOT NULL,
  `text` text NOT NULL,
  `file_type` text NOT NULL,
  `file_id` text NOT NULL,
  `keyboard` text NOT NULL,
  `button_dop` text NOT NULL,
  `entities` text NOT NULL,
  `preview` int(11) NOT NULL,
  `date_start` datetime NOT NULL,
  `date_finish` datetime NOT NULL,
  `date_wait` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `module_mailling`
--
ALTER TABLE `module_mailling`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `module_mailling`
--
ALTER TABLE `module_mailling`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
