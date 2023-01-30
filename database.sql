-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Gen 23, 2023 alle 23:29
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
  `max_lives` int(11) UNSIGNED NOT NULL,
  `ID_room` int(11) NOT NULL,
  `ID_word` int(11) NOT NULL,
  `endTimestamp` timestamp NULL DEFAULT NULL,
  `turnPlayerID` int(11) DEFAULT NULL,
  `wordMask` int(30) NOT NULL DEFAULT 0
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

-- --------------------------------------------------------

--
-- Struttura della tabella `room_partecipation`
--

CREATE TABLE `room_partecipation` (
  `ID_user` int(11) NOT NULL,
  `ID_room` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(8, 'username', '$2y$10$q.9PhsXhhkcyqcPiK8aqb.9laEGS43c4s5JKyQGz3LxBsgIE7tVJa', 'admin');

-- --------------------------------------------------------

--
-- Struttura della tabella `word`
--

CREATE TABLE `word` (
  `word` varchar(30) NOT NULL,
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
  ADD KEY `ID_word` (`ID_word`),
  ADD KEY `game_ibfk_1` (`ID_room`),
  ADD KEY `game_ibfk_3` (`turnPlayerID`);

--
-- Indici per le tabelle `game_partecipation`
--
ALTER TABLE `game_partecipation`
  ADD PRIMARY KEY (`ID_game_partecipation`),
  ADD KEY `game_partecipation_ibfk_1` (`ID_user`),
  ADD KEY `game_partecipation_ibfk_2` (`ID_game`);

--
-- Indici per le tabelle `guess`
--
ALTER TABLE `guess`
  ADD PRIMARY KEY (`ID_guess`),
  ADD KEY `guess_ibfk_1` (`ID_game_partecipation`);

--
-- Indici per le tabelle `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`ID_message`),
  ADD KEY `ID_user` (`ID_user`),
  ADD KEY `message_ibfk_2` (`ID_room`);

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
  ADD KEY `room_partecipation_ibfk_2` (`ID_room`);

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
  ADD PRIMARY KEY (`ID_word`),
  ADD UNIQUE KEY `word` (`word`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `game`
--
ALTER TABLE `game`
  MODIFY `ID_game` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT per la tabella `game_partecipation`
--
ALTER TABLE `game_partecipation`
  MODIFY `ID_game_partecipation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=688;

--
-- AUTO_INCREMENT per la tabella `guess`
--
ALTER TABLE `guess`
  MODIFY `ID_guess` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=421;

--
-- AUTO_INCREMENT per la tabella `message`
--
ALTER TABLE `message`
  MODIFY `ID_message` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT per la tabella `room`
--
ALTER TABLE `room`
  MODIFY `ID_room` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT per la tabella `user`
--
ALTER TABLE `user`
  MODIFY `ID_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT per la tabella `word`
--
ALTER TABLE `word`
  MODIFY `ID_word` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `game`
--
ALTER TABLE `game`
  ADD CONSTRAINT `game_ibfk_1` FOREIGN KEY (`ID_room`) REFERENCES `room` (`ID_room`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_ibfk_3` FOREIGN KEY (`turnPlayerID`) REFERENCES `user` (`ID_user`) ON DELETE SET NULL;

--
-- Limiti per la tabella `game_partecipation`
--
ALTER TABLE `game_partecipation`
  ADD CONSTRAINT `game_partecipation_ibfk_1` FOREIGN KEY (`ID_user`) REFERENCES `user` (`ID_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_partecipation_ibfk_2` FOREIGN KEY (`ID_game`) REFERENCES `game` (`ID_game`) ON DELETE CASCADE;

--
-- Limiti per la tabella `guess`
--
ALTER TABLE `guess`
  ADD CONSTRAINT `guess_ibfk_1` FOREIGN KEY (`ID_game_partecipation`) REFERENCES `game_partecipation` (`ID_game_partecipation`) ON DELETE CASCADE;

--
-- Limiti per la tabella `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_2` FOREIGN KEY (`ID_room`) REFERENCES `room` (`ID_room`) ON DELETE CASCADE;

--
-- Limiti per la tabella `room_partecipation`
--
ALTER TABLE `room_partecipation`
  ADD CONSTRAINT `room_partecipation_ibfk_1` FOREIGN KEY (`ID_user`) REFERENCES `user` (`ID_user`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_partecipation_ibfk_2` FOREIGN KEY (`ID_room`) REFERENCES `room` (`ID_room`) ON DELETE CASCADE;

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
