-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 08. Jan 2016 um 10:07
-- Server Version: 5.5.27
-- PHP-Version: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `dropfile`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `autor`
--

CREATE TABLE IF NOT EXISTS `autor` (
  `name` varchar(46) DEFAULT NULL,
  `gnd` varchar(10) DEFAULT NULL,
  `C` varchar(26) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `drucker_verleger`
--

CREATE TABLE IF NOT EXISTS `drucker_verleger` (
  `drucker_verleger` varchar(42) DEFAULT NULL,
  `gnd` varchar(10) DEFAULT NULL,
  `C` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `form`
--

CREATE TABLE IF NOT EXISTS `form` (
  `form` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `format`
--

CREATE TABLE IF NOT EXISTS `format` (
  `format` varchar(8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gattungsbegriff`
--

CREATE TABLE IF NOT EXISTS `gattungsbegriff` (
  `aad_gattungsbegriff` varchar(38) DEFAULT NULL,
  `ppn` varchar(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `medium`
--

CREATE TABLE IF NOT EXISTS `medium` (
  `medium` varchar(11) DEFAULT NULL,
  `B` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `nachweis`
--

CREATE TABLE IF NOT EXISTS `nachweis` (
  `katalog` varchar(13) DEFAULT NULL,
  `name_katalog` varchar(64) DEFAULT NULL,
  `url_katalog` varchar(66) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ort`
--

CREATE TABLE IF NOT EXISTS `ort` (
  `ort` varchar(22) DEFAULT NULL,
  `x` varchar(9) DEFAULT NULL,
  `y` varchar(9) DEFAULT NULL,
  `tgn` int(7) DEFAULT NULL,
  `E` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `qualitaet_erfassung`
--

CREATE TABLE IF NOT EXISTS `qualitaet_erfassung` (
  `kuerzel` varchar(1) DEFAULT NULL,
  `ebene` varchar(13) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sachbegriff`
--

CREATE TABLE IF NOT EXISTS `sachbegriff` (
  `aad_sachbegriff` varchar(14) DEFAULT NULL,
  `ppn` varchar(9) DEFAULT NULL,
  `C` varchar(10) DEFAULT NULL,
  `D` varchar(10) DEFAULT NULL,
  `E` varchar(10) DEFAULT NULL,
  `F` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sprache`
--

CREATE TABLE IF NOT EXISTS `sprache` (
  `iso_639_2_B` varchar(3) DEFAULT NULL,
  `B` varchar(144) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `zusammenfassung`
--

CREATE TABLE IF NOT EXISTS `zusammenfassung` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image` int(5) DEFAULT NULL,
  `seite` int(2) DEFAULT NULL,
  `nr` varchar(5) DEFAULT NULL,
  `qualitaet` varchar(1) DEFAULT NULL,
  `autor` varchar(46) DEFAULT NULL,
  `titel_vorlage` varchar(127) DEFAULT NULL,
  `titel_bibliographiert` varchar(1185) DEFAULT NULL,
  `ort` varchar(22) DEFAULT NULL,
  `drucker_verleger` varchar(42) DEFAULT NULL,
  `jahr` varchar(16) DEFAULT NULL,
  `format` varchar(8) DEFAULT NULL,
  `sachgruppe_historisch` varchar(29) DEFAULT NULL,
  `sachbegriff` varchar(14) DEFAULT NULL,
  `gattungsbegriff` varchar(28) DEFAULT NULL,
  `medium` varchar(5) DEFAULT NULL,
  `sprache` varchar(3) DEFAULT NULL,
  `sprache2` varchar(3) DEFAULT NULL,
  `nachweis` varchar(13) DEFAULT NULL,
  `datensatz` varchar(115) DEFAULT NULL,
  `form` varchar(66) DEFAULT NULL,
  `freitext` varchar(894) DEFAULT NULL,
  `digital` varchar(103) DEFAULT NULL,
  `onlinebiographien` varchar(56) DEFAULT NULL,
  `beteiligte_person` varchar(31) DEFAULT NULL,
  `beteiligte_person2` varchar(37) DEFAULT NULL,
  `beteiligte_person3` varchar(28) DEFAULT NULL,
  `beteiligte_person4` varchar(37) DEFAULT NULL,
  `autor2` varchar(23) DEFAULT NULL,
  `autor3` varchar(19) DEFAULT NULL,
  `autor4` varchar(16) DEFAULT NULL,
  `drucker_verleger2` varchar(32) DEFAULT NULL,
  `ort2` varchar(12) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1178 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
