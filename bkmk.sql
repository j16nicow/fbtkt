-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2019 年 4 朁E04 日 14:14
-- サーバのバージョン： 10.1.34-MariaDB
-- PHP Version: 5.6.37

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `agnaktordb`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `bkmk`
--

CREATE TABLE `bkmk` (
  `id` int(11) NOT NULL,
  `KeyCode` text NOT NULL,
  `Category` text NOT NULL,
  `ItmName0` text NOT NULL,
  `Genre0` text NOT NULL,
  `Number0` int(11) NOT NULL,
  `ItmName1` text NOT NULL,
  `Genre1` text NOT NULL,
  `Number1` int(11) NOT NULL,
  `ItmName2` text NOT NULL,
  `Genre2` text NOT NULL,
  `Number2` int(11) NOT NULL,
  `ItmName3` text NOT NULL,
  `Genre3` text NOT NULL,
  `Number3` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bkmk`
--
ALTER TABLE `bkmk`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bkmk`
--
ALTER TABLE `bkmk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
