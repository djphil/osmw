SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `cheminAppli` varchar(50) NOT NULL,
  `destinataire` varchar(50) NOT NULL,
  `Autorized` int(2) NOT NULL,
  `NbAutorized` int(2) NOT NULL,
  `VersionOSMW` varchar(50) NOT NULL,
  `urlOSMW` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `config` (`id`, `cheminAppli`, `destinataire`, `Autorized`, `NbAutorized`, `VersionOSMW`, `urlOSMW`) VALUES
(1, '/osmw/', 'contact@mail.com', 1, 5, '6.0', '/osmw/');

CREATE TABLE `inventaire` (
  `id` int(10) NOT NULL,
  `uuid_parent` varchar(50) NOT NULL,
  `type` varchar(50) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `region` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

CREATE TABLE `moteurs` (
  `osAutorise` tinyint(4) NOT NULL,
  `id_os` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `version` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL,
  `DB_OS` varchar(50) NOT NULL,
  `hypergrid` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `moteurs` (`osAutorise`, `id_os`, `name`, `version`, `address`, `DB_OS`, `hypergrid`) VALUES
(1, 'Opensim_1', 'MyGridName', 'Opensim 0.9.1.1', 'C:/OpenSimulator/opensim/', 'OpensimDB', 'hg.domain.com:80');

CREATE TABLE `npc` (
  `id` int(10) NOT NULL,
  `uuid_npc` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `region` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `osnpc_terminals` (
  `id` int(10) NOT NULL,
  `uuid` varchar(36) NOT NULL DEFAULT '0',
  `region` varchar(64) NOT NULL DEFAULT '0',
  `server_url` varchar(256) NOT NULL,
  `server_uuid` varchar(36) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(15) NOT NULL,
  `lastname` varchar(15) NOT NULL,
  `password` text NOT NULL,
  `privilege` int(11) NOT NULL,
  `osAutorise` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `users` (`id`, `firstname`, `lastname`, `password`, `privilege`, `osAutorise`) VALUES
(1, 'Super', 'Admin', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 4, '');


ALTER TABLE `config`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `inventaire`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `moteurs`
  ADD PRIMARY KEY (`osAutorise`);

ALTER TABLE `npc`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `osnpc_terminals`
  ADD KEY `Index 1` (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `inventaire`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `moteurs`
  MODIFY `osAutorise` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

ALTER TABLE `npc`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `osnpc_terminals`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
