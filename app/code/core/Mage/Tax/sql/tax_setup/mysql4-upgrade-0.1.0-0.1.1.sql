/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DROP TABLE IF EXISTS `tax_class_customer`;

DROP TABLE IF EXISTS `tax_class_customer_group`;

DROP TABLE IF EXISTS `tax_class_product`;

DROP TABLE IF EXISTS `tax_class_product_group`;

DROP TABLE IF EXISTS `tax_rate`;

DROP TABLE IF EXISTS `tax_rate_value`;

DROP TABLE IF EXISTS `tax_rule`;

CREATE TABLE `tax_class` (
  `class_id` smallint(6) NOT NULL auto_increment,
  `class_name` varchar(255) NOT NULL,
  `class_type` enum('CUSTOMER','PRODUCT') NOT NULL,
  PRIMARY KEY  (`class_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

LOCK TABLES `tax_class` WRITE;
/*!40000 ALTER TABLE `tax_class` DISABLE KEYS */;
INSERT INTO `tax_class` VALUES (1,'Test','CUSTOMER'),(2,'new','CUSTOMER'),(3,'One more','CUSTOMER'),(4,'Test product class name','PRODUCT');
/*!40000 ALTER TABLE `tax_class` ENABLE KEYS */;
UNLOCK TABLES;

CREATE TABLE `tax_class_group` (
  `group_id` smallint(6) NOT NULL auto_increment,
  `class_parent_id` smallint(6) NOT NULL,
  `class_group_id` tinyint(3) NOT NULL,
  PRIMARY KEY  (`group_id`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

LOCK TABLES `tax_class_group` WRITE;
/*!40000 ALTER TABLE `tax_class_group` DISABLE KEYS */;
INSERT INTO `tax_class_group` VALUES (1,1,3),(17,1,1),(6,2,2),(7,2,1),(8,2,3),(18,3,1),(19,3,2),(11,1,2),(20,3,3),(21,4,3),(22,4,12),(23,4,4),(24,4,10),(26,4,2);
/*!40000 ALTER TABLE `tax_class_group` ENABLE KEYS */;
UNLOCK TABLES;

CREATE TABLE `tax_rate` (
  `rate_id` smallint(6) NOT NULL auto_increment,
  `rate_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`rate_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

LOCK TABLES `tax_rate` WRITE;
/*!40000 ALTER TABLE `tax_rate` DISABLE KEYS */;
INSERT INTO `tax_rate` VALUES (1,'Rate 1'),(2,'Rate 2'),(3,'Rate 3'),(4,'Rate 4'),(5,'Rate 5');
/*!40000 ALTER TABLE `tax_rate` ENABLE KEYS */;
UNLOCK TABLES;

CREATE TABLE `tax_rate_data` (
  `tax_rate_id` int(11) NOT NULL auto_increment,
  `tax_country_id` smallint(6) default NULL,
  `tax_region_id` mediumint(9) unsigned default NULL,
  `tax_zip_code` varchar(12) default NULL,
  PRIMARY KEY  (`tax_rate_id`),
  KEY `FK_RATE_PARENT_COUNTRY` (`tax_country_id`),
  KEY `FK_RATE_PARENT_REGION` (`tax_region_id`),
  CONSTRAINT `FK_RATE_PARENT_COUNTRY` FOREIGN KEY (`tax_country_id`) REFERENCES `directory_country` (`country_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_RATE_PARENT_REGION` FOREIGN KEY (`tax_region_id`) REFERENCES `directory_country_region` (`region_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Base tax rates';


LOCK TABLES `tax_rate_data` WRITE;
/*!40000 ALTER TABLE `tax_rate_data` DISABLE KEYS */;
/*!40000 ALTER TABLE `tax_rate_data` ENABLE KEYS */;
UNLOCK TABLES;

CREATE TABLE `tax_rule` (
  `tax_rule_id` smallint(6) NOT NULL auto_increment,
  `tax_customer_class_id` smallint(6) NOT NULL,
  `tax_product_class_id` smallint(6) NOT NULL,
  `tax_rate_value_id` int(11) NOT NULL,
  `tax_rule_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`tax_rule_id`),
  KEY `FK_RULE_PARENT_CUSTOMER_CLASS` (`tax_customer_class_id`),
  CONSTRAINT `FK_RULE_PARENT_CUSTOMER_CLASS` FOREIGN KEY (`tax_customer_class_id`) REFERENCES `tax_class_group` (`class_customer_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;