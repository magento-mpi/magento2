/*
SQLyog Enterprise - MySQL GUI v5.13
Host - 4.1.21-community-nt : Database - magenta
*********************************************************************
Server version : 4.1.21-community-nt
*/

SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `directory_country` */

DROP TABLE IF EXISTS `directory_country`;

CREATE TABLE `directory_country` (
  `country_id` smallint(6) NOT NULL auto_increment,
  `currency_id` smallint(6) unsigned default NULL,
  `country_iso_code` char(2) NOT NULL default '',
  PRIMARY KEY  (`country_id`),
  KEY `FK_COUNTRY_DEFAULT_CURRENCY` (`currency_id`),
  CONSTRAINT `FK_COUNTRY_DEFAULT_CURRENCY` FOREIGN KEY (`currency_id`) REFERENCES `directory_currency` (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Countries';

/*Data for the table `directory_country` */

insert into `directory_country` (`country_id`,`currency_id`,`country_iso_code`) values (1,1,'AF'),(2,1,'AL'),(3,1,'DZ'),(4,1,'AS'),(5,1,'AD'),(6,1,'AO'),(7,1,'AI'),(8,1,'AQ'),(9,1,'AG'),(10,1,'AR'),(11,1,'AM'),(12,1,'AW'),(13,1,'AU'),(14,1,'AT'),(15,1,'AZ'),(16,1,'BS'),(17,1,'BH'),(18,1,'BD'),(19,1,'BB'),(20,1,'BY'),(21,1,'BE'),(22,1,'BZ'),(23,1,'BJ'),(24,1,'BM'),(25,1,'BT'),(26,1,'BO'),(27,1,'BA'),(28,1,'BW'),(29,1,'BV'),(30,1,'BR'),(31,1,'IO'),(32,1,'BN'),(33,1,'BG'),(34,1,'BF'),(35,1,'BI'),(36,1,'KH'),(37,1,'CM'),(38,1,'CA'),(39,1,'CV'),(40,1,'KY'),(41,1,'CF'),(42,1,'TD'),(43,1,'CL'),(44,1,'CN'),(45,1,'CX'),(46,1,'CC'),(47,1,'CO'),(48,1,'KM'),(49,1,'CG'),(50,1,'CK'),(51,1,'CR'),(52,1,'CI'),(53,1,'HR'),(54,1,'CU'),(55,1,'CY'),(56,1,'CZ'),(57,1,'DK'),(58,1,'DJ'),(59,1,'DM'),(60,1,'DO'),(61,1,'TP'),(62,1,'EC'),(63,1,'EG'),(64,1,'SV'),(65,1,'GQ'),(66,1,'ER'),(67,1,'EE'),(68,1,'ET'),(69,1,'FK'),(70,1,'FO'),(71,1,'FJ'),(72,1,'FI'),(73,1,'FR'),(74,1,'FX'),(75,1,'GF'),(76,1,'PF'),(77,1,'TF'),(78,1,'GA'),(79,1,'GM'),(80,1,'GE'),(81,1,'DE'),(82,1,'GH'),(83,1,'GI'),(84,1,'GR'),(85,1,'GL'),(86,1,'GD'),(87,1,'GP'),(88,1,'GU'),(89,1,'GT'),(90,1,'GN'),(91,1,'GW'),(92,1,'GY'),(93,1,'HT'),(94,1,'HM'),(95,1,'HN'),(96,1,'HK'),(97,1,'HU'),(98,1,'IS'),(99,1,'IN'),(100,1,'ID'),(101,1,'IR'),(102,1,'IQ'),(103,1,'IE'),(104,1,'IL'),(105,1,'IT'),(106,1,'JM'),(107,1,'JP'),(108,1,'JO'),(109,1,'KZ'),(110,1,'KE'),(111,1,'KI'),(112,1,'KP'),(113,1,'KR'),(114,1,'KW'),(115,1,'KG');
insert into `directory_country` (`country_id`,`currency_id`,`country_iso_code`) values (116,1,'LA'),(117,1,'LV'),(118,1,'LB'),(119,1,'LS'),(120,1,'LR'),(121,1,'LY'),(122,1,'LI'),(123,1,'LT'),(124,1,'LU'),(125,1,'MO'),(126,1,'MK'),(127,1,'MG'),(128,1,'MW'),(129,1,'MY'),(130,1,'MV'),(131,1,'ML'),(132,1,'MT'),(133,1,'MH'),(134,1,'MQ'),(135,1,'MR'),(136,1,'MU'),(137,1,'YT'),(138,1,'MX'),(139,1,'FM'),(140,1,'MD'),(141,1,'MC'),(142,1,'MN'),(143,1,'MS'),(144,1,'MA'),(145,1,'MZ'),(146,1,'MM'),(147,1,'NA'),(148,1,'NR'),(149,1,'NP'),(150,1,'NL'),(151,1,'AN'),(152,1,'NC'),(153,1,'NZ'),(154,1,'NI'),(155,1,'NE'),(156,1,'NG'),(157,1,'NU'),(158,1,'NF'),(159,1,'MP'),(160,1,'NO'),(161,1,'OM'),(162,1,'PK'),(163,1,'PW'),(164,1,'PA'),(165,1,'PG'),(166,1,'PY'),(167,1,'PE'),(168,1,'PH'),(169,1,'PN'),(170,1,'PL'),(171,1,'PT'),(172,1,'PR'),(173,1,'QA'),(174,1,'RE'),(175,1,'RO'),(176,1,'RU'),(177,1,'RW'),(178,1,'KN'),(179,1,'LC'),(180,1,'VC'),(181,1,'WS'),(182,1,'SM'),(183,1,'ST'),(184,1,'SA'),(185,1,'SN'),(186,1,'SC'),(187,1,'SL'),(188,1,'SG'),(189,1,'SK'),(190,1,'SI'),(191,1,'SB'),(192,1,'SO'),(193,1,'ZA'),(194,1,'GS'),(195,1,'ES'),(196,1,'LK'),(197,1,'SH'),(198,1,'PM'),(199,1,'SD'),(200,1,'SR'),(201,1,'SJ'),(202,1,'SZ'),(203,1,'SE'),(204,1,'CH'),(205,1,'SY'),(206,1,'TW'),(207,1,'TJ'),(208,1,'TZ'),(209,1,'TH'),(210,1,'TG'),(211,1,'TK'),(212,1,'TO'),(213,1,'TT'),(214,1,'TN'),(215,1,'TR'),(216,1,'TM'),(217,1,'TC'),(218,1,'TV'),(219,1,'UG'),(220,1,'UA'),(221,1,'AE'),(222,1,'GB'),(223,1,'US'),(224,1,'UM'),(225,1,'UY'),(226,1,'UZ'),(227,1,'VU'),(228,1,'VA');
insert into `directory_country` (`country_id`,`currency_id`,`country_iso_code`) values (229,1,'VE'),(230,1,'VN'),(231,1,'VG'),(232,1,'VI'),(233,1,'WF'),(234,1,'EH'),(235,1,'YE'),(236,1,'YU'),(237,1,'ZR'),(238,1,'ZM'),(239,1,'ZW');

/*Table structure for table `directory_country_currency` */

DROP TABLE IF EXISTS `directory_country_currency`;

CREATE TABLE `directory_country_currency` (
  `country_id` smallint(6) NOT NULL default '0',
  `currency_id` smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY  (`country_id`,`currency_id`),
  KEY `FK_COUNTRY_CURRENCY` (`currency_id`),
  CONSTRAINT `FK_CURRENCY_COUNTRY` FOREIGN KEY (`country_id`) REFERENCES `directory_country` (`country_id`),
  CONSTRAINT `FK_COUNTRY_CURRENCY` FOREIGN KEY (`currency_id`) REFERENCES `directory_currency` (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Currency per country';

/*Data for the table `directory_country_currency` */

insert into `directory_country_currency` (`country_id`,`currency_id`) values (223,1);

/*Table structure for table `directory_country_name` */

DROP TABLE IF EXISTS `directory_country_name`;

CREATE TABLE `directory_country_name` (
  `language_code` varchar(2) NOT NULL default '',
  `country_id` smallint(6) NOT NULL default '0',
  `country_name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`language_code`,`country_id`),
  KEY `FK_COUNTRY_NAME_COUNTRY` (`country_id`),
  CONSTRAINT `FK_COUNTRY_NAME_LANG` FOREIGN KEY (`language_code`) REFERENCES `core_language` (`language_code`),
  CONSTRAINT `FK_COUNTRY_NAME_COUNTRY` FOREIGN KEY (`country_id`) REFERENCES `directory_country` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Country names';

/*Data for the table `directory_country_name` */

insert into `directory_country_name` (`language_code`,`country_id`,`country_name`) values ('en',1,'Afghanistan'),('en',2,'Albania'),('en',3,'Algeria'),('en',4,'American Samoa'),('en',5,'Andorra'),('en',6,'Angola'),('en',7,'Anguilla'),('en',8,'Antarctica'),('en',9,'Antigua and Barbuda'),('en',10,'Argentina'),('en',11,'Armenia'),('en',12,'Aruba'),('en',13,'Australia'),('en',14,'Austria'),('en',15,'Azerbaijan'),('en',16,'Bahamas'),('en',17,'Bahrain'),('en',18,'Bangladesh'),('en',19,'Barbados'),('en',20,'Belarus'),('en',21,'Belgium'),('en',22,'Belize'),('en',23,'Benin'),('en',24,'Bermuda'),('en',25,'Bhutan'),('en',26,'Bolivia'),('en',27,'Bosnia and Herzegowina'),('en',28,'Botswana'),('en',29,'Bouvet Island'),('en',30,'Brazil'),('en',31,'British Indian Ocean Territory'),('en',32,'Brunei Darussalam'),('en',33,'Bulgaria'),('en',34,'Burkina Faso'),('en',35,'Burundi'),('en',36,'Cambodia'),('en',37,'Cameroon'),('en',38,'Canada'),('en',39,'Cape Verde'),('en',40,'Cayman Islands'),('en',41,'Central African Republic'),('en',42,'Chad'),('en',43,'Chile'),('en',44,'China'),('en',45,'Christmas Island'),('en',46,'Cocos (Keeling) Islands'),('en',47,'Colombia'),('en',48,'Comoros'),('en',49,'Congo'),('en',50,'Cook Islands'),('en',51,'Costa Rica'),('en',52,'Cote D\'Ivoire'),('en',53,'Croatia'),('en',54,'Cuba'),('en',55,'Cyprus'),('en',56,'Czech Republic'),('en',57,'Denmark'),('en',58,'Djibouti'),('en',59,'Dominica'),('en',60,'Dominican Republic'),('en',61,'East Timor');
insert into `directory_country_name` (`language_code`,`country_id`,`country_name`) values ('en',62,'Ecuador'),('en',63,'Egypt'),('en',64,'El Salvador'),('en',65,'Equatorial Guinea'),('en',66,'Eritrea'),('en',67,'Estonia'),('en',68,'Ethiopia'),('en',69,'Falkland Islands (Malvinas)'),('en',70,'Faroe Islands'),('en',71,'Fiji'),('en',72,'Finland'),('en',73,'France'),('en',74,'France, Metropolitan'),('en',75,'French Guiana'),('en',76,'French Polynesia'),('en',77,'French Southern Territories'),('en',78,'Gabon'),('en',79,'Gambia'),('en',80,'Georgia'),('en',81,'Germany'),('en',82,'Ghana'),('en',83,'Gibraltar'),('en',84,'Greece'),('en',85,'Greenland'),('en',86,'Grenada'),('en',87,'Guadeloupe'),('en',88,'Guam'),('en',89,'Guatemala'),('en',90,'Guinea'),('en',91,'Guinea-bissau'),('en',92,'Guyana'),('en',93,'Haiti'),('en',94,'Heard and Mc Donald Islands'),('en',95,'Honduras'),('en',96,'Hong Kong'),('en',97,'Hungary'),('en',98,'Iceland'),('en',99,'India'),('en',100,'Indonesia'),('en',101,'Iran (Islamic Republic of)'),('en',102,'Iraq'),('en',103,'Ireland'),('en',104,'Israel'),('en',105,'Italy'),('en',106,'Jamaica'),('en',107,'Japan'),('en',108,'Jordan'),('en',109,'Kazakhstan'),('en',110,'Kenya'),('en',111,'Kiribati'),('en',112,'Korea, Democratic People\'s Republic of'),('en',113,'Korea, Republic of'),('en',114,'Kuwait'),('en',115,'Kyrgyzstan'),('en',116,'Lao People\'s Democratic Republic'),('en',117,'Latvia'),('en',118,'Lebanon'),('en',119,'Lesotho');
insert into `directory_country_name` (`language_code`,`country_id`,`country_name`) values ('en',120,'Liberia'),('en',121,'Libyan Arab Jamahiriya'),('en',122,'Liechtenstein'),('en',123,'Lithuania'),('en',124,'Luxembourg'),('en',125,'Macau'),('en',126,'Macedonia, The Former Yugoslav Republic of'),('en',127,'Madagascar'),('en',128,'Malawi'),('en',129,'Malaysia'),('en',130,'Maldives'),('en',131,'Mali'),('en',132,'Malta'),('en',133,'Marshall Islands'),('en',134,'Martinique'),('en',135,'Mauritania'),('en',136,'Mauritius'),('en',137,'Mayotte'),('en',138,'Mexico'),('en',139,'Micronesia, Federated States of'),('en',140,'Moldova, Republic of'),('en',141,'Monaco'),('en',142,'Mongolia'),('en',143,'Montserrat'),('en',144,'Morocco'),('en',145,'Mozambique'),('en',146,'Myanmar'),('en',147,'Namibia'),('en',148,'Nauru'),('en',149,'Nepal'),('en',150,'Netherlands'),('en',151,'Netherlands Antilles'),('en',152,'New Caledonia'),('en',153,'New Zealand'),('en',154,'Nicaragua'),('en',155,'Niger'),('en',156,'Nigeria'),('en',157,'Niue'),('en',158,'Norfolk Island'),('en',159,'Northern Mariana Islands'),('en',160,'Norway'),('en',161,'Oman'),('en',162,'Pakistan'),('en',163,'Palau'),('en',164,'Panama'),('en',165,'Papua New Guinea'),('en',166,'Paraguay'),('en',167,'Peru'),('en',168,'Philippines'),('en',169,'Pitcairn'),('en',170,'Poland'),('en',171,'Portugal'),('en',172,'Puerto Rico'),('en',173,'Qatar'),('en',174,'Reunion'),('en',175,'Romania');
insert into `directory_country_name` (`language_code`,`country_id`,`country_name`) values ('en',176,'Russian Federation'),('en',177,'Rwanda'),('en',178,'Saint Kitts and Nevis'),('en',179,'Saint Lucia'),('en',180,'Saint Vincent and the Grenadines'),('en',181,'Samoa'),('en',182,'San Marino'),('en',183,'Sao Tome and Principe'),('en',184,'Saudi Arabia'),('en',185,'Senegal'),('en',186,'Seychelles'),('en',187,'Sierra Leone'),('en',188,'Singapore'),('en',189,'Slovakia (Slovak Republic)'),('en',190,'Slovenia'),('en',191,'Solomon Islands'),('en',192,'Somalia'),('en',193,'South Africa'),('en',194,'South Georgia and the South Sandwich Islands'),('en',195,'Spain'),('en',196,'Sri Lanka'),('en',197,'St. Helena'),('en',198,'St. Pierre and Miquelon'),('en',199,'Sudan'),('en',200,'Suriname'),('en',201,'Svalbard and Jan Mayen Islands'),('en',202,'Swaziland'),('en',203,'Sweden'),('en',204,'Switzerland'),('en',205,'Syrian Arab Republic'),('en',206,'Taiwan'),('en',207,'Tajikistan'),('en',208,'Tanzania, United Republic of'),('en',209,'Thailand'),('en',210,'Togo'),('en',211,'Tokelau'),('en',212,'Tonga'),('en',213,'Trinidad and Tobago'),('en',214,'Tunisia'),('en',215,'Turkey'),('en',216,'Turkmenistan'),('en',217,'Turks and Caicos Islands'),('en',218,'Tuvalu'),('en',219,'Uganda'),('en',220,'Ukraine'),('en',221,'United Arab Emirates'),('en',222,'United Kingdom'),('en',223,'United States');
insert into `directory_country_name` (`language_code`,`country_id`,`country_name`) values ('en',224,'United States Minor Outlying Islands'),('en',225,'Uruguay'),('en',226,'Uzbekistan'),('en',227,'Vanuatu'),('en',228,'Vatican City State (Holy See)'),('en',229,'Venezuela'),('en',230,'Viet Nam'),('en',231,'Virgin Islands (British)'),('en',232,'Virgin Islands (U.S.)'),('en',233,'Wallis and Futuna Islands'),('en',234,'Western Sahara'),('en',235,'Yemen'),('en',236,'Yugoslavia'),('en',237,'Zaire'),('en',238,'Zambia'),('en',239,'Zimbabwe');

/*Table structure for table `directory_country_region` */

DROP TABLE IF EXISTS `directory_country_region`;

CREATE TABLE `directory_country_region` (
  `region_id` mediumint(8) unsigned NOT NULL auto_increment,
  `country_id` smallint(6) NOT NULL default '0',
  `region_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`region_id`),
  KEY `FK_REGION_COUNTRY` (`country_id`),
  CONSTRAINT `FK_REGION_COUNTRY` FOREIGN KEY (`country_id`) REFERENCES `directory_country` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Country regions';

/*Data for the table `directory_country_region` */

insert into `directory_country_region` (`region_id`,`country_id`,`region_code`) values (1,223,'AL'),(2,223,'AK'),(3,223,'AS'),(4,223,'AZ'),(5,223,'AR'),(6,223,'AF'),(7,223,'AA'),(8,223,'AC'),(9,223,'AE'),(10,223,'AM'),(11,223,'AP'),(12,223,'CA'),(13,223,'CO'),(14,223,'CT'),(15,223,'DE'),(16,223,'DC'),(17,223,'FM'),(18,223,'FL'),(19,223,'GA'),(20,223,'GU'),(21,223,'HI'),(22,223,'ID'),(23,223,'IL'),(24,223,'IN'),(25,223,'IA'),(26,223,'KS'),(27,223,'KY'),(28,223,'LA'),(29,223,'ME'),(30,223,'MH'),(31,223,'MD'),(32,223,'MA'),(33,223,'MI'),(34,223,'MN'),(35,223,'MS'),(36,223,'MO'),(37,223,'MT'),(38,223,'NE'),(39,223,'NV'),(40,223,'NH'),(41,223,'NJ'),(42,223,'NM'),(43,223,'NY'),(44,223,'NC'),(45,223,'ND'),(46,223,'MP'),(47,223,'OH'),(48,223,'OK'),(49,223,'OR'),(50,223,'PW'),(51,223,'PA'),(52,223,'PR'),(53,223,'RI'),(54,223,'SC'),(55,223,'SD'),(56,223,'TN'),(57,223,'TX'),(58,223,'UT'),(59,223,'VT'),(60,223,'VI'),(61,223,'VA'),(62,223,'WA'),(63,223,'WV'),(64,223,'WI'),(65,223,'WY'),(66,38,'AB'),(67,38,'BC'),(68,38,'MB'),(69,38,'NF'),(70,38,'NB'),(71,38,'NS'),(72,38,'NT'),(73,38,'NU'),(74,38,'ON'),(75,38,'PE'),(76,38,'QC'),(77,38,'SK'),(78,38,'YT'),(79,81,'NDS'),(80,81,'BAW'),(81,81,'BAY'),(82,81,'BER'),(83,81,'BRG'),(84,81,'BRE'),(85,81,'HAM'),(86,81,'HES'),(87,81,'MEC'),(88,81,'NRW'),(89,81,'RHE'),(90,81,'SAR'),(91,81,'SAS'),(92,81,'SAC'),(93,81,'SCN'),(94,81,'THE'),(95,14,'WI'),(96,14,'NO'),(97,14,'OO'),(98,14,'SB'),(99,14,'KN'),(100,14,'ST'),(101,14,'TI'),(102,14,'BL'),(103,14,'VB'),(104,204,'AG');
insert into `directory_country_region` (`region_id`,`country_id`,`region_code`) values (105,204,'AI'),(106,204,'AR'),(107,204,'BE'),(108,204,'BL'),(109,204,'BS'),(110,204,'FR'),(111,204,'GE'),(112,204,'GL'),(113,204,'JU'),(114,204,'JU'),(115,204,'LU'),(116,204,'NE'),(117,204,'NW'),(118,204,'OW'),(119,204,'SG'),(120,204,'SH'),(121,204,'SO'),(122,204,'SZ'),(123,204,'TG'),(124,204,'TI'),(125,204,'UR'),(126,204,'VD'),(127,204,'VS'),(128,204,'ZG'),(129,204,'ZH'),(130,195,'A Coruсa'),(131,195,'Alava'),(132,195,'Albacete'),(133,195,'Alicante'),(134,195,'Almeria'),(135,195,'Asturias'),(136,195,'Avila'),(137,195,'Badajoz'),(138,195,'Baleares'),(139,195,'Barcelona'),(140,195,'Burgos'),(141,195,'Caceres'),(142,195,'Cadiz'),(143,195,'Cantabria'),(144,195,'Castellon'),(145,195,'Ceuta'),(146,195,'Ciudad Real'),(147,195,'Cordoba'),(148,195,'Cuenca'),(149,195,'Girona'),(150,195,'Granada'),(151,195,'Guadalajara'),(152,195,'Guipuzcoa'),(153,195,'Huelva'),(154,195,'Huesca'),(155,195,'Jaen'),(156,195,'La Rioja'),(157,195,'Las Palmas'),(158,195,'Leon'),(159,195,'Lleida'),(160,195,'Lugo'),(161,195,'Madrid'),(162,195,'Malaga'),(163,195,'Melilla'),(164,195,'Murcia'),(165,195,'Navarra'),(166,195,'Ourense'),(167,195,'Palencia'),(168,195,'Pontevedra'),(169,195,'Salamanca'),(170,195,'Santa Cruz de Tenerife'),(171,195,'Segovia'),(172,195,'Sevilla'),(173,195,'Soria'),(174,195,'Tarragona');
insert into `directory_country_region` (`region_id`,`country_id`,`region_code`) values (175,195,'Teruel'),(176,195,'Toledo'),(177,195,'Valencia'),(178,195,'Valladolid'),(179,195,'Vizcaya'),(180,195,'Zamora'),(181,195,'Zaragoza');

/*Table structure for table `directory_country_region_name` */

DROP TABLE IF EXISTS `directory_country_region_name`;

CREATE TABLE `directory_country_region_name` (
  `language_code` varchar(2) NOT NULL default '',
  `region_id` mediumint(8) unsigned NOT NULL default '0',
  `region_name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`language_code`,`region_id`),
  KEY `FK_REGION_NAME_REGION` (`region_id`),
  CONSTRAINT `FK_REGION_NAME_REGION` FOREIGN KEY (`region_id`) REFERENCES `directory_country_region` (`region_id`),
  CONSTRAINT `FK_REGION_NAME_LANG` FOREIGN KEY (`language_code`) REFERENCES `core_language` (`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Regions names';

/*Data for the table `directory_country_region_name` */

insert into `directory_country_region_name` (`language_code`,`region_id`,`region_name`) values ('en',1,'Alabama'),('en',2,'Alaska'),('en',3,'American Samoa'),('en',4,'Arizona'),('en',5,'Arkansas'),('en',6,'Armed Forces Africa'),('en',7,'Armed Forces Americas'),('en',8,'Armed Forces Canada'),('en',9,'Armed Forces Europe'),('en',10,'Armed Forces Middle East'),('en',11,'Armed Forces Pacific'),('en',12,'California'),('en',13,'Colorado'),('en',14,'Connecticut'),('en',15,'Delaware'),('en',16,'District of Columbia'),('en',17,'Federated States Of Micronesia'),('en',18,'Florida'),('en',19,'Georgia'),('en',20,'Guam'),('en',21,'Hawaii'),('en',22,'Idaho'),('en',23,'Illinois'),('en',24,'Indiana'),('en',25,'Iowa'),('en',26,'Kansas'),('en',27,'Kentucky'),('en',28,'Louisiana'),('en',29,'Maine'),('en',30,'Marshall Islands'),('en',31,'Maryland'),('en',32,'Massachusetts'),('en',33,'Michigan'),('en',34,'Minnesota'),('en',35,'Mississippi'),('en',36,'Missouri'),('en',37,'Montana'),('en',38,'Nebraska'),('en',39,'Nevada'),('en',40,'New Hampshire'),('en',41,'New Jersey'),('en',42,'New Mexico'),('en',43,'New York'),('en',44,'North Carolina'),('en',45,'North Dakota'),('en',46,'Northern Mariana Islands'),('en',47,'Ohio'),('en',48,'Oklahoma'),('en',49,'Oregon'),('en',50,'Palau'),('en',51,'Pennsylvania'),('en',52,'Puerto Rico'),('en',53,'Rhode Island'),('en',54,'South Carolina'),('en',55,'South Dakota'),('en',56,'Tennessee'),('en',57,'Texas'),('en',58,'Utah');
insert into `directory_country_region_name` (`language_code`,`region_id`,`region_name`) values ('en',59,'Vermont'),('en',60,'Virgin Islands'),('en',61,'Virginia'),('en',62,'Washington'),('en',63,'West Virginia'),('en',64,'Wisconsin'),('en',65,'Wyoming'),('en',66,'Alberta'),('en',67,'British Columbia'),('en',68,'Manitoba'),('en',69,'Newfoundland'),('en',70,'New Brunswick'),('en',71,'Nova Scotia'),('en',72,'Northwest Territories'),('en',73,'Nunavut'),('en',74,'Ontario'),('en',75,'Prince Edward Island'),('en',76,'Quebec'),('en',77,'Saskatchewan'),('en',78,'Yukon Territory'),('en',79,'Niedersachsen'),('en',80,'Baden-Wьrttemberg'),('en',81,'Bayern'),('en',82,'Berlin'),('en',83,'Brandenburg'),('en',84,'Bremen'),('en',85,'Hamburg'),('en',86,'Hessen'),('en',87,'Mecklenburg-Vorpommern'),('en',88,'Nordrhein-Westfalen'),('en',89,'Rheinland-Pfalz'),('en',90,'Saarland'),('en',91,'Sachsen'),('en',92,'Sachsen-Anhalt'),('en',93,'Schleswig-Holstein'),('en',94,'Thьringen'),('en',95,'Wien'),('en',96,'Niederцsterreich'),('en',97,'Oberцsterreich'),('en',98,'Salzburg'),('en',99,'Kдrnten'),('en',100,'Steiermark'),('en',101,'Tirol'),('en',102,'Burgenland'),('en',103,'Voralberg'),('en',104,'Aargau'),('en',105,'Appenzell Innerrhoden'),('en',106,'Appenzell Ausserrhoden'),('en',107,'Bern'),('en',108,'Basel-Landschaft'),('en',109,'Basel-Stadt'),('en',110,'Freiburg'),('en',111,'Genf'),('en',112,'Glarus'),('en',113,'Graubьnden'),('en',114,'Jura');
insert into `directory_country_region_name` (`language_code`,`region_id`,`region_name`) values ('en',115,'Luzern'),('en',116,'Neuenburg'),('en',117,'Nidwalden'),('en',118,'Obwalden'),('en',119,'St. Gallen'),('en',120,'Schaffhausen'),('en',121,'Solothurn'),('en',122,'Schwyz'),('en',123,'Thurgau'),('en',124,'Tessin'),('en',125,'Uri'),('en',126,'Waadt'),('en',127,'Wallis'),('en',128,'Zug'),('en',129,'Zьrich'),('en',130,'A Coruсa'),('en',131,'Alava'),('en',132,'Albacete'),('en',133,'Alicante'),('en',134,'Almeria'),('en',135,'Asturias'),('en',136,'Avila'),('en',137,'Badajoz'),('en',138,'Baleares'),('en',139,'Barcelona'),('en',140,'Burgos'),('en',141,'Caceres'),('en',142,'Cadiz'),('en',143,'Cantabria'),('en',144,'Castellon'),('en',145,'Ceuta'),('en',146,'Ciudad Real'),('en',147,'Cordoba'),('en',148,'Cuenca'),('en',149,'Girona'),('en',150,'Granada'),('en',151,'Guadalajara'),('en',152,'Guipuzcoa'),('en',153,'Huelva'),('en',154,'Huesca'),('en',155,'Jaen'),('en',156,'La Rioja'),('en',157,'Las Palmas'),('en',158,'Leon'),('en',159,'Lleida'),('en',160,'Lugo'),('en',161,'Madrid'),('en',162,'Malaga'),('en',163,'Melilla'),('en',164,'Murcia'),('en',165,'Navarra'),('en',166,'Ourense'),('en',167,'Palencia'),('en',168,'Pontevedra'),('en',169,'Salamanca'),('en',170,'Santa Cruz de Tenerife'),('en',171,'Segovia'),('en',172,'Sevilla'),('en',173,'Soria'),('en',174,'Tarragona'),('en',175,'Teruel'),('en',176,'Toledo'),('en',177,'Valencia'),('en',178,'Valladolid'),('en',179,'Vizcaya'),('en',180,'Zamora');
insert into `directory_country_region_name` (`language_code`,`region_id`,`region_name`) values ('en',181,'Zaragoza');

/*Table structure for table `directory_currency` */

DROP TABLE IF EXISTS `directory_currency`;

CREATE TABLE `directory_currency` (
  `currency_id` smallint(6) unsigned NOT NULL auto_increment,
  `currency_code` char(3) NOT NULL default '',
  `currency_symbol` char(1) default NULL,
  PRIMARY KEY  (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Currency';

/*Data for the table `directory_currency` */

insert into `directory_currency` (`currency_id`,`currency_code`,`currency_symbol`) values (1,'USD','$');

/*Table structure for table `directory_currency_name` */

DROP TABLE IF EXISTS `directory_currency_name`;

CREATE TABLE `directory_currency_name` (
  `language_code` varchar(2) NOT NULL default '',
  `currency_id` smallint(6) unsigned NOT NULL default '0',
  `currency_name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`language_code`,`currency_id`),
  KEY `FK_CURRENCY_NAME_CURRENCY` (`currency_id`),
  CONSTRAINT `FK_CURRENCY_NAME_CURRENCY` FOREIGN KEY (`currency_id`) REFERENCES `directory_currency` (`currency_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CURENCY_NAME_LANG` FOREIGN KEY (`language_code`) REFERENCES `core_language` (`language_code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Currency names';

/*Data for the table `directory_currency_name` */

insert into `directory_currency_name` (`language_code`,`currency_id`,`currency_name`) values ('en',1,'Dollar USA');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
