-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Dic 28, 2022 alle 14:49
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
  `gamemode` varchar(15) NOT NULL,
  `ID_room` int(11) NOT NULL,
  `ID_word` int(11) NOT NULL
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
-- Struttura della tabella `mode`
--

CREATE TABLE `mode` (
  `name` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `room`
--

CREATE TABLE `room` (
  `ID_room` int(11) NOT NULL,
  `name` varchar(32) NOT NULL,
  `password` varchar(15) DEFAULT NULL,
  `max_player` tinyint(11) UNSIGNED NOT NULL,
  `ID_host` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `room_partecipation`
--

CREATE TABLE `room_partecipation` (
  `ID_user` int(11) NOT NULL,
  `ID_room` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `user`
--

CREATE TABLE `user` (
  `ID_user` int(11) NOT NULL,
  `username` varchar(15) NOT NULL,
  `password` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  ADD KEY `ID_word` (`ID_word`),
  ADD KEY `gamemode` (`gamemode`);

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
-- Indici per le tabelle `mode`
--
ALTER TABLE `mode`
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
  ADD UNIQUE KEY `username` (`username`);

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
  MODIFY `ID_message` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `room`
--
ALTER TABLE `room`
  MODIFY `ID_room` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `user`
--
ALTER TABLE `user`
  MODIFY `ID_user` int(11) NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `game_ibfk_2` FOREIGN KEY (`ID_word`) REFERENCES `word` (`ID_word`),
  ADD CONSTRAINT `game_ibfk_3` FOREIGN KEY (`gamemode`) REFERENCES `mode` (`name`);

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
