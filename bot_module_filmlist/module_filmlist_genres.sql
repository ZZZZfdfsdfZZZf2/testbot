-- phpMyAdmin SQL Dump
-- version 3.5.8.1
-- http://www.phpmyadmin.net
--
-- Хост: mysql99.1gb.ru
-- Время создания: Апр 24 2022 г., 17:59
-- Версия сервера: 5.7.13-6-log
-- Версия PHP: 5.3.29

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `gb_dv_test3`
--

-- --------------------------------------------------------

--
-- Структура таблицы `module_filmlist_genres`
--

CREATE TABLE IF NOT EXISTS `module_filmlist_genres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `num` text NOT NULL,
  `button` text NOT NULL,
  `name` text NOT NULL,
  `order` int(11) NOT NULL,
  `load_check` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251 AUTO_INCREMENT=26 ;

--
-- Дамп данных таблицы `module_filmlist_genres`
--

INSERT INTO `module_filmlist_genres` (`id`, `num`, `button`, `name`, `order`, `load_check`) VALUES
(1, '1', '"\\ud83e\\uddb9\\u200d\\u2642\\ufe0f \\u0442\\u0440\\u0438\\u043b\\u043b\\u0435\\u0440 \\ud83e\\uddb9\\u200d\\u2642\\ufe0f"', 'триллер', 1, 1),
(2, '2', '"\\ud83d\\ude2d \\u0434\\u0440\\u0430\\u043c\\u0430 \\ud83d\\ude2d"', 'драма', 2, 1),
(3, '3', '"\\ud83d\\udd2a \\u043a\\u0440\\u0438\\u043c\\u0438\\u043d\\u0430\\u043b \\ud83d\\udd2a"', 'криминал', 3, 1),
(4, '4', '"\\ud83d\\udc69\\u200d\\u2764\\ufe0f\\u200d\\ud83d\\udc68 \\u043c\\u0435\\u043b\\u043e\\u0434\\u0440\\u0430\\u043c\\u0430 \\ud83d\\udc69\\u200d\\u2764\\ufe0f\\u200d\\ud83d\\udc68"', 'мелодрама', 4, 1),
(5, '5', '"\\ud83d\\udd75\\ufe0f\\u200d\\u2642\\ufe0f \\u0434\\u0435\\u0442\\u0435\\u043a\\u0442\\u0438\\u0432 \\ud83d\\udd75\\ufe0f\\u200d\\u2642\\ufe0f"', 'детектив', 5, 1),
(6, '6', '"\\ud83d\\udc7d \\u0444\\u0430\\u043d\\u0442\\u0430\\u0441\\u0442\\u0438\\u043a\\u0430 \\ud83d\\udc7d"', 'фантастика', 6, 1),
(7, '7', '"\\u26f0 \\u043f\\u0440\\u0438\\u043a\\u043b\\u044e\\u0447\\u0435\\u043d\\u0438\\u044f \\u26f0"', 'приключения', 7, 1),
(8, '8', '"\\ud83d\\udc64 \\u0431\\u0438\\u043e\\u0433\\u0440\\u0430\\u0444\\u0438\\u044f \\ud83d\\udc64"', 'биография', 8, 1),
(9, '10', '"\\ud83e\\udd20 \\u0432\\u0435\\u0441\\u0442\\u0435\\u0440\\u043d \\ud83e\\udd20"', 'вестерн', 9, 1),
(10, '11', '"\\ud83d\\udca5 \\u0431\\u043e\\u0435\\u0432\\u0438\\u043a \\ud83d\\udca5"', 'боевик', 10, 1),
(11, '12', '"\\ud83e\\uddd9\\u200d\\u2640\\ufe0f \\u0444\\u044d\\u043d\\u0442\\u0435\\u0437\\u0438 \\ud83e\\uddd9\\u200d\\u2640\\ufe0f"', 'фэнтези', 11, 1),
(12, '13', '"\\ud83e\\udd23 \\u043a\\u043e\\u043c\\u0435\\u0434\\u0438\\u044f \\ud83e\\udd23"', 'комедия', 12, 1),
(13, '14', '"\\u2694 \\u0432\\u043e\\u0435\\u043d\\u043d\\u044b\\u0439 \\u2694"', 'военный', 13, 1),
(14, '15', '"\\ud83d\\udee1 \\u0438\\u0441\\u0442\\u043e\\u0440\\u0438\\u0447\\u0435\\u0441\\u043a\\u0438\\u0439 \\ud83d\\udee1"', 'история', 14, 1),
(15, '16', '"\\ud83c\\udfbb \\u043c\\u0443\\u0437\\u044b\\u043a\\u0430 \\ud83c\\udfbb"', 'музыка', 15, 1),
(16, '17', '"\\ud83e\\udddf\\u200d\\u2642\\ufe0f \\u0443\\u0436\\u0430\\u0441\\u044b \\ud83e\\udddf\\u200d\\u2642\\ufe0f"', 'ужасы', 17, 1),
(17, '18', '"\\ud83d\\udc76 \\u043c\\u0443\\u043b\\u044c\\u0442\\u0444\\u0438\\u043b\\u044c\\u043c \\ud83d\\udc76"', 'мультфильм', 23, 1),
(18, '19', '"\\ud83d\\udc68\\u200d\\ud83d\\udc69\\u200d\\ud83d\\udc66 \\u0441\\u0435\\u043c\\u0435\\u0439\\u043d\\u044b\\u0439 \\ud83d\\udc68\\u200d\\ud83d\\udc69\\u200d\\ud83d\\udc66"', 'семейный', 19, 1),
(19, '20', '"\\ud83c\\udfbc \\u043c\\u044e\\u0437\\u0438\\u043a\\u043b \\ud83c\\udfbc"', 'мюзикл', 16, 1),
(20, '21', '"\\u26bd\\ufe0f \\u0441\\u043f\\u043e\\u0440\\u0442 \\u26bd\\ufe0f"', 'спорт', 18, 1),
(21, '22', '"\\ud83e\\udd13 \\u0434\\u043e\\u043a\\u0443\\u043c\\u0435\\u043d\\u0442\\u0430\\u043b\\u044c\\u043d\\u044b\\u0439 \\ud83e\\udd13"', 'документальный', 20, 1),
(22, '23', '"\\ud83c\\udfa5 \\u043a\\u043e\\u0440\\u043e\\u0442\\u043a\\u043e\\u043c\\u0435\\u0442\\u0440\\u0430\\u0436\\u043a\\u0430 \\ud83c\\udfa5"', 'короткометражка', 21, 1),
(23, '24', '"\\ud83d\\udc67 \\u0430\\u043d\\u0438\\u043c\\u0435 \\ud83d\\udc67"', 'аниме', 22, 1),
(25, '33', '"\\ud83e\\uddd2 \\u0434\\u0435\\u0442\\u0441\\u043a\\u0438\\u0439 \\ud83e\\uddd2"', 'детский', 24, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
