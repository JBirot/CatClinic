-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Sam 17 Janvier 2015 à 13:57
-- Version du serveur: 5.5.40
-- Version de PHP: 5.4.36-0+deb7u3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: 'catclinic'
--

-- --------------------------------------------------------

--
-- Structure de la table 'article'
--

DROP TABLE IF EXISTS article;
CREATE TABLE article (
  id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  titre varchar(100) NOT NULL,
  texte text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  auteur smallint(5) unsigned NOT NULL,
  categorie smallint(5) unsigned NOT NULL,
  en_ligne tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY auteur (auteur),
  KEY categorie (categorie)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- RELATIONS POUR LA TABLE article:
--   auteur
--       auteur -> id
--   categorie
--       categorie -> id
--

-- --------------------------------------------------------

--
-- Structure de la table 'auteur'
--

DROP TABLE IF EXISTS auteur;
CREATE TABLE auteur (
  id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  nom varchar(20) NOT NULL,
  prenom varchar(20) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY nom (nom,prenom)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table 'categorie'
--

DROP TABLE IF EXISTS categorie;
CREATE TABLE categorie (
  id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  titre varchar(100) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY titre (titre)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table 'chat'
--

DROP TABLE IF EXISTS chat;
CREATE TABLE chat (
  id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  nom varchar(30) NOT NULL,
  age tinyint(3) unsigned NOT NULL,
  tatouage varchar(10) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY tatouage (tatouage)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table 'praticien'
--

DROP TABLE IF EXISTS praticien;
CREATE TABLE praticien (
  id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  nom varchar(30) NOT NULL,
  prenom varchar(30) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table 'proprietaire'
--

DROP TABLE IF EXISTS proprietaire;
CREATE TABLE proprietaire (
  id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  nom varchar(30) NOT NULL,
  prenom varchar(30) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table 'proprietaire_chat'
--

DROP TABLE IF EXISTS proprietaire_chat;
CREATE TABLE proprietaire_chat (
  id_proprietaire smallint(5) unsigned NOT NULL DEFAULT '0',
  id_chat smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (id_proprietaire,id_chat),
  KEY FK_proprietaire_chat_chat (id_chat)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- RELATIONS POUR LA TABLE proprietaire_chat:
--   id_chat
--       chat -> id
--   id_proprietaire
--       proprietaire -> id
--

-- --------------------------------------------------------

--
-- Structure de la table 'utilisateur'
--

DROP TABLE IF EXISTS utilisateur;
CREATE TABLE utilisateur (
  id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  login varchar(10) NOT NULL,
  motdepasse char(40) NOT NULL,
  admin tinyint(3) unsigned NOT NULL DEFAULT '0',
  id_proprietaire smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY login (login),
  KEY FK_propr_user (id_proprietaire)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- RELATIONS POUR LA TABLE utilisateur:
--   id_proprietaire
--       proprietaire -> id
--

-- --------------------------------------------------------

--
-- Structure de la table 'visite'
--

DROP TABLE IF EXISTS visite;
CREATE TABLE visite (
  id smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  id_praticien smallint(5) unsigned DEFAULT NULL,
  id_chat smallint(5) unsigned DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  prix float(6,2) unsigned NOT NULL,
  observations tinytext,
  PRIMARY KEY (id),
  KEY FK_visite_chat (id_chat),
  KEY FK_visite_prat (id_praticien)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- RELATIONS POUR LA TABLE visite:
--   id_chat
--       chat -> id
--   id_praticien
--       praticien -> id
--

--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `article`
--
ALTER TABLE `article`
  ADD CONSTRAINT article_ibfk_1 FOREIGN KEY (auteur) REFERENCES auteur (id) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT article_ibfk_2 FOREIGN KEY (categorie) REFERENCES categorie (id) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `proprietaire_chat`
--
ALTER TABLE `proprietaire_chat`
  ADD CONSTRAINT FK_proprietaire_chat_chat FOREIGN KEY (id_chat) REFERENCES chat (id) ON DELETE CASCADE,
  ADD CONSTRAINT FK_proprietaire_chat_proprietaire FOREIGN KEY (id_proprietaire) REFERENCES proprietaire (id) ON DELETE CASCADE;

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT FK_propr_user FOREIGN KEY (id_proprietaire) REFERENCES proprietaire (id) ON DELETE CASCADE;

--
-- Contraintes pour la table `visite`
--
ALTER TABLE `visite`
  ADD CONSTRAINT FK_visite_chat FOREIGN KEY (id_chat) REFERENCES chat (id) ON DELETE CASCADE,
  ADD CONSTRAINT FK_visite_prat FOREIGN KEY (id_praticien) REFERENCES praticien (id);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
