-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Jeu 27 Août 2015 à 23:50
-- Version du serveur :  5.6.21
-- Version de PHP :  5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `managerweb`
--

-- --------------------------------------------------------

--
-- Structure de la table `config`
--

CREATE TABLE IF NOT EXISTS `config` (
`id` int(11) NOT NULL,
  `cheminAppli` varchar(50) NOT NULL,
  `destinataire` varchar(50) NOT NULL,
  `Autorized` int(2) NOT NULL,
  `NbAutorized` int(2) NOT NULL,
  `VersionOSMW` varchar(50) NOT NULL,
  `urlOSMW` varchar(255) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `config`
--

INSERT INTO `config` (`id`, `cheminAppli`, `destinataire`, `Autorized`, `NbAutorized`, `VersionOSMW`, `urlOSMW`) VALUES
(1, '/osmw/', 'mail@google.com', 1, 5, 'v5.0', '/osmw/');

-- --------------------------------------------------------

--
-- Structure de la table `moteurs`
--

CREATE TABLE IF NOT EXISTS `moteurs` (
`osAutorise` tinyint(4) NOT NULL,
  `id_os` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `version` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL,
  `DB_OS` varchar(50) NOT NULL,
  `hypergrid` varchar(255) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `moteurs`
--

INSERT INTO `moteurs` (`osAutorise`, `id_os`, `name`, `version`, `address`, `DB_OS`, `hypergrid`) VALUES
(1, 'mygrid', 'mygrid', 'v0.8.2 (Dev)', 'C:/opensim/mygrid/bin/', 'mygrid', 'mygrid.com:8002'),

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`id` int(11) NOT NULL,
  `firstname` varchar(15) NOT NULL,
  `lastname` varchar(15) NOT NULL,
  `password` text NOT NULL,
  `privilege` int(11) NOT NULL,
  `osAutorise` varchar(50) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `password`, `privilege`, `osAutorise`) VALUES
(1, 'super', 'admin', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 4, '1|2|3|4|5|'),

--
-- Index pour les tables exportées
--

--
-- Index pour la table `config`
--
ALTER TABLE `config`
 ADD PRIMARY KEY (`id`);

--
-- Index pour la table `moteurs`
--
ALTER TABLE `moteurs`
 ADD PRIMARY KEY (`osAutorise`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `config`
--
ALTER TABLE `config`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `moteurs`
--
ALTER TABLE `moteurs`
MODIFY `osAutorise` tinyint(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
