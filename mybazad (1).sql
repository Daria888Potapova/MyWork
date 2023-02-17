-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июн 18 2022 г., 12:44
-- Версия сервера: 5.7.33
-- Версия PHP: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `mybazad`
--

-- --------------------------------------------------------

--
-- Структура таблицы `academperfomancescore`
--

CREATE TABLE `academperfomancescore` (
  `id_academPerfomanceScore` int(11) NOT NULL,
  `id_academPerfomance` int(11) NOT NULL,
  `id_student` int(11) NOT NULL,
  `id_discipline` int(11) NOT NULL,
  `score` int(1) NOT NULL,
  `NoVisited` int(11) NOT NULL,
  `GoodReason` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `academperfomancescore`
--

INSERT INTO `academperfomancescore` (`id_academPerfomanceScore`, `id_academPerfomance`, `id_student`, `id_discipline`, `score`, `NoVisited`, `GoodReason`) VALUES
(15, 1, 1, 1, 1, 5, 15),
(16, 1, 1, 2, 1, 0, 0),
(17, 1, 7, 1, 1, 0, 0),
(18, 1, 7, 2, 2, 0, 0),
(19, 1, 18, 1, 1, 0, 0),
(20, 5, 1, 1, 3, 2, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `academperformance`
--

CREATE TABLE `academperformance` (
  `id_academPerformance` int(11) NOT NULL,
  `id_group` int(11) NOT NULL,
  `term` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `academperformance`
--

INSERT INTO `academperformance` (`id_academPerformance`, `id_group`, `term`) VALUES
(1, 1, 1),
(3, 1, 3),
(4, 1, 4),
(5, 1, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `academplan`
--

CREATE TABLE `academplan` (
  `id_academplan` int(11) NOT NULL,
  `id_group` int(11) NOT NULL,
  `date_academplan` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `academplan`
--

INSERT INTO `academplan` (`id_academplan`, `id_group`, `date_academplan`) VALUES
(3, 1, '2022-06-05');

-- --------------------------------------------------------

--
-- Структура таблицы `academplan_detail`
--

CREATE TABLE `academplan_detail` (
  `id_academPlanDetail` int(11) NOT NULL,
  `id_academplan` int(11) NOT NULL,
  `term` int(11) NOT NULL,
  `id_discipline` int(11) NOT NULL,
  `id_typeScore` int(11) NOT NULL,
  `timeTerm` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `academplan_detail`
--

INSERT INTO `academplan_detail` (`id_academPlanDetail`, `id_academplan`, `term`, `id_discipline`, `id_typeScore`, `timeTerm`) VALUES
(420, 3, 1, 1, 2, 100),
(421, 3, 2, 1, 1, 200),
(422, 3, 1, 2, 1, 300),
(423, 3, 2, 2, 2, 400),
(424, 3, 3, 2, 2, 500),
(425, 3, 4, 2, 1, 123),
(426, 3, 5, 2, 1, 123),
(427, 3, 6, 2, 1, 345),
(428, 3, 7, 2, 1, 215),
(429, 3, 8, 2, 1, 523);

-- --------------------------------------------------------

--
-- Структура таблицы `activity`
--

CREATE TABLE `activity` (
  `id_activity` int(11) NOT NULL,
  `name_activity` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `activity`
--

INSERT INTO `activity` (`id_activity`, `name_activity`) VALUES
(1, 'Здоровье и быт'),
(2, 'Студенческое самоуправление'),
(3, 'Духовно-нравственное воспитание'),
(4, 'Личностно-профессиональное развитие'),
(5, 'Учебная деятельность');

-- --------------------------------------------------------

--
-- Структура таблицы `classtime`
--

CREATE TABLE `classtime` (
  `id_classtime` int(11) NOT NULL,
  `id_group` int(11) NOT NULL,
  `date_classtime` date NOT NULL,
  `theme_classtime` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `schedule_classtime` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `classtime`
--

INSERT INTO `classtime` (`id_classtime`, `id_group`, `date_classtime`, `theme_classtime`, `schedule_classtime`) VALUES
(1, 1, '2022-05-11', 'Тестовый классный час', '1. первый\r\n2. второй\r\n3. третий');

-- --------------------------------------------------------

--
-- Структура таблицы `disciplines`
--

CREATE TABLE `disciplines` (
  `id_discipline` int(11) NOT NULL,
  `name_discipline` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `disciplines`
--

INSERT INTO `disciplines` (`id_discipline`, `name_discipline`) VALUES
(1, 'Иностранный язык'),
(2, 'История'),
(3, 'Физическая культура'),
(4, 'ОБЖ'),
(5, 'Литература'),
(6, 'Русский язык'),
(7, 'Астрономия'),
(8, 'Математика'),
(9, 'Информатика'),
(10, 'Физика'),
(11, 'Химия'),
(12, 'Обществознание'),
(13, 'Биология'),
(14, 'География'),
(15, 'История Иркутской области'),
(16, 'Экология'),
(17, 'Основы философии'),
(18, 'Технический английский язык'),
(19, 'Дискретная математика'),
(20, 'Основы криптографии'),
(21, 'Численные методы'),
(22, 'Менеджмент'),
(23, 'Теория вероятностей и метематическая статистика'),
(24, 'Основы теории информации'),
(25, 'Операционные системы и среды'),
(26, 'Архитектура электронно-вычислительных машин и вычислительные системы'),
(27, 'Безопасность жизнедеятельности'),
(28, 'Информационные системы в образовании'),
(29, 'Компьютерные издательские системы'),
(30, 'Мировые информационные ресурсы'),
(31, 'Основы алгоритмизации и программирования'),
(32, 'Основы проектирования баз данных'),
(33, 'основы робототехники'),
(34, 'Управление системами дистанционного обучения');

-- --------------------------------------------------------

--
-- Структура таблицы `events`
--

CREATE TABLE `events` (
  `id_event` int(11) NOT NULL,
  `id_activity` int(11) NOT NULL,
  `date_event` date NOT NULL,
  `name_event` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_levelEvent` int(11) NOT NULL,
  `id_typeEvent` int(11) NOT NULL,
  `id_resultEvent` int(11) NOT NULL,
  `fileEvent` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `events`
--

INSERT INTO `events` (`id_event`, `id_activity`, `date_event`, `name_event`, `id_levelEvent`, `id_typeEvent`, `id_resultEvent`, `fileEvent`) VALUES
(10, 4, '2022-05-11', 'Мероприятие 1', 2, 3, 3, ''),
(11, 1, '2022-05-04', 'Мероприятие 2', 3, 2, 3, '1e7d4359e19877046c5ea3a5153f3b1c.pdf'),
(13, 1, '2021-12-11', 'Мероприятия 4', 1, 1, 1, ''),
(14, 4, '2022-05-05', 'Мероприятие 3', 1, 1, 1, '');

-- --------------------------------------------------------

--
-- Структура таблицы `groups`
--

CREATE TABLE `groups` (
  `id_group` int(11) NOT NULL,
  `name_group` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `groups`
--

INSERT INTO `groups` (`id_group`, `name_group`, `id_user`) VALUES
(1, 'И-318', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `levelevent`
--

CREATE TABLE `levelevent` (
  `id_levelEvent` int(11) NOT NULL,
  `name_levelEvent` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `levelevent`
--

INSERT INTO `levelevent` (`id_levelEvent`, `name_levelEvent`) VALUES
(1, 'Областной'),
(2, 'Внутриколледжный'),
(3, 'Региональный'),
(4, 'Межрегиональный'),
(5, 'Всероссийский'),
(6, 'Международный');

-- --------------------------------------------------------

--
-- Структура таблицы `parentsmeeting`
--

CREATE TABLE `parentsmeeting` (
  `id_parentmeeting` int(11) NOT NULL,
  `date_parentmeeting` date NOT NULL,
  `theme_parentmeeting` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `count_parentmeeting` int(11) NOT NULL,
  `result_parentmeeting` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `parentsmeeting`
--

INSERT INTO `parentsmeeting` (`id_parentmeeting`, `date_parentmeeting`, `theme_parentmeeting`, `count_parentmeeting`, `result_parentmeeting`) VALUES
(1, '2022-06-02', 'Собрание 1', 6, 'asd'),
(3, '2022-06-23', 'тест', 3, 'фвавыапм');

-- --------------------------------------------------------

--
-- Структура таблицы `residences`
--

CREATE TABLE `residences` (
  `id_residence` int(11) NOT NULL,
  `name_residence` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `residences`
--

INSERT INTO `residences` (`id_residence`, `name_residence`) VALUES
(1, 'Проживает в сельской местности'),
(2, 'Проживает в городе Иркутск');

-- --------------------------------------------------------

--
-- Структура таблицы `responsibilities`
--

CREATE TABLE `responsibilities` (
  `id_responsibility` int(11) NOT NULL,
  `id_student` int(11) DEFAULT NULL,
  `name_responsibility` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `desc_responsibility` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_responsibility` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `responsibilities`
--

INSERT INTO `responsibilities` (`id_responsibility`, `id_student`, `name_responsibility`, `desc_responsibility`, `date_responsibility`) VALUES
(3, 18, 'Староста', '', '2022-06-01'),
(5, 1, 'Зам. старосты', 'asdasd', '2022-06-04'),
(6, 9, 'Учебный сектор', 'ничего не делает', '2022-06-05'),
(7, 20, 'Учебный сектор', '', '2022-06-08');

-- --------------------------------------------------------

--
-- Структура таблицы `resultevent`
--

CREATE TABLE `resultevent` (
  `id_resultEvent` int(11) NOT NULL,
  `name_resultEvent` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `resultevent`
--

INSERT INTO `resultevent` (`id_resultEvent`, `name_resultEvent`) VALUES
(1, 'Диплом'),
(2, 'Грамота'),
(3, 'Сертификат'),
(4, 'Благодарность');

-- --------------------------------------------------------

--
-- Структура таблицы `role`
--

CREATE TABLE `role` (
  `id_role` int(11) NOT NULL,
  `name_role` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `role`
--

INSERT INTO `role` (`id_role`, `name_role`) VALUES
(1, 'teacher'),
(2, 'admin');

-- --------------------------------------------------------

--
-- Структура таблицы `students`
--

CREATE TABLE `students` (
  `id_student` int(11) NOT NULL,
  `id_group` int(11) DEFAULT NULL,
  `FIO` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mother` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `father` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mother_workplace` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mother_education` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mother_profession` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `mother_number` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `father_workplace` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `father_education` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `father_profession` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `father_number` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Avatar` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birthday` date NOT NULL,
  `isDormitory` tinyint(1) NOT NULL,
  `dormitoryRoom` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_residence` int(11) DEFAULT NULL,
  `IsLargeFamily` tinyint(1) DEFAULT NULL,
  `IsPoorFamily` tinyint(1) DEFAULT NULL,
  `orphan` tinyint(1) DEFAULT NULL,
  `IsBudget` tinyint(1) DEFAULT NULL,
  `IsAcademicScholarShip` tinyint(1) DEFAULT NULL,
  `IsSocialScholarShip` tinyint(1) DEFAULT NULL,
  `IsScholarship` tinyint(1) DEFAULT NULL,
  `IsDispensaryAcc` tinyint(1) DEFAULT NULL,
  `HasChildren` tinyint(1) DEFAULT NULL,
  `HaveDisPerson` tinyint(1) DEFAULT NULL,
  `IntAccCollege` tinyint(1) DEFAULT NULL,
  `KDN` tinyint(1) DEFAULT NULL,
  `DisabledChildren` tinyint(1) DEFAULT NULL,
  `ChildrenUnemploy` tinyint(1) DEFAULT NULL,
  `ChildrenPension` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `students`
--

INSERT INTO `students` (`id_student`, `id_group`, `FIO`, `mother`, `father`, `mother_workplace`, `mother_education`, `mother_profession`, `mother_number`, `father_workplace`, `father_education`, `father_profession`, `father_number`, `Avatar`, `phone`, `birthday`, `isDormitory`, `dormitoryRoom`, `id_residence`, `IsLargeFamily`, `IsPoorFamily`, `orphan`, `IsBudget`, `IsAcademicScholarShip`, `IsSocialScholarShip`, `IsScholarship`, `IsDispensaryAcc`, `HasChildren`, `HaveDisPerson`, `IntAccCollege`, `KDN`, `DisabledChildren`, `ChildrenUnemploy`, `ChildrenPension`) VALUES
(1, 1, 'Андреева Валерия Владимировна', 'Андреева Ольга Александровна', '', 'Иркутский авиазавод старший диспетчер', 'Высшее', 'Диспетчер', '12348765438', '', '', '', '', 'fa4097f43e42ca3ff094f7526003ad91.jpeg', '12345678901', '1998-05-10', 0, '', 2, 1, 1, 0, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0),
(7, 1, 'Белова Алина Витальевна', '', '', '', '', '', '', '', '', '', '', 'bb833f76c5ce3d63bb922deac8c3aa78.jpeg', '89045686793', '2022-05-11', 0, '412', 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(9, 1, 'Березовикова Елизавета Викторовна', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '89045686793', '2022-05-19', 1, '412', 2, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(10, 1, 'Бураков Иван Юрьевич', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '89045686793', '2022-05-05', 1, '412', 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(11, 1, 'Буркин Денис Сергеевич', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '12348765438', '1999-11-15', 1, '', 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(12, 1, 'Власова Екатерина Яковлевна', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '12348765438', '1999-11-15', 1, '', 2, 1, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0),
(17, 1, 'Горбачев Андрей Вячеславович', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '+79041501309', '2022-04-28', 1, '', 2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(18, 1, 'Иванов Юрий Юрьевич', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '+79996818267', '2022-05-11', 0, '', 2, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(19, 1, 'Казанцев Сергей Викторович', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '12348765438', '2022-06-07', 0, '', 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(20, 1, 'Калиничев Александр Витальевич', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '12348765438', '2022-06-08', 1, '', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(21, 1, 'Карелин Сергей Алексеевич', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '12348765438', '2022-06-03', 1, '', 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(22, 1, 'Кобелев Денис Александрович', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '12348765438', '2022-06-08', 1, '', 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(23, 1, 'Маляр Михаил Васильевич', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '12348765438', '2022-06-09', 1, '', 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(24, 1, 'Меньшенин Никита Евгеньевич', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '12348765438', '2022-06-15', 0, '', 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(25, 1, 'Михайлова Жанна Александровна', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '12348765438', '2022-06-16', 0, '', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(26, 1, 'Новлянский Дмитрий Алексеевич', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '12348765438', '2022-06-08', 0, '', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(27, 1, 'Поломошнов Валентин Сергеевич', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '12348765438', '2022-06-10', 1, '', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(28, 1, 'Пукса Кирилл Эдуардович', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '12348765438', '2022-06-08', 1, '', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(29, 1, 'Сабирова Валерия Мулаяновна', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '12348765438', '2022-06-08', 1, '', 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(30, 1, 'Сахаровская Алина Сергеевна', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '12348765438', '2022-06-08', 0, '', 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(31, 1, 'Тагаева Дияна Азатбековна', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '12348765438', '2022-06-15', 0, '', 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(32, 1, 'Таранюк Алексей Игоревич', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '12348765438', '2022-06-16', 1, '', 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(33, 1, 'Тарасов Илья Алексеевич', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '12348765438', '2022-06-09', 1, '', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(34, 1, 'Толстых Роман Владимирович', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '12348765438', '2022-06-02', 1, '', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(35, 1, 'Шалупкин Степан Андреевич', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '12348765438', '2022-06-08', 1, '', 2, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(36, 1, 'Потапова Дарья Дмитриевна', '', '', '', '', '', '', '', '', '', '', 'NoAvatar.jpg', '12348765438', '2022-06-15', 1, '', 2, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `studentsandevents`
--

CREATE TABLE `studentsandevents` (
  `id_srudentsandevents` int(11) NOT NULL,
  `id_student` int(11) NOT NULL,
  `id_event` int(11) NOT NULL,
  `prize_event` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `studentsandevents`
--

INSERT INTO `studentsandevents` (`id_srudentsandevents`, `id_student`, `id_event`, `prize_event`) VALUES
(81, 1, 11, 'Сертификат'),
(82, 10, 11, ''),
(83, 28, 11, ''),
(87, 1, 10, ''),
(88, 10, 10, ''),
(89, 7, 10, '');

-- --------------------------------------------------------

--
-- Структура таблицы `typeevent`
--

CREATE TABLE `typeevent` (
  `id_typeEvent` int(11) NOT NULL,
  `name_typeEvent` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `typeevent`
--

INSERT INTO `typeevent` (`id_typeEvent`, `name_typeEvent`) VALUES
(1, 'Конференция'),
(2, 'Праздник'),
(3, 'Соревнования'),
(4, 'Конкурс');

-- --------------------------------------------------------

--
-- Структура таблицы `typescore`
--

CREATE TABLE `typescore` (
  `id_typeScore` int(11) NOT NULL,
  `name_typeScore` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `typescore`
--

INSERT INTO `typescore` (`id_typeScore`, `name_typeScore`) VALUES
(1, 'Экзамен'),
(2, 'Зачет');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `fio` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_role` int(11) NOT NULL,
  `avatar` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id_user`, `fio`, `email`, `password`, `id_role`, `avatar`) VALUES
(1, 'Ганеева Людмила Леонидовна', 'ganeeva@mail.ru', '4828140403f6eaee3b5af62a0b09ae61', 1, '590b89e98d8992879a368c7b3854528f.jpeg'),
(2, 'Потапова Дарья Дмитриевна', 'dariapotapova777@gmail.com', 'd4a1c6168d6a0392e0da1a5249b0f1da', 2, '805211c237129577a2cec987c02a1d60.gif');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `academperfomancescore`
--
ALTER TABLE `academperfomancescore`
  ADD PRIMARY KEY (`id_academPerfomanceScore`),
  ADD KEY `id_academPerfomance` (`id_academPerfomance`),
  ADD KEY `id_discipline` (`id_discipline`),
  ADD KEY `id_student` (`id_student`);

--
-- Индексы таблицы `academperformance`
--
ALTER TABLE `academperformance`
  ADD PRIMARY KEY (`id_academPerformance`),
  ADD KEY `id_group` (`id_group`);

--
-- Индексы таблицы `academplan`
--
ALTER TABLE `academplan`
  ADD PRIMARY KEY (`id_academplan`),
  ADD KEY `academplan_ibfk_1` (`id_group`);

--
-- Индексы таблицы `academplan_detail`
--
ALTER TABLE `academplan_detail`
  ADD PRIMARY KEY (`id_academPlanDetail`),
  ADD KEY `academplan_detail_ibfk_1` (`id_academplan`),
  ADD KEY `academplan_detail_ibfk_2` (`id_discipline`),
  ADD KEY `academplan_detail_ibfk_3` (`id_typeScore`);

--
-- Индексы таблицы `activity`
--
ALTER TABLE `activity`
  ADD PRIMARY KEY (`id_activity`);

--
-- Индексы таблицы `classtime`
--
ALTER TABLE `classtime`
  ADD PRIMARY KEY (`id_classtime`),
  ADD KEY `id_group` (`id_group`);

--
-- Индексы таблицы `disciplines`
--
ALTER TABLE `disciplines`
  ADD PRIMARY KEY (`id_discipline`);

--
-- Индексы таблицы `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id_event`),
  ADD KEY `id_levelEvent` (`id_levelEvent`),
  ADD KEY `id_typeEvent` (`id_typeEvent`),
  ADD KEY `id_resultEvent` (`id_resultEvent`),
  ADD KEY `id_activity` (`id_activity`);

--
-- Индексы таблицы `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id_group`),
  ADD KEY `id_user` (`id_user`);

--
-- Индексы таблицы `levelevent`
--
ALTER TABLE `levelevent`
  ADD PRIMARY KEY (`id_levelEvent`);

--
-- Индексы таблицы `parentsmeeting`
--
ALTER TABLE `parentsmeeting`
  ADD PRIMARY KEY (`id_parentmeeting`);

--
-- Индексы таблицы `residences`
--
ALTER TABLE `residences`
  ADD PRIMARY KEY (`id_residence`);

--
-- Индексы таблицы `responsibilities`
--
ALTER TABLE `responsibilities`
  ADD PRIMARY KEY (`id_responsibility`),
  ADD KEY `id_student` (`id_student`);

--
-- Индексы таблицы `resultevent`
--
ALTER TABLE `resultevent`
  ADD PRIMARY KEY (`id_resultEvent`);

--
-- Индексы таблицы `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`);

--
-- Индексы таблицы `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id_student`),
  ADD KEY `id_dormitory` (`isDormitory`),
  ADD KEY `id_residence` (`id_residence`),
  ADD KEY `id_group` (`id_group`);

--
-- Индексы таблицы `studentsandevents`
--
ALTER TABLE `studentsandevents`
  ADD PRIMARY KEY (`id_srudentsandevents`),
  ADD KEY `id_student` (`id_student`),
  ADD KEY `studentsandevents_ibfk_1` (`id_event`);

--
-- Индексы таблицы `typeevent`
--
ALTER TABLE `typeevent`
  ADD PRIMARY KEY (`id_typeEvent`);

--
-- Индексы таблицы `typescore`
--
ALTER TABLE `typescore`
  ADD PRIMARY KEY (`id_typeScore`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_role` (`id_role`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `academperfomancescore`
--
ALTER TABLE `academperfomancescore`
  MODIFY `id_academPerfomanceScore` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `academperformance`
--
ALTER TABLE `academperformance`
  MODIFY `id_academPerformance` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `academplan`
--
ALTER TABLE `academplan`
  MODIFY `id_academplan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `academplan_detail`
--
ALTER TABLE `academplan_detail`
  MODIFY `id_academPlanDetail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=430;

--
-- AUTO_INCREMENT для таблицы `activity`
--
ALTER TABLE `activity`
  MODIFY `id_activity` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `classtime`
--
ALTER TABLE `classtime`
  MODIFY `id_classtime` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `disciplines`
--
ALTER TABLE `disciplines`
  MODIFY `id_discipline` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT для таблицы `events`
--
ALTER TABLE `events`
  MODIFY `id_event` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблицы `groups`
--
ALTER TABLE `groups`
  MODIFY `id_group` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `levelevent`
--
ALTER TABLE `levelevent`
  MODIFY `id_levelEvent` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `parentsmeeting`
--
ALTER TABLE `parentsmeeting`
  MODIFY `id_parentmeeting` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `residences`
--
ALTER TABLE `residences`
  MODIFY `id_residence` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `responsibilities`
--
ALTER TABLE `responsibilities`
  MODIFY `id_responsibility` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `resultevent`
--
ALTER TABLE `resultevent`
  MODIFY `id_resultEvent` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `students`
--
ALTER TABLE `students`
  MODIFY `id_student` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT для таблицы `studentsandevents`
--
ALTER TABLE `studentsandevents`
  MODIFY `id_srudentsandevents` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT для таблицы `typeevent`
--
ALTER TABLE `typeevent`
  MODIFY `id_typeEvent` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `typescore`
--
ALTER TABLE `typescore`
  MODIFY `id_typeScore` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `academperfomancescore`
--
ALTER TABLE `academperfomancescore`
  ADD CONSTRAINT `academperfomancescore_ibfk_1` FOREIGN KEY (`id_academPerfomance`) REFERENCES `academperformance` (`id_academPerformance`),
  ADD CONSTRAINT `academperfomancescore_ibfk_2` FOREIGN KEY (`id_discipline`) REFERENCES `disciplines` (`id_discipline`),
  ADD CONSTRAINT `academperfomancescore_ibfk_3` FOREIGN KEY (`id_student`) REFERENCES `students` (`id_student`);

--
-- Ограничения внешнего ключа таблицы `academperformance`
--
ALTER TABLE `academperformance`
  ADD CONSTRAINT `academperformance_ibfk_1` FOREIGN KEY (`id_group`) REFERENCES `groups` (`id_group`);

--
-- Ограничения внешнего ключа таблицы `academplan`
--
ALTER TABLE `academplan`
  ADD CONSTRAINT `academplan_ibfk_1` FOREIGN KEY (`id_group`) REFERENCES `groups` (`id_group`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `academplan_detail`
--
ALTER TABLE `academplan_detail`
  ADD CONSTRAINT `academplan_detail_ibfk_1` FOREIGN KEY (`id_academplan`) REFERENCES `academplan` (`id_academplan`) ON DELETE CASCADE,
  ADD CONSTRAINT `academplan_detail_ibfk_2` FOREIGN KEY (`id_discipline`) REFERENCES `disciplines` (`id_discipline`) ON DELETE CASCADE,
  ADD CONSTRAINT `academplan_detail_ibfk_3` FOREIGN KEY (`id_typeScore`) REFERENCES `typescore` (`id_typeScore`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `classtime`
--
ALTER TABLE `classtime`
  ADD CONSTRAINT `classtime_ibfk_1` FOREIGN KEY (`id_group`) REFERENCES `groups` (`id_group`);

--
-- Ограничения внешнего ключа таблицы `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`id_levelEvent`) REFERENCES `levelevent` (`id_levelEvent`),
  ADD CONSTRAINT `events_ibfk_2` FOREIGN KEY (`id_typeEvent`) REFERENCES `typeevent` (`id_typeEvent`),
  ADD CONSTRAINT `events_ibfk_3` FOREIGN KEY (`id_resultEvent`) REFERENCES `resultevent` (`id_resultEvent`),
  ADD CONSTRAINT `events_ibfk_4` FOREIGN KEY (`id_activity`) REFERENCES `activity` (`id_activity`);

--
-- Ограничения внешнего ключа таблицы `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`);

--
-- Ограничения внешнего ключа таблицы `responsibilities`
--
ALTER TABLE `responsibilities`
  ADD CONSTRAINT `responsibilities_ibfk_2` FOREIGN KEY (`id_student`) REFERENCES `students` (`id_student`);

--
-- Ограничения внешнего ключа таблицы `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_2` FOREIGN KEY (`id_residence`) REFERENCES `residences` (`id_residence`),
  ADD CONSTRAINT `students_ibfk_3` FOREIGN KEY (`id_group`) REFERENCES `groups` (`id_group`);

--
-- Ограничения внешнего ключа таблицы `studentsandevents`
--
ALTER TABLE `studentsandevents`
  ADD CONSTRAINT `studentsandevents_ibfk_1` FOREIGN KEY (`id_event`) REFERENCES `events` (`id_event`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `studentsandevents_ibfk_2` FOREIGN KEY (`id_student`) REFERENCES `students` (`id_student`);

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
