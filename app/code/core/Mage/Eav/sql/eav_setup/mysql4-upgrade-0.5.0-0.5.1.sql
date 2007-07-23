/*
SQLyog Enterprise - MySQL GUI v5.13
Host - 4.1.22 : Database - magento_dmitriy
*********************************************************************
Server version : 4.1.22
*/

SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

ALTER TABLE `eav_attribute` ADD `attribute_code` VARCHAR( 255 ) NOT NULL AFTER `attribute_name`;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
