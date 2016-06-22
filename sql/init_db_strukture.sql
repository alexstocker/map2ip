-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u3
-- Server Version: 5.5.49

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `ip` (
  `ipID` int(11) NOT NULL AUTO_INCREMENT,
  `ipv4` varchar(15) NOT NULL DEFAULT '000.000.000.000',
  `ipv6` varchar(39) NOT NULL DEFAULT '2001:0000:0000:0000:0000:0000:0000:0000',
  `sid` int(2) NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ipID`),
  KEY `sid` (`sid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2010 ;

CREATE TABLE IF NOT EXISTS `logs` (
  `logID` int(11) NOT NULL AUTO_INCREMENT,
  `ipID` int(11) NOT NULL,
  `sID` int(11) NOT NULL,
  `address0` text,
  `address1` text,
  `address2` text,
  `city` varchar(54) DEFAULT NULL,
  `country` varchar(54) DEFAULT NULL,
  `lat` varchar(254) NOT NULL DEFAULT '0.0',
  `lng` varchar(254) NOT NULL DEFAULT '0.0',
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`logID`),
  KEY `ipID` (`ipID`),
  KEY `sID` (`sID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8145 ;

CREATE TABLE IF NOT EXISTS `services` (
  `sID` int(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

ALTER TABLE `ip`
  ADD CONSTRAINT `ip_ibfk_1` FOREIGN KEY (`sid`) REFERENCES `services` (`sID`) ON DELETE CASCADE ON UPDATE NO ACTION;

ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`ipID`) REFERENCES `ip` (`ipID`),
  ADD CONSTRAINT `logs_ibfk_2` FOREIGN KEY (`sID`) REFERENCES `services` (`sID`);
