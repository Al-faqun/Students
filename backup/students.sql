-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Май 25 2017 г., 22:36
-- Версия сервера: 10.1.21-MariaDB
-- Версия PHP: 7.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `students_book`
--

-- --------------------------------------------------------

--
-- Структура таблицы `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` text,
  `surname` text,
  `sex` varchar(3) DEFAULT NULL,
  `group_num` varchar(5) DEFAULT NULL,
  `email` varchar(254) DEFAULT NULL,
  `ege_sum` int(11) DEFAULT NULL,
  `birth_year` int(11) DEFAULT NULL,
  `location` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `students`
--

INSERT INTO `students` (`id`, `name`, `surname`, `sex`, `group_num`, `email`, `ege_sum`, `birth_year`, `location`) VALUES
(1, 'Ибрагим', 'Хусейнович', 'М', 'АБВГ5', 'ibrag355@gmail.com', 300, 1994, 'Местный'),
(2, 'Муслим', 'Шишани', 'М', 'fgh4', 'siem@rambler.ru', 299, 1967, 'Местный'),
(3, 'Мурзик', 'Выбрасович', 'Ж', 'АБВГД', 'kot@yandex.ru', 300, 1989, 'местный'),
(4, 'Ибрагим1', 'Хусейнович', 'М', 'АБВГ5', 'ibrag755@gmail.com', 300, 1994, 'Местный'),
(5, 'Мурзик1', 'Выбрасович', 'Ж', 'АБВГД', 'kot1@yandex.ru', 300, 1989, 'местный'),
(6, 'Мурзик2', 'Выбрасович', 'Ж', 'АБВГД', 'kot2@yandex.ru', 300, 1989, 'местный'),
(7, 'Мурзик3', 'Выбрасович', 'Ж', 'АБВГД', 'kot3@yandex.ru', 300, 1989, 'местный'),
(8, 'Мурзик4', 'Выбрасович', 'Ж', 'АБВГД', 'kot4@yandex.ru', 300, 1989, 'местный'),
(9, 'Мурзик5', 'Выбрасович', 'Ж', 'АБВГД', 'kot5@yandex.ru', 300, 1989, 'местный'),
(10, 'Мурзик6', 'Выбрасович', 'Ж', 'АБВГД', 'kot6@yandex.ru', 300, 1989, 'местный'),
(11, 'Мурзик7', 'Выбрасович', 'Ж', 'АБВГД', 'kot7@yandex.ru', 300, 1989, 'местный'),
(12, 'Мурзик8', 'Выбрасович', 'Ж', 'АБВГД', 'kot8@yandex.ru', 300, 1989, 'местный'),
(14, 'Люк1', 'Бессон', 'М', 'TYGF4', 'bes1@yandex.ru', 200, 1967, 'Иногородний'),
(15, 'Люк2', 'Бессон', 'М', 'TYGF4', 'bes2@yandex.ru', 200, 1967, 'Иногородний'),
(16, 'Люк3', 'Бессон', 'М', 'TYGF4', 'bes3@yandex.ru', 200, 1967, 'Иногородний'),
(17, 'Люк4', 'Бессон', 'М', 'TYGF4', 'bes4@yandex.ru', 200, 1967, 'Иногородний'),
(18, 'Люк5', 'Бессон', 'М', 'TYGF4', 'bes5@yandex.ru', 200, 1967, 'Иногородний'),
(19, 'Люк6', 'Бессон', 'М', 'TYGF4', 'bes6@yandex.ru', 200, 1967, 'Иногородний'),
(20, 'Люк7', 'Бессон', 'М', 'TYGF4', 'bes7@yandex.ru', 200, 1967, 'Иногородний'),
(21, 'Люк8', 'Бессон', 'М', 'TYGF4', 'bes8@yandex.ru', 200, 1967, 'Иногородний'),
(22, 'Люк9', 'Бессон', 'М', 'TYGF4', 'bes9@yandex.ru', 200, 1967, 'Иногородний'),
(41, 'Бака1', 'Сырная', 'Ж', 'ВДО43', 'cirnaya1@mail.ru', 277, 1968, 'Иногородний'),
(42, 'Бака2', 'Сырная', 'Ж', 'ВДО43', 'cirnaya2@mail.ru', 274, 1969, 'Иногородний'),
(43, 'Бака3', 'Сырная', 'Ж', 'ВДО43', 'cirnaya3@mail.ru', 271, 1970, 'Иногородний'),
(44, 'Бака4', 'Сырная', 'Ж', 'ВДО43', 'cirnaya4@mail.ru', 268, 1971, 'Иногородний'),
(45, 'Бака5', 'Сырная', 'Ж', 'ВДО43', 'cirnaya5@mail.ru', 265, 1972, 'Иногородний'),
(46, 'Бака6', 'Сырная', 'Ж', 'ВДО43', 'cirnaya6@mail.ru', 262, 1973, 'Иногородний'),
(47, 'Бака7', 'Сырная', 'Ж', 'ВДО43', 'cirnaya7@mail.ru', 259, 1974, 'Иногородний'),
(48, 'Бака8', 'Сырная', 'Ж', 'ВДО43', 'cirnaya8@mail.ru', 256, 1975, 'Иногородний'),
(49, 'Бака9', 'Сырная', 'Ж', 'ВДО43', 'cirnaya9@mail.ru', 253, 1976, 'Иногородний'),
(50, 'Футо1', 'Хмельницкий', 'М', 'ПИКАП', 'futou1@yap.com', 243, 1968, 'Местный'),
(51, 'Футо2', 'Хмельницкий', 'М', 'ПИКАП', 'futou2@yap.com', 241, 1969, 'Местный'),
(52, 'Футо3', 'Хмельницкий', 'М', 'ПИКАП', 'futou3@yap.com', 239, 1970, 'Местный'),
(53, 'Кагуя1', 'Хиросима', 'Ж', 'N9IUИ', 'kagu1@yak.net', 297, 1986, 'Местный'),
(54, 'Кагуя2', 'Хиросима', 'Ж', 'N9IUИ', 'kagu2@yak.net', 295, 1987, 'Местный'),
(55, 'Кагуя3', 'Хиросима', 'Ж', 'N9IUИ', 'kagu3@yak.net', 293, 1988, 'Местный'),
(62, 'Тестовое имя3', 'Тестовая фамилия', 'М', 'АБВГД', 'mail@mail.ru', 300, 1994, 'Иногородний'),
(68, 'Кагуя13sdsdsd', 'Хиросима', 'М', 'DDF', 'kagu12@yak.net', 233, 1975, 'Иногородний'),
(70, 'NazzGull', 'Yeroshenko', 'М', 'AF534', 'alfa@omega.com', 400, 1987, 'Местный'),
(84, 'Abra', 'Cadabta', 'М', 'fad32', 'dog@god.ru', 499, 1994, 'Иногородний');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
