-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Gen 10, 2023 alle 15:22
-- Versione del server: 10.4.27-MariaDB
-- Versione PHP: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hangman`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `game`
--

CREATE TABLE `game` (
  `ID_game` int(11) NOT NULL,
  `max_time` tinyint(11) UNSIGNED NOT NULL,
  `max_lives` tinyint(11) UNSIGNED NOT NULL,
  `ID_room` int(11) NOT NULL,
  `ID_word` int(11) NOT NULL,
  `endTimestamp` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `game_partecipation`
--

CREATE TABLE `game_partecipation` (
  `ID_game_partecipation` int(11) NOT NULL,
  `ID_game` int(11) NOT NULL,
  `ID_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `guess`
--

CREATE TABLE `guess` (
  `ID_guess` int(11) NOT NULL,
  `word` varchar(30) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `ID_game_partecipation` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `message`
--

CREATE TABLE `message` (
  `ID_message` int(11) NOT NULL,
  `message` varchar(128) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `ID_user` int(11) NOT NULL,
  `ID_room` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `message`
--

INSERT INTO `message` (`ID_message`, `message`, `timestamp`, `ID_user`, `ID_room`) VALUES
(1, 'fawfawfawfawfaf', '2023-01-09 11:11:54', 9, 17),
(4, 'nuovo messAAGGIOOOOOO', '2023-01-09 11:35:35', 9, 17),
(5, 'ciaooo', '2023-01-09 12:02:35', 8, 17),
(6, 'AFWafwaFAF', '2023-01-09 13:30:05', 8, 17),
(7, 'nuovo messaggio', '2023-01-09 13:38:50', 9, 17),
(8, 'dawdawd', '2023-01-09 13:39:20', 9, 17),
(9, 'fawfawf', '2023-01-09 13:40:43', 8, 17),
(10, 'newmessage', '2023-01-09 14:10:36', 9, 17),
(11, 'NEWMWEADAWD', '2023-01-09 14:11:25', 9, 17),
(12, 'awdfawdawdawd', '2023-01-09 14:29:14', 8, 18),
(13, 'dawfawffw', '2023-01-09 15:21:51', 9, 17),
(14, 'non funziona un cazzo', '2023-01-09 15:22:18', 9, 17),
(15, 'propvaprovaporvaporcva', '2023-01-09 15:23:28', 8, 17),
(16, 'provafinalissima', '2023-01-09 16:12:52', 9, 17),
(22, 'nuovonuoouovoooo', '2023-01-09 16:25:31', 9, 17),
(23, 'faewfw', '2023-01-09 16:56:43', 8, 17),
(24, 'faewfw', '2023-01-09 16:56:43', 8, 17),
(25, 'qw3e4tgq3wfgtq3wafgtw3qg', '2023-01-09 16:58:30', 9, 17),
(26, 'palle', '2023-01-09 17:02:46', 8, 17),
(27, 'palle', '2023-01-09 17:02:46', 8, 17),
(28, 'palle', '2023-01-09 17:02:46', 8, 17),
(29, 'palle', '2023-01-09 17:02:46', 8, 17),
(30, 'palle', '2023-01-09 17:02:46', 8, 17),
(31, 'palle', '2023-01-09 17:02:46', 8, 17),
(32, 'palle', '2023-01-09 17:02:46', 8, 17),
(33, 'palle', '2023-01-09 17:02:46', 8, 17),
(34, 'fafw', '2023-01-09 17:15:49', 10, 18),
(35, 'messaggioooo<br>', '2023-01-09 17:15:58', 10, 18),
(36, 'messaggioooo<br>', '2023-01-09 17:16:37', 10, 18),
(37, 'messaggioooo<br>', '2023-01-09 17:17:29', 10, 18),
(38, 'messaggioooo<br>', '2023-01-09 17:17:31', 10, 18),
(39, 'messaggioooo<br>', '2023-01-09 17:17:32', 10, 18),
(40, 'messaggioooo<br>', '2023-01-09 17:17:32', 10, 18),
(41, 'messaggioooo<br>', '2023-01-09 17:17:33', 10, 18),
(42, 'messaggioooo<br>', '2023-01-09 17:17:33', 10, 18),
(43, 'messaggioooo<br>', '2023-01-09 17:17:33', 10, 18),
(44, 'messaggioooo<br>', '2023-01-09 17:17:33', 10, 18),
(45, 'messaggioooo<br>', '2023-01-09 17:17:33', 10, 18),
(46, 'messaggioooo<br>', '2023-01-09 17:17:33', 10, 18),
(47, 'messaggioooo<br>', '2023-01-09 17:17:34', 10, 18),
(48, 'messaggioooo<br>', '2023-01-09 17:17:34', 10, 18),
(49, 'messaggioooo<br>', '2023-01-09 17:17:34', 10, 18),
(50, 'messaggioooo<br>', '2023-01-09 17:17:34', 10, 18),
(51, 'messaggioooo<br>', '2023-01-09 17:17:34', 10, 18),
(52, 'messaggioooo<br>', '2023-01-09 17:17:34', 10, 18),
(53, 'messaggioooo<br>', '2023-01-09 17:18:31', 10, 18),
(54, 'fsef', '2023-01-09 17:21:18', 10, 18),
(55, 'fsef', '2023-01-09 17:21:30', 10, 18),
(56, 'posso mandare i messaggi ma non riceverli?????<br>', '2023-01-09 17:21:46', 10, 18),
(57, 'problema di browser e di connessioni attive contemporaneamente, non di database<br>', '2023-01-09 17:22:05', 10, 18),
(58, 'ciao', '2023-01-09 17:23:35', 8, 18),
(59, 'fawfawf', '2023-01-09 23:46:34', 9, 17),
(60, 'scrivo', '2023-01-09 23:51:43', 8, 17),
(61, 'scrivo', '2023-01-09 23:51:43', 8, 17),
(62, 'prova', '2023-01-10 00:07:23', 8, 17),
(63, 'numero', '2023-01-10 00:07:40', 8, 17),
(64, 'afwfaw', '2023-01-10 00:15:28', 8, 17),
(65, 'bene', '2023-01-10 11:45:27', 9, 17),
(66, 'nuovissimo messagio<br>', '2023-01-10 11:52:18', 8, 18),
(67, 'ti prego', '2023-01-10 12:01:01', 8, 17),
(68, 'TI PREGO DIO<br>', '2023-01-10 12:46:43', 8, 18),
(76, 'eeeeeeee', '2023-01-10 13:23:14', 12, 18),
(77, 'AAAAAAAAAA', '2023-01-10 13:23:21', 12, 18),
(78, 'NO', '2023-01-10 13:23:32', 8, 18),
(79, 'dwadawd', '2023-01-10 13:24:40', 12, 18),
(80, 'dawd', '2023-01-10 13:24:47', 8, 18);

-- --------------------------------------------------------

--
-- Struttura della tabella `role`
--

CREATE TABLE `role` (
  `name` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `role`
--

INSERT INTO `role` (`name`) VALUES
('admin'),
('user');

-- --------------------------------------------------------

--
-- Struttura della tabella `room`
--

CREATE TABLE `room` (
  `ID_room` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `ID_host` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `room`
--

INSERT INTO `room` (`ID_room`, `name`, `ID_host`) VALUES
(8, 'lampo', 8),
(17, 'addwfwa', 8),
(18, 'addwfwawfwafa', 8),
(20, 'addwfwawfwyhuiafa', 8),
(24, 'addwfwawfwyhdfdfuiafa', 8),
(25, 'prova', 8),
(26, 'suca', 8);

-- --------------------------------------------------------

--
-- Struttura della tabella `room_partecipation`
--

CREATE TABLE `room_partecipation` (
  `ID_user` int(11) NOT NULL,
  `ID_room` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `room_partecipation`
--

INSERT INTO `room_partecipation` (`ID_user`, `ID_room`, `timestamp`) VALUES
(8, 17, '2023-01-10 13:05:50'),
(8, 18, '2023-01-10 13:05:50'),
(8, 20, '2023-01-10 13:05:50'),
(8, 24, '2023-01-10 13:05:50'),
(8, 25, '2023-01-10 13:05:50'),
(8, 26, '2023-01-10 13:05:50'),
(9, 18, '2023-01-10 13:05:50'),
(9, 24, '2023-01-10 13:05:50'),
(10, 17, '2023-01-10 13:05:50'),
(10, 18, '2023-01-10 13:05:50'),
(10, 24, '2023-01-10 13:05:50'),
(12, 18, '2023-01-10 13:12:23');

-- --------------------------------------------------------

--
-- Struttura della tabella `user`
--

CREATE TABLE `user` (
  `ID_user` int(11) NOT NULL,
  `username` varchar(15) NOT NULL,
  `password` varchar(64) NOT NULL,
  `role` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `user`
--

INSERT INTO `user` (`ID_user`, `username`, `password`, `role`) VALUES
(8, 'username', '$2y$10$q.9PhsXhhkcyqcPiK8aqb.9laEGS43c4s5JKyQGz3LxBsgIE7tVJa', 'admin'),
(9, 'account', '$2y$10$D4mGQ76Ty5hFAQJlnwUVXOYxtaQ9O2gzH87NQH6I5kr16iqn5SX9.', 'user'),
(10, 'nuovoutente', '$2y$10$RH5.cbUM7F9Ij9LpPP/JRufhPSgyrpT2NWJ.Vd4gWbOr8EF8P3QXG', 'user'),
(11, 'pincopallo', '$2y$10$47n1PNKmJQPmTDNQBeufb.FGTEZSaYwdVo2Q/WOD.uTzvRGeEE6Mu', 'user'),
(12, 'forse', '$2y$10$kBMHbQqtpdACnEvkfzxwtuqIZxlKFoz37WqhB2wLWUFT466HI08/.', 'user');

-- --------------------------------------------------------

--
-- Struttura della tabella `word`
--

CREATE TABLE `word` (
  `word` varchar(30) NOT NULL,
  `language` varchar(15) NOT NULL,
  `ID_word` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`ID_game`),
  ADD KEY `ID_room` (`ID_room`),
  ADD KEY `ID_word` (`ID_word`);

--
-- Indici per le tabelle `game_partecipation`
--
ALTER TABLE `game_partecipation`
  ADD PRIMARY KEY (`ID_game_partecipation`),
  ADD KEY `ID_user` (`ID_user`),
  ADD KEY `ID_game` (`ID_game`);

--
-- Indici per le tabelle `guess`
--
ALTER TABLE `guess`
  ADD PRIMARY KEY (`ID_guess`),
  ADD KEY `ID_game_partecipation` (`ID_game_partecipation`);

--
-- Indici per le tabelle `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`ID_message`),
  ADD KEY `ID_user` (`ID_user`),
  ADD KEY `ID_room` (`ID_room`);

--
-- Indici per le tabelle `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`name`);

--
-- Indici per le tabelle `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`ID_room`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `ID_host` (`ID_host`);

--
-- Indici per le tabelle `room_partecipation`
--
ALTER TABLE `room_partecipation`
  ADD PRIMARY KEY (`ID_user`,`ID_room`),
  ADD KEY `ID_room` (`ID_room`);

--
-- Indici per le tabelle `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID_user`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `role` (`role`);

--
-- Indici per le tabelle `word`
--
ALTER TABLE `word`
  ADD PRIMARY KEY (`ID_word`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `game`
--
ALTER TABLE `game`
  MODIFY `ID_game` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `game_partecipation`
--
ALTER TABLE `game_partecipation`
  MODIFY `ID_game_partecipation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `guess`
--
ALTER TABLE `guess`
  MODIFY `ID_guess` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `message`
--
ALTER TABLE `message`
  MODIFY `ID_message` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT per la tabella `room`
--
ALTER TABLE `room`
  MODIFY `ID_room` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT per la tabella `user`
--
ALTER TABLE `user`
  MODIFY `ID_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT per la tabella `word`
--
ALTER TABLE `word`
  MODIFY `ID_word` int(11) NOT NULL AUTO_INCREMENT;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `game`
--
ALTER TABLE `game`
  ADD CONSTRAINT `game_ibfk_1` FOREIGN KEY (`ID_room`) REFERENCES `room` (`ID_room`),
  ADD CONSTRAINT `game_ibfk_2` FOREIGN KEY (`ID_word`) REFERENCES `word` (`ID_word`);

--
-- Limiti per la tabella `game_partecipation`
--
ALTER TABLE `game_partecipation`
  ADD CONSTRAINT `game_partecipation_ibfk_1` FOREIGN KEY (`ID_user`) REFERENCES `user` (`ID_user`),
  ADD CONSTRAINT `game_partecipation_ibfk_2` FOREIGN KEY (`ID_game`) REFERENCES `game` (`ID_game`);

--
-- Limiti per la tabella `guess`
--
ALTER TABLE `guess`
  ADD CONSTRAINT `guess_ibfk_1` FOREIGN KEY (`ID_game_partecipation`) REFERENCES `game_partecipation` (`ID_game_partecipation`);

--
-- Limiti per la tabella `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`ID_user`) REFERENCES `user` (`ID_user`),
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`ID_room`) REFERENCES `room` (`ID_room`);

--
-- Limiti per la tabella `room`
--
ALTER TABLE `room`
  ADD CONSTRAINT `room_ibfk_1` FOREIGN KEY (`ID_host`) REFERENCES `user` (`ID_user`);

--
-- Limiti per la tabella `room_partecipation`
--
ALTER TABLE `room_partecipation`
  ADD CONSTRAINT `room_partecipation_ibfk_1` FOREIGN KEY (`ID_user`) REFERENCES `user` (`ID_user`),
  ADD CONSTRAINT `room_partecipation_ibfk_2` FOREIGN KEY (`ID_room`) REFERENCES `room` (`ID_room`),
  ADD CONSTRAINT `room_partecipation_ibfk_3` FOREIGN KEY (`ID_room`) REFERENCES `room` (`ID_room`);

--
-- Limiti per la tabella `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`role`) REFERENCES `role` (`name`),
  ADD CONSTRAINT `user_ibfk_2` FOREIGN KEY (`role`) REFERENCES `role` (`name`),
  ADD CONSTRAINT `user_ibfk_3` FOREIGN KEY (`role`) REFERENCES `role` (`name`),
  ADD CONSTRAINT `user_ibfk_4` FOREIGN KEY (`role`) REFERENCES `role` (`name`),
  ADD CONSTRAINT `user_ibfk_5` FOREIGN KEY (`role`) REFERENCES `role` (`name`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
