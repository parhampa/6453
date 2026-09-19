-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 12, 2026 at 02:52 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cafe`
--

-- --------------------------------------------------------

--
-- Table structure for table `act_log`
--

CREATE TABLE `act_log` (
  `id` int(11) NOT NULL,
  `user` varchar(500) COLLATE utf8_persian_ci NOT NULL,
  `user_type` varchar(300) COLLATE utf8_persian_ci NOT NULL,
  `file_name` varchar(300) COLLATE utf8_persian_ci NOT NULL,
  `place_title` varchar(500) COLLATE utf8_persian_ci NOT NULL,
  `action` varchar(300) COLLATE utf8_persian_ci NOT NULL,
  `action_title` varchar(500) COLLATE utf8_persian_ci NOT NULL,
  `tbl` varchar(300) COLLATE utf8_persian_ci NOT NULL,
  `key_name` varchar(300) COLLATE utf8_persian_ci NOT NULL,
  `key_type` int(11) NOT NULL,
  `key_var` varchar(300) COLLATE utf8_persian_ci NOT NULL,
  `rec_title` varchar(300) COLLATE utf8_persian_ci NOT NULL,
  `act_date` date NOT NULL,
  `mili` varchar(300) COLLATE utf8_persian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `act_log`
--

INSERT INTO `act_log` (`id`, `user`, `user_type`, `file_name`, `place_title`, `action`, `action_title`, `tbl`, `key_name`, `key_type`, `key_var`, `rec_title`, `act_date`, `mili`) VALUES
(1, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'addform', 'مشاهده فرم ثبت اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789208011461'),
(2, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789208012376'),
(3, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'addform', 'مشاهده فرم ثبت اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789208013381'),
(4, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'addquery', 'ثبت اطلاعات جدید', 'marketers', 'id', 1, '1', '-', '2026-09-12', '1789208026971'),
(5, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789208028184'),
(6, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'editform', 'نمایش فرم ویرایش اطلاعات', 'marketers', 'id', 1, '1', '-', '2026-09-12', '1789208031800'),
(7, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789208036794'),
(8, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'editform', 'نمایش فرم ویرایش اطلاعات', 'marketers', 'id', 1, '1', '-', '2026-09-12', '1789208038260'),
(9, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'editquery', 'اعمال تغییرات در اطلاعات', 'marketers', 'id', 1, '1', '-', '2026-09-12', '1789208041281'),
(10, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789208042374'),
(11, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'editform', 'نمایش فرم ویرایش اطلاعات', 'marketers', 'id', 1, '1', '-', '2026-09-12', '1789208043694'),
(12, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'editquery', 'اعمال تغییرات در اطلاعات', 'marketers', 'id', 1, '1', '-', '2026-09-12', '1789208045944'),
(13, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789208047107'),
(14, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'addform', 'مشاهده فرم ثبت اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789209607625'),
(15, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'addform', 'مشاهده فرم ثبت اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789210042406'),
(16, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789210044439'),
(17, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'editform', 'نمایش فرم ویرایش اطلاعات', 'marketers', 'id', 1, '1', '-', '2026-09-12', '1789210046731'),
(18, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'editform', 'نمایش فرم ویرایش اطلاعات', 'marketers', 'id', 1, '1', '-', '2026-09-12', '1789210165276'),
(19, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789210181528'),
(20, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'addform', 'مشاهده فرم ثبت اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789210183315'),
(21, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'addform', 'مشاهده فرم ثبت اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789210637806'),
(22, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789210644861'),
(23, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'addform', 'مشاهده فرم ثبت اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789210646062'),
(24, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789210648274'),
(25, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'addform', 'مشاهده فرم ثبت اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789210649412'),
(26, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789210683747'),
(27, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789212069461'),
(28, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789212070155'),
(29, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789212070499'),
(30, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789212070645'),
(31, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789212070835'),
(32, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789212166772'),
(33, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789212167725'),
(34, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789212203056'),
(35, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789212204079'),
(36, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'addform', 'مشاهده فرم ثبت اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789212207664'),
(37, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'addform', 'مشاهده فرم ثبت اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789212320499'),
(38, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789212430830'),
(39, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'addform', 'مشاهده فرم ثبت اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789212431814'),
(40, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'addform', 'مشاهده فرم ثبت اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789212535119'),
(41, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'addform', 'مشاهده فرم ثبت اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789212537461'),
(42, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'addform', 'مشاهده فرم ثبت اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789212537660'),
(43, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'addform', 'مشاهده فرم ثبت اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789212537775'),
(44, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'addform', 'مشاهده فرم ثبت اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789212570180'),
(45, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'addform', 'مشاهده فرم ثبت اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789212627013'),
(46, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789212627865'),
(47, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'addform', 'مشاهده فرم ثبت اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789212628877'),
(48, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'addquery', 'ثبت اطلاعات جدید', 'cafes', 'id', 1, '1', '-', '2026-09-12', '1789212697863'),
(49, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789212698814'),
(50, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'editform', 'نمایش فرم ویرایش اطلاعات', 'cafes', 'id', 1, '1', '-', '2026-09-12', '1789212700547'),
(51, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213253505'),
(52, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'addform', 'مشاهده فرم ثبت اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213254727'),
(53, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'addform', 'مشاهده فرم ثبت اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213302451'),
(54, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213306062'),
(55, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'editform', 'نمایش فرم ویرایش اطلاعات', 'cafes', 'id', 1, '1', '-', '2026-09-12', '1789213307008'),
(56, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'editquery', 'اعمال تغییرات در اطلاعات', 'cafes', 'id', 1, '1', '-', '2026-09-12', '1789213312847'),
(57, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213314042'),
(58, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'editform', 'نمایش فرم ویرایش اطلاعات', 'cafes', 'id', 1, '1', '-', '2026-09-12', '1789213315825'),
(59, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213318540'),
(60, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213537336'),
(61, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'addform', 'مشاهده فرم ثبت اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213541960'),
(62, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213543733'),
(63, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'addform', 'مشاهده فرم ثبت اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213544533'),
(64, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213546660'),
(65, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789213547502'),
(66, 'admin', 'admin', 'admin_user.php', 'تعریف مدیریت کاربران', 'show', 'نمایش اطلاعات', 'admin_user', 'username', 0, '0', '-', '2026-09-12', '1789213549150'),
(67, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789213550237'),
(68, 'admin', 'admin', 'admin_user.php', 'تعریف مدیریت کاربران', 'show', 'نمایش اطلاعات', 'admin_user', 'username', 0, '0', '-', '2026-09-12', '1789213551950'),
(69, 'admin', 'admin', 'admin_user.php', 'تعریف مدیریت کاربران', 'addform', 'مشاهده فرم ثبت اطلاعات', 'admin_user', 'username', 0, '0', '-', '2026-09-12', '1789213552735'),
(70, 'admin', 'admin', 'admin_user.php', 'تعریف مدیریت کاربران', 'show', 'نمایش اطلاعات', 'admin_user', 'username', 0, '0', '-', '2026-09-12', '1789213554216'),
(71, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789213563371'),
(72, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213563937'),
(73, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789213564400'),
(74, 'admin', 'admin', 'admin_user.php', 'تعریف مدیریت کاربران', 'show', 'نمایش اطلاعات', 'admin_user', 'username', 0, '0', '-', '2026-09-12', '1789213564980'),
(75, 'admin', 'admin', 'admin_user.php', 'تعریف مدیریت کاربران', 'show', 'نمایش اطلاعات', 'admin_user', 'username', 0, '0', '-', '2026-09-12', '1789213565982'),
(76, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789213566461'),
(77, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213566873'),
(78, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789213567344'),
(79, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213567889'),
(80, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789213568262'),
(81, 'admin', 'admin', 'admin_user.php', 'تعریف مدیریت کاربران', 'show', 'نمایش اطلاعات', 'admin_user', 'username', 0, '0', '-', '2026-09-12', '1789213568641'),
(82, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213570017'),
(83, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789213624141'),
(84, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213624960'),
(85, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789213625646'),
(86, 'admin', 'admin', 'admin_user.php', 'تعریف مدیریت کاربران', 'show', 'نمایش اطلاعات', 'admin_user', 'username', 0, '0', '-', '2026-09-12', '1789213626267'),
(87, 'admin', 'admin', 'admin_user.php', 'تعریف مدیریت کاربران', 'show', 'نمایش اطلاعات', 'admin_user', 'username', 0, '0', '-', '2026-09-12', '1789213695832'),
(88, 'admin', 'admin', 'admin_user.php', 'تعریف مدیریت کاربران', 'show', 'نمایش اطلاعات', 'admin_user', 'username', 0, '0', '-', '2026-09-12', '1789213705156'),
(89, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789213705848'),
(90, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213706558'),
(91, 'admin', 'admin', 'admin_user.php', 'تعریف مدیریت کاربران', 'show', 'نمایش اطلاعات', 'admin_user', 'username', 0, '0', '-', '2026-09-12', '1789213755603'),
(92, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789213756157'),
(93, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213756711'),
(94, 'admin', 'admin', 'admin_user.php', 'تعریف مدیریت کاربران', 'show', 'نمایش اطلاعات', 'admin_user', 'username', 0, '0', '-', '2026-09-12', '1789213758117'),
(95, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789213758534'),
(96, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213759025'),
(97, 'admin', 'admin', 'admin_user.php', 'تعریف مدیریت کاربران', 'show', 'نمایش اطلاعات', 'admin_user', 'username', 0, '0', '-', '2026-09-12', '1789213760139'),
(98, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789213760549'),
(99, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213760919'),
(100, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789213761321'),
(101, 'admin', 'admin', 'admin_user.php', 'تعریف مدیریت کاربران', 'show', 'نمایش اطلاعات', 'admin_user', 'username', 0, '0', '-', '2026-09-12', '1789213761723'),
(102, 'admin', 'admin', 'admin_user.php', 'تعریف مدیریت کاربران', 'show', 'نمایش اطلاعات', 'admin_user', 'username', 0, '0', '-', '2026-09-12', '1789213762629'),
(103, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789213763094'),
(104, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213763536'),
(105, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789213763949'),
(106, 'admin', 'admin', 'admin_user.php', 'تعریف مدیریت کاربران', 'show', 'نمایش اطلاعات', 'admin_user', 'username', 0, '0', '-', '2026-09-12', '1789213764459'),
(107, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213765863'),
(108, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789213766325'),
(109, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789213767086'),
(110, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'editform', 'نمایش فرم ویرایش اطلاعات', 'cafes', 'id', 1, '1', '-', '2026-09-12', '1789214122038'),
(111, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789214124383'),
(112, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789214309251'),
(113, 'admin', 'admin', 'cafe_categories.php', 'تعریف دسته بندی کافه‌ها', 'show', 'نمایش اطلاعات', 'cafe_categories', 'id', 1, '0', '-', '2026-09-12', '1789214310060'),
(114, 'admin', 'admin', 'cafe_categories.php', 'تعریف دسته بندی کافه‌ها', 'addform', 'مشاهده فرم ثبت اطلاعات', 'cafe_categories', 'id', 1, '0', '-', '2026-09-12', '1789214311638'),
(115, 'admin', 'admin', 'cafe_categories.php', 'تعریف دسته بندی کافه‌ها', 'addquery', 'ثبت اطلاعات جدید', 'cafe_categories', 'id', 1, '1', '-', '2026-09-12', '1789214324805'),
(116, 'admin', 'admin', 'cafe_categories.php', 'تعریف دسته بندی کافه‌ها', 'show', 'نمایش اطلاعات', 'cafe_categories', 'id', 1, '0', '-', '2026-09-12', '1789214325782'),
(117, 'admin', 'admin', 'cafe_categories.php', 'تعریف دسته بندی کافه‌ها', 'addform', 'مشاهده فرم ثبت اطلاعات', 'cafe_categories', 'id', 1, '0', '-', '2026-09-12', '1789214326751'),
(118, 'admin', 'admin', 'cafe_categories.php', 'تعریف دسته بندی کافه‌ها', 'addquery', 'ثبت اطلاعات جدید', 'cafe_categories', 'id', 1, '2', '-', '2026-09-12', '1789214331773'),
(119, 'admin', 'admin', 'cafe_categories.php', 'تعریف دسته بندی کافه‌ها', 'show', 'نمایش اطلاعات', 'cafe_categories', 'id', 1, '0', '-', '2026-09-12', '1789214332778'),
(120, 'admin', 'admin', 'cafe_categories.php', 'تعریف دسته بندی کافه‌ها', 'editform', 'نمایش فرم ویرایش اطلاعات', 'cafe_categories', 'id', 1, '2', '-', '2026-09-12', '1789214353074'),
(121, 'admin', 'admin', 'cafe_categories.php', 'تعریف دسته بندی کافه‌ها', 'show', 'نمایش اطلاعات', 'cafe_categories', 'id', 1, '0', '-', '2026-09-12', '1789214871097'),
(122, 'admin', 'admin', 'cafe_categories.php', 'تعریف دسته بندی کافه‌ها', 'addform', 'مشاهده فرم ثبت اطلاعات', 'cafe_categories', 'id', 1, '0', '-', '2026-09-12', '1789214872523'),
(123, 'admin', 'admin', 'cafe_categories.php', 'تعریف دسته بندی کافه‌ها', 'addquery', 'ثبت اطلاعات جدید', 'cafe_categories', 'id', 1, '3', '-', '2026-09-12', '1789214880403'),
(124, 'admin', 'admin', 'cafe_categories.php', 'تعریف دسته بندی کافه‌ها', 'show', 'نمایش اطلاعات', 'cafe_categories', 'id', 1, '0', '-', '2026-09-12', '1789214881577'),
(125, 'admin', 'admin', 'cafe_categories.php', 'تعریف دسته بندی کافه‌ها', 'show', 'نمایش اطلاعات', 'cafe_categories', 'id', 1, '0', '-', '2026-09-12', '1789214884275'),
(126, 'admin', 'admin', 'cafe_categories.php', 'تعریف دسته بندی کافه‌ها', 'show', 'نمایش اطلاعات', 'cafe_categories', 'id', 1, '0', '-', '2026-09-12', '1789214885116'),
(127, 'admin', 'admin', 'cafe_categories.php', 'تعریف دسته بندی کافه‌ها', 'show', 'نمایش اطلاعات', 'cafe_categories', 'id', 1, '0', '-', '2026-09-12', '1789214886605'),
(128, 'admin', 'admin', 'cafes.php', 'تعریف کافه‌ها', 'show', 'نمایش اطلاعات', 'cafes', 'id', 1, '0', '-', '2026-09-12', '1789214887077'),
(129, 'admin', 'admin', 'cafe_categories.php', 'تعریف دسته بندی کافه‌ها', 'show', 'نمایش اطلاعات', 'cafe_categories', 'id', 1, '0', '-', '2026-09-12', '1789214887652'),
(130, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789214888427'),
(131, 'admin', 'admin', 'admin_user.php', 'تعریف مدیریت کاربران', 'show', 'نمایش اطلاعات', 'admin_user', 'username', 0, '0', '-', '2026-09-12', '1789214888905'),
(132, 'admin', 'admin', 'marketers.php', 'تعریف بازاریابان', 'show', 'نمایش اطلاعات', 'marketers', 'id', 1, '0', '-', '2026-09-12', '1789214889388'),
(133, 'admin', 'admin', 'cafe_categories.php', 'تعریف دسته بندی کافه‌ها', 'show', 'نمایش اطلاعات', 'cafe_categories', 'id', 1, '0', '-', '2026-09-12', '1789214889989'),
(134, 'admin', 'admin', 'cafe_categories.php', 'تعریف دسته بندی کافه‌ها', 'show', 'نمایش اطلاعات', 'cafe_categories', 'id', 1, '0', '-', '2026-09-12', '1789214972411'),
(135, 'admin', 'admin', 'cafe_categories.php', 'تعریف دسته بندی کافه‌ها', 'show', 'نمایش اطلاعات', 'cafe_categories', 'id', 1, '0', '-', '2026-09-12', '1789214999620'),
(136, 'admin', 'admin', 'menu_items.php', 'تعریف آیتم‌های منو', 'show', 'نمایش اطلاعات', 'menu_items', 'id', 1, '0', '-', '2026-09-12', '1789215000420'),
(137, 'admin', 'admin', 'menu_items.php', 'تعریف آیتم‌های منو', 'addform', 'مشاهده فرم ثبت اطلاعات', 'menu_items', 'id', 1, '0', '-', '2026-09-12', '1789215001423'),
(138, 'admin', 'admin', 'menu_items.php', 'تعریف آیتم‌های منو', 'addquery', 'ثبت اطلاعات جدید', 'menu_items', 'id', 1, '1', '-', '2026-09-12', '1789215030017'),
(139, 'admin', 'admin', 'menu_items.php', 'تعریف آیتم‌های منو', 'show', 'نمایش اطلاعات', 'menu_items', 'id', 1, '0', '-', '2026-09-12', '1789215031271'),
(140, 'admin', 'admin', 'menu_items.php', 'تعریف آیتم‌های منو', 'addform', 'مشاهده فرم ثبت اطلاعات', 'menu_items', 'id', 1, '0', '-', '2026-09-12', '1789215032561'),
(141, 'admin', 'admin', 'menu_items.php', 'تعریف آیتم‌های منو', 'addquery', 'ثبت اطلاعات جدید', 'menu_items', 'id', 1, '2', '-', '2026-09-12', '1789215040853'),
(142, 'admin', 'admin', 'menu_items.php', 'تعریف آیتم‌های منو', 'show', 'نمایش اطلاعات', 'menu_items', 'id', 1, '0', '-', '2026-09-12', '1789215041766'),
(143, 'admin', 'admin', 'customers.php', 'تعریف مشتریان', 'show', 'نمایش اطلاعات', 'customers', 'id', 1, '0', '-', '2026-09-12', '1789216304230'),
(144, 'admin', 'admin', 'customers.php', 'تعریف مشتریان', 'addform', 'مشاهده فرم ثبت اطلاعات', 'customers', 'id', 1, '0', '-', '2026-09-12', '1789216307364'),
(145, 'admin', 'admin', 'customers.php', 'تعریف مشتریان', 'addquery', 'ثبت اطلاعات جدید', 'customers', 'id', 1, '1', '-', '2026-09-12', '1789216323487'),
(146, 'admin', 'admin', 'customers.php', 'تعریف مشتریان', 'show', 'نمایش اطلاعات', 'customers', 'id', 1, '0', '-', '2026-09-12', '1789216324464'),
(147, 'admin', 'admin', 'customers.php', 'تعریف مشتریان', 'deletequery', 'حذف اطلاعات', 'customers', 'id', 1, '1', '-', '2026-09-12', '1789216326244'),
(148, 'admin', 'admin', 'customers.php', 'تعریف مشتریان', 'show', 'نمایش اطلاعات', 'customers', 'id', 1, '0', '-', '2026-09-12', '1789216327201'),
(149, 'admin', 'admin', 'customers.php', 'تعریف مشتریان', 'show', 'نمایش اطلاعات', 'customers', 'id', 1, '0', '-', '2026-09-12', '1789216715375'),
(150, 'admin', 'admin', 'customers.php', 'تعریف مشتریان', 'addform', 'مشاهده فرم ثبت اطلاعات', 'customers', 'id', 1, '0', '-', '2026-09-12', '1789216721506'),
(151, 'admin', 'admin', 'customers.php', 'تعریف مشتریان', 'addform', 'مشاهده فرم ثبت اطلاعات', 'customers', 'id', 1, '0', '-', '2026-09-12', '1789216827786'),
(152, 'admin', 'admin', 'customers.php', 'تعریف مشتریان', 'show', 'نمایش اطلاعات', 'customers', 'id', 1, '0', '-', '2026-09-12', '1789216833838'),
(153, 'admin', 'admin', 'customers.php', 'تعریف مشتریان', 'show', 'نمایش اطلاعات', 'customers', 'id', 1, '0', '-', '2026-09-12', '1789216905002'),
(154, 'admin', 'admin', 'customers.php', 'تعریف مشتریان', 'addform', 'مشاهده فرم ثبت اطلاعات', 'customers', 'id', 1, '0', '-', '2026-09-12', '1789217508012'),
(155, 'admin', 'admin', 'customers.php', 'تعریف مشتریان', 'addquery', 'ثبت اطلاعات جدید', 'customers', 'id', 1, '1', '-', '2026-09-12', '1789217525183'),
(156, 'admin', 'admin', 'customers.php', 'تعریف مشتریان', 'show', 'نمایش اطلاعات', 'customers', 'id', 1, '0', '-', '2026-09-12', '1789217526337'),
(157, 'admin', 'admin', 'customers.php', 'تعریف مشتریان', 'show', 'نمایش اطلاعات', 'customers', 'id', 1, '0', '-', '2026-09-12', '1789217549456'),
(158, 'admin', 'admin', 'customers.php', 'تعریف مشتریان', 'show', 'نمایش اطلاعات', 'customers', 'id', 1, '0', '-', '2026-09-12', '1789217550411');

-- --------------------------------------------------------

--
-- Table structure for table `admin_user`
--

CREATE TABLE `admin_user` (
  `username` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `pass` varchar(500) COLLATE utf8_persian_ci NOT NULL,
  `name` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `family` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `tel` varchar(20) COLLATE utf8_persian_ci NOT NULL,
  `email` varchar(500) COLLATE utf8_persian_ci NOT NULL,
  `active` bit(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `admin_user`
--

INSERT INTO `admin_user` (`username`, `pass`, `name`, `family`, `tel`, `email`, `active`) VALUES
('admin', '44332211', 'admin', 'admin', '123456789', 'email@web.com', b'1');

-- --------------------------------------------------------

--
-- Table structure for table `cafes`
--

CREATE TABLE `cafes` (
  `id` int(11) NOT NULL,
  `title` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `slogan` text COLLATE utf8_persian_ci DEFAULT NULL,
  `tel1` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `tel2` varchar(250) COLLATE utf8_persian_ci DEFAULT NULL,
  `manager_name` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `manager_mobile` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `address` text COLLATE utf8_persian_ci NOT NULL,
  `instagram` varchar(250) COLLATE utf8_persian_ci DEFAULT NULL,
  `working_hours` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `logo` text COLLATE utf8_persian_ci DEFAULT NULL,
  `pass` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `marketer_id` int(11) NOT NULL,
  `register_date` date NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '1=فعال، 0=غیرفعال'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `cafes`
--

INSERT INTO `cafes` (`id`, `title`, `slogan`, `tel1`, `tel2`, `manager_name`, `manager_mobile`, `address`, `instagram`, `working_hours`, `logo`, `pass`, `marketer_id`, `register_date`, `status`) VALUES
(1, 'علی کافه', 'شعار نمیدیم عمل میکنیم', '123513', '3213513', 'مهدی حسینی', '0915555562135', 'شسیشسیشس', 'شسیسشی', '12 الی 14', '../uploads/4229f3f2b4a7e029f566d828510bc70dpexels-worldspectrum-844124.jpg', '123456', 1, '2026-09-12', 1);

-- --------------------------------------------------------

--
-- Table structure for table `cafe_categories`
--

CREATE TABLE `cafe_categories` (
  `id` int(11) NOT NULL,
  `title` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `image` text COLLATE utf8_persian_ci DEFAULT NULL,
  `cafe_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `cafe_categories`
--

INSERT INTO `cafe_categories` (`id`, `title`, `image`, `cafe_id`) VALUES
(1, 'بار سرد', '../uploads/bce8a3a114800dd802708821a7c48f04Untitled.jpg', 1),
(2, 'بار گرم', NULL, 1),
(3, 'صبحانه', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(250) COLLATE utf8_persian_ci DEFAULT NULL,
  `family` varchar(250) COLLATE utf8_persian_ci DEFAULT NULL,
  `tel` varchar(250) COLLATE utf8_persian_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `mili` text COLLATE utf8_persian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `family`, `tel`, `birth_date`, `mili`) VALUES
(1, 'سعید', 'محمدی', '02535132052', '2026-09-12', '2116351351');

-- --------------------------------------------------------

--
-- Table structure for table `marketers`
--

CREATE TABLE `marketers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_persian_ci NOT NULL,
  `family` varchar(100) COLLATE utf8_persian_ci NOT NULL,
  `tel` varchar(20) COLLATE utf8_persian_ci NOT NULL,
  `tel2` varchar(20) COLLATE utf8_persian_ci DEFAULT NULL,
  `pass` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '1=فعال، 0=غیرفعال'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `marketers`
--

INSERT INTO `marketers` (`id`, `name`, `family`, `tel`, `tel2`, `pass`, `status`) VALUES
(1, 'علی', 'حسنی', '9155630215', '0', '1234', 1);

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `title` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `image` text COLLATE utf8_persian_ci DEFAULT NULL,
  `recipe` text COLLATE utf8_persian_ci DEFAULT NULL,
  `price` decimal(15,0) NOT NULL DEFAULT 0,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `title`, `image`, `recipe`, `price`, `category_id`) VALUES
(1, 'شیک وانیل', '../uploads/7c6c8a025c559b1c0deac7d5a6f5beearevolut.jpg', '', '2000000', 2),
(2, 'سشیسشی', NULL, 'طزرطزر', '50000', 3);

-- --------------------------------------------------------

--
-- Table structure for table `mynote`
--

CREATE TABLE `mynote` (
  `id` int(11) NOT NULL,
  `title` varchar(250) COLLATE utf8_persian_ci NOT NULL,
  `txt` text COLLATE utf8_persian_ci DEFAULT NULL,
  `tarikh` date NOT NULL,
  `ordnum` int(11) NOT NULL DEFAULT 0,
  `vaz` int(11) NOT NULL DEFAULT 0,
  `fpage` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `mynote`
--

INSERT INTO `mynote` (`id`, `title`, `txt`, `tarikh`, `ordnum`, `vaz`, `fpage`) VALUES
(2, 'یادآوری', 'متن یادآوری', '2026-03-17', 99, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `id` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `title` varchar(500) COLLATE utf8_persian_ci NOT NULL,
  `rep_task` text COLLATE utf8_persian_ci DEFAULT NULL,
  `count_task` int(11) DEFAULT 0,
  `rep_peygiri` text COLLATE utf8_persian_ci DEFAULT NULL,
  `count_peygiri` int(11) DEFAULT 0,
  `rep_faktor` text COLLATE utf8_persian_ci DEFAULT NULL,
  `count_faktor` int(11) DEFAULT 0,
  `rep_more` text COLLATE utf8_persian_ci DEFAULT NULL,
  `post_date` date NOT NULL,
  `vaz_admin` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_ip`
--

CREATE TABLE `users_ip` (
  `id` int(11) NOT NULL,
  `ip` varchar(50) COLLATE utf8_persian_ci NOT NULL,
  `username` varchar(200) COLLATE utf8_persian_ci DEFAULT NULL,
  `country` int(11) DEFAULT 0,
  `tarikh` date NOT NULL,
  `pages` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `users_ip`
--

INSERT INTO `users_ip` (`id`, `ip`, `username`, `country`, `tarikh`, `pages`) VALUES
(1, '127 0 0 1', 'no no username', 0, '2026-09-12', 6),
(2, '::1', 'no no username', 0, '2026-09-12', 172);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `act_log`
--
ALTER TABLE `act_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_user`
--
ALTER TABLE `admin_user`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `cafes`
--
ALTER TABLE `cafes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `marketer_id` (`marketer_id`);

--
-- Indexes for table `cafe_categories`
--
ALTER TABLE `cafe_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cafe_id` (`cafe_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `marketers`
--
ALTER TABLE `marketers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mynote`
--
ALTER TABLE `mynote`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_ip`
--
ALTER TABLE `users_ip`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `act_log`
--
ALTER TABLE `act_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT for table `cafes`
--
ALTER TABLE `cafes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cafe_categories`
--
ALTER TABLE `cafe_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `marketers`
--
ALTER TABLE `marketers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `mynote`
--
ALTER TABLE `mynote`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `report`
--
ALTER TABLE `report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users_ip`
--
ALTER TABLE `users_ip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
