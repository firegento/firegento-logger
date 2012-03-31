CREATE DATABASE `jaxl` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

USE `jaxl`;

CREATE TABLE IF NOT EXISTS `message` (
  `Id` int(11) NOT NULL auto_increment,
  `FromJid` varchar(128) collate utf8_unicode_ci NOT NULL,
  `Message` varchar(128) collate utf8_unicode_ci NOT NULL,
  `Timestamp` datetime NOT NULL,
  PRIMARY KEY  (`Id`),
  KEY `FromJid` (`FromJid`,`Message`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `presence` (
  `Id` int(11) NOT NULL auto_increment,
  `FromJid` varchar(128) collate utf8_unicode_ci NOT NULL,
  `Status` varchar(128) collate utf8_unicode_ci NOT NULL,
  `Timestamp` datetime NOT NULL,
  PRIMARY KEY  (`Id`),
  KEY `FromJid` (`FromJid`,`Status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
