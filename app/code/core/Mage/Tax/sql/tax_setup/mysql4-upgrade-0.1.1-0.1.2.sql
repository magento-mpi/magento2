-- MySQL dump 10.11
--
-- Host: localhost    Database: magento
-- ------------------------------------------------------
-- Server version   5.0.42-debug-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

SET FOREIGN_KEY_CHECKS = 0;


DROP TABLE IF EXISTS `tax_class_customer_group`;
DROP TABLE IF EXISTS `tax_class_customer`;
DROP TABLE IF EXISTS `tax_class_product_group`;
DROP TABLE IF EXISTS `tax_class_product`;
DROP TABLE IF EXISTS `tax_rate_value`;

--
-- Table structure for table `tax_class`
--

DROP TABLE IF EXISTS `tax_class`;
CREATE TABLE `tax_class` (
  `class_id` smallint(6) NOT NULL auto_increment,
  `class_name` varchar(255) NOT NULL,
  `class_type` enum('CUSTOMER','PRODUCT') NOT NULL,
  PRIMARY KEY  (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tax_class`
--

LOCK TABLES `tax_class` WRITE;
/*!40000 ALTER TABLE `tax_class` DISABLE KEYS */;
/*!40000 ALTER TABLE `tax_class` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tax_class_group`
--

DROP TABLE IF EXISTS `tax_class_group`;
CREATE TABLE `tax_class_group` (
  `group_id` smallint(6) NOT NULL auto_increment,
  `class_parent_id` smallint(6) NOT NULL,
  `class_group_id` tinyint(3) NOT NULL,
  PRIMARY KEY  (`group_id`),
  KEY `class_parent_id` (`class_parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tax_class_group`
--

LOCK TABLES `tax_class_group` WRITE;
/*!40000 ALTER TABLE `tax_class_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `tax_class_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tax_rate`
--

DROP TABLE IF EXISTS `tax_rate`;
CREATE TABLE `tax_rate` (
  `tax_rate_id` tinyint(4) NOT NULL auto_increment,
  `tax_county_id` smallint(6) default NULL,
  `tax_region_id` mediumint(9) unsigned default NULL,
  `tax_zip_code` varchar(12) default NULL,
  PRIMARY KEY  (`tax_rate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Base tax rates';

--
-- Dumping data for table `tax_rate`
--

LOCK TABLES `tax_rate` WRITE;
/*!40000 ALTER TABLE `tax_rate` DISABLE KEYS */;
/*!40000 ALTER TABLE `tax_rate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tax_rate_data`
--

DROP TABLE IF EXISTS `tax_rate_data`;
CREATE TABLE `tax_rate_data` (
  `tax_rate_data_id` tinyint(4) NOT NULL auto_increment,
  `tax_rate_id` tinyint(4) NOT NULL,
  `rate_value` decimal(12,4) NOT NULL,
  `rate_type_id` tinyint(4) NOT NULL,
  PRIMARY KEY  (`tax_rate_data_id`),
  KEY `rate_id` (`tax_rate_id`),
  KEY `rate_type_id` (`rate_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tax_rate_data`
--

LOCK TABLES `tax_rate_data` WRITE;
/*!40000 ALTER TABLE `tax_rate_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `tax_rate_data` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tax_rate_type`
--

DROP TABLE IF EXISTS `tax_rate_type`;
CREATE TABLE `tax_rate_type` (
  `type_id` tinyint(4) NOT NULL auto_increment,
  `type_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tax_rate_type`
--

LOCK TABLES `tax_rate_type` WRITE;
/*!40000 ALTER TABLE `tax_rate_type` DISABLE KEYS */;
INSERT INTO `tax_rate_type` VALUES (1,'Rate 1'),(2,'Rate 2'),(3,'Rate 3'),(4,'Rate 4'),(5,'Rate 5');
/*!40000 ALTER TABLE `tax_rate_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tax_rule`
--

DROP TABLE IF EXISTS `tax_rule`;
CREATE TABLE `tax_rule` (
  `tax_rule_id` tinyint(4) NOT NULL auto_increment,
  `tax_customer_class_id` smallint(6) NOT NULL,
  `tax_product_class_id` smallint(6) NOT NULL,
  `tax_rate_id` tinyint(4) NOT NULL,
  PRIMARY KEY  (`tax_rule_id`),
  KEY `tax_customer_class_id` (`tax_customer_class_id`,`tax_product_class_id`),
  KEY `tax_customer_class_id_2` (`tax_customer_class_id`),
  KEY `tax_product_class_id` (`tax_product_class_id`),
  KEY `tax_rate_id` (`tax_rate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tax_rule`
--

LOCK TABLES `tax_rule` WRITE;
/*!40000 ALTER TABLE `tax_rule` DISABLE KEYS */;
/*!40000 ALTER TABLE `tax_rule` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

SET FOREIGN_KEY_CHECKS = 1;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2007-07-12  03:15:19
