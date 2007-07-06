-- MySQL dump 10.11
--
-- Host: localhost    Database: magento
-- ------------------------------------------------------
-- Server version	5.0.38-log

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

--
-- Table structure for table `tax_class_customer`
--

DROP TABLE IF EXISTS `tax_class_customer`;
CREATE TABLE `tax_class_customer` (
  `class_customer_id` smallint(6) NOT NULL auto_increment,
  `class_customer_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`class_customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tax_class_customer`
--

LOCK TABLES `tax_class_customer` WRITE;
/*!40000 ALTER TABLE `tax_class_customer` DISABLE KEYS */;
/*!40000 ALTER TABLE `tax_class_customer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tax_class_customer_group`
--

DROP TABLE IF EXISTS `tax_class_customer_group`;
CREATE TABLE `tax_class_customer_group` (
  `class_customer_id` smallint(6) NOT NULL,
  `class_group_id` tinyint(3) NOT NULL,
  KEY `class_customer_id` (`class_customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tax_class_customer_group`
--

LOCK TABLES `tax_class_customer_group` WRITE;
/*!40000 ALTER TABLE `tax_class_customer_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `tax_class_customer_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tax_class_product`
--

DROP TABLE IF EXISTS `tax_class_product`;
CREATE TABLE `tax_class_product` (
  `class_product_id` smallint(6) NOT NULL auto_increment,
  `class_product_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`class_product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tax_class_product`
--

LOCK TABLES `tax_class_product` WRITE;
/*!40000 ALTER TABLE `tax_class_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `tax_class_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tax_class_product_group`
--

DROP TABLE IF EXISTS `tax_class_product_group`;
CREATE TABLE `tax_class_product_group` (
  `class_id` smallint(6) NOT NULL,
  `class_category_id` mediumint(9) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tax_class_product_group`
--

LOCK TABLES `tax_class_product_group` WRITE;
/*!40000 ALTER TABLE `tax_class_product_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `tax_class_product_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tax_rate`
--

DROP TABLE IF EXISTS `tax_rate`;
CREATE TABLE `tax_rate` (
  `tax_rate_id` int(11) NOT NULL auto_increment,
  `tax_country_id` smallint(6) default NULL,
  `tax_region_id` mediumint(9) UNSIGNED default NULL,
  `tax_zip_code` varchar(12) default NULL,
  PRIMARY KEY  (`tax_rate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Base tax rates';

--
-- Dumping data for table `tax_rate`
--

LOCK TABLES `tax_rate` WRITE;
/*!40000 ALTER TABLE `tax_rate` DISABLE KEYS */;
/*!40000 ALTER TABLE `tax_rate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tax_rate_value`
--

DROP TABLE IF EXISTS `tax_rate_value`;
CREATE TABLE `tax_rate_value` (
  `value_id` int(11) NOT NULL auto_increment,
  `rate_id` int(11) NOT NULL,
  `value_name` varchar(255) NOT NULL,
  `value_rate` decimal(12,4) NOT NULL,
  PRIMARY KEY  (`value_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tax_rate_value`
--

LOCK TABLES `tax_rate_value` WRITE;
/*!40000 ALTER TABLE `tax_rate_value` DISABLE KEYS */;
/*!40000 ALTER TABLE `tax_rate_value` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tax_rule`
--

DROP TABLE IF EXISTS `tax_rule`;
CREATE TABLE `tax_rule` (
  `tax_rule_id` smallint(6) NOT NULL auto_increment,
  `tax_customer_class_id` smallint(6) NOT NULL,
  `tax_product_class_id` smallint(6) NOT NULL,
  `tax_rate_value_id` int(11) NOT NULL,
  `tax_rule_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`tax_rule_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tax_rule`
--

LOCK TABLES `tax_rule` WRITE;
/*!40000 ALTER TABLE `tax_rule` DISABLE KEYS */;
/*!40000 ALTER TABLE `tax_rule` ENABLE KEYS */;
UNLOCK TABLES;

ALTER TABLE tax_rule
  ADD CONSTRAINT FK_RULE_PARENT_CUSTOMER_CLASS FOREIGN KEY (tax_customer_class_id) REFERENCES tax_class_customer_group (class_customer_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE tax_rule
  ADD CONSTRAINT FK_RULE_PARENT_PRODUCT_CLASS FOREIGN KEY (tax_product_class_id) REFERENCES tax_class_product_group (class_product_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE tax_rate_value
  ADD CONSTRAINT FK_RATE_VALUE_PARENT FOREIGN KEY (rate_id) REFERENCES tax_rate (tax_rate_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE tax_rate
  ADD CONSTRAINT FK_RATE_PARENT_COUNTRY FOREIGN KEY (tax_country_id) REFERENCES directory_country (country_id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE tax_rate
  ADD CONSTRAINT FK_RATE_PARENT_REGION FOREIGN KEY (tax_region_id) REFERENCES directory_country_region (region_id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE tax_class_product_group
  ADD CONSTRAINT FK_TAX_CLASS_PRODUCT_GROUP_PARENT FOREIGN KEY (class_id) REFERENCES tax_class_product (class_product_id) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE tax_class_customer_group
  ADD CONSTRAINT FK_TAX_CLASS_CUSTOMER_GROUP_PARENT FOREIGN KEY (class_customer_id) REFERENCES tax_class_customer (class_customer_id) ON DELETE CASCADE ON UPDATE CASCADE;


/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2007-07-05 12:22:05
