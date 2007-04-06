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
  `iso2_code` char(2) NOT NULL default '',
  `iso3_code` char(3) NOT NULL default '',
  PRIMARY KEY  (`country_id`),
  KEY `FK_COUNTRY_DEFAULT_CURRENCY` (`currency_id`),
  CONSTRAINT `FK_COUNTRY_DEFAULT_CURRENCY` FOREIGN KEY (`currency_id`) REFERENCES `directory_currency` (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Countries';

/*Data for the table `directory_country` */

insert into `directory_country` (`country_id`,`currency_id`,`iso2_code`,`iso3_code`) values (1,1,'AF','AFG'),(2,1,'AL','ALB'),(3,1,'DZ','DZA'),(4,1,'AS','ASM'),(5,1,'AD','AND'),(6,1,'AO','AGO'),(7,1,'AI','AIA'),(8,1,'AQ','ATA'),(9,1,'AG','ATG'),(10,1,'AR','ARG'),(11,1,'AM','ARM'),(12,1,'AW','ABW'),(13,1,'AU','AUS'),(14,1,'AT','AUT'),(15,1,'AZ','AZE'),(16,1,'BS','BHS'),(17,1,'BH','BHR'),(18,1,'BD','BGD'),(19,1,'BB','BRB'),(20,1,'BY','BLR'),(21,1,'BE','BEL'),(22,1,'BZ','BLZ'),(23,1,'BJ','BEN'),(24,1,'BM','BMU'),(25,1,'BT','BTN'),(26,1,'BO','BOL'),(27,1,'BA','BIH'),(28,1,'BW','BWA'),(29,1,'BV','BVT'),(30,1,'BR','BRA'),(31,1,'IO','IOT'),(32,1,'BN','BRN'),(33,1,'BG','BGR'),(34,1,'BF','BFA'),(35,1,'BI','BDI'),(36,1,'KH','KHM'),(37,1,'CM','CMR'),(38,1,'CA','CAN'),(39,1,'CV','CPV'),(40,1,'KY','CYM'),(41,1,'CF','CAF'),(42,1,'TD','TCD'),(43,1,'CL','CHL'),(44,1,'CN','CHN'),(45,1,'CX','CXR'),(46,1,'CC','CCK'),(47,1,'CO','COL'),(48,1,'KM','COM'),(49,1,'CG','COG'),(50,1,'CK','COK'),(51,1,'CR','CRI'),(52,1,'CI','CIV'),(53,1,'HR','HRV'),(54,1,'CU','CUB'),(55,1,'CY','CYP'),(56,1,'CZ','CZE'),(57,1,'DK','DNK'),(58,1,'DJ','DJI'),(59,1,'DM','DMA'),(60,1,'DO','DOM'),(61,1,'TP','TMP'),(62,1,'EC','ECU'),(63,1,'EG','EGY'),(64,1,'SV','SLV'),(65,1,'GQ','GNQ'),(66,1,'ER','ERI'),(67,1,'EE','EST'),(68,1,'ET','ETH'),(69,1,'FK','FLK'),(70,1,'FO','FRO'),(71,1,'FJ','FJI'),(72,1,'FI','FIN'),(73,1,'FR','FRA'),(74,1,'FX','FXX'),(75,1,'GF','GUF'),(76,1,'PF','PYF'),(77,1,'TF','ATF'),(78,1,'GA','GAB'),(79,1,'GM','GMB'),(80,1,'GE','GEO'),(81,1,'DE','DEU'),(82,1,'GH','GHA'),(83,1,'GI','GIB'),(84,1,'GR','GRC');
insert into `directory_country` (`country_id`,`currency_id`,`iso2_code`,`iso3_code`) values (85,1,'GL','GRL'),(86,1,'GD','GRD'),(87,1,'GP','GLP'),(88,1,'GU','GUM'),(89,1,'GT','GTM'),(90,1,'GN','GIN'),(91,1,'GW','GNB'),(92,1,'GY','GUY'),(93,1,'HT','HTI'),(94,1,'HM','HMD'),(95,1,'HN','HND'),(96,1,'HK','HKG'),(97,1,'HU','HUN'),(98,1,'IS','ISL'),(99,1,'IN','IND'),(100,1,'ID','IDN'),(101,1,'IR','IRN'),(102,1,'IQ','IRQ'),(103,1,'IE','IRL'),(104,1,'IL','ISR'),(105,1,'IT','ITA'),(106,1,'JM','JAM'),(107,1,'JP','JPN'),(108,1,'JO','JOR'),(109,1,'KZ','KAZ'),(110,1,'KE','KEN'),(111,1,'KI','KIR'),(112,1,'KP','PRK'),(113,1,'KR','KOR'),(114,1,'KW','KWT'),(115,1,'KG','KGZ'),(116,1,'LA','LAO'),(117,1,'LV','LVA'),(118,1,'LB','LBN'),(119,1,'LS','LSO'),(120,1,'LR','LBR'),(121,1,'LY','LBY'),(122,1,'LI','LIE'),(123,1,'LT','LTU'),(124,1,'LU','LUX'),(125,1,'MO','MAC'),(126,1,'MK','MKD'),(127,1,'MG','MDG'),(128,1,'MW','MWI'),(129,1,'MY','MYS'),(130,1,'MV','MDV'),(131,1,'ML','MLI'),(132,1,'MT','MLT'),(133,1,'MH','MHL'),(134,1,'MQ','MTQ'),(135,1,'MR','MRT'),(136,1,'MU','MUS'),(137,1,'YT','MYT'),(138,1,'MX','MEX'),(139,1,'FM','FSM'),(140,1,'MD','MDA'),(141,1,'MC','MCO'),(142,1,'MN','MNG'),(143,1,'MS','MSR'),(144,1,'MA','MAR'),(145,1,'MZ','MOZ'),(146,1,'MM','MMR'),(147,1,'NA','NAM'),(148,1,'NR','NRU'),(149,1,'NP','NPL'),(150,1,'NL','NLD'),(151,1,'AN','ANT'),(152,1,'NC','NCL'),(153,1,'NZ','NZL'),(154,1,'NI','NIC'),(155,1,'NE','NER'),(156,1,'NG','NGA'),(157,1,'NU','NIU'),(158,1,'NF','NFK'),(159,1,'MP','MNP'),(160,1,'NO','NOR'),(161,1,'OM','OMN'),(162,1,'PK','PAK'),(163,1,'PW','PLW'),(164,1,'PA','PAN'),(165,1,'PG','PNG'),(166,1,'PY','PRY'),(167,1,'PE','PER'),(168,1,'PH','PHL'),(169,1,'PN','PCN'),(170,1,'PL','POL');
insert into `directory_country` (`country_id`,`currency_id`,`iso2_code`,`iso3_code`) values (171,1,'PT','PRT'),(172,1,'PR','PRI'),(173,1,'QA','QAT'),(174,1,'RE','REU'),(175,1,'RO','ROM'),(176,1,'RU','RUS'),(177,1,'RW','RWA'),(178,1,'KN','KNA'),(179,1,'LC','LCA'),(180,1,'VC','VCT'),(181,1,'WS','WSM'),(182,1,'SM','SMR'),(183,1,'ST','STP'),(184,1,'SA','SAU'),(185,1,'SN','SEN'),(186,1,'SC','SYC'),(187,1,'SL','SLE'),(188,1,'SG','SGP'),(189,1,'SK','SVK'),(190,1,'SI','SVN'),(191,1,'SB','SLB'),(192,1,'SO','SOM'),(193,1,'ZA','ZAF'),(194,1,'GS','SGS'),(195,1,'ES','ESP'),(196,1,'LK','LKA'),(197,1,'SH','SHN'),(198,1,'PM','SPM'),(199,1,'SD','SDN'),(200,1,'SR','SUR'),(201,1,'SJ','SJM'),(202,1,'SZ','SWZ'),(203,1,'SE','SWE'),(204,1,'CH','CHE'),(205,1,'SY','SYR'),(206,1,'TW','TWN'),(207,1,'TJ','TJK'),(208,1,'TZ','TZA'),(209,1,'TH','THA'),(210,1,'TG','TGO'),(211,1,'TK','TKL'),(212,1,'TO','TON'),(213,1,'TT','TTO'),(214,1,'TN','TUN'),(215,1,'TR','TUR'),(216,1,'TM','TKM'),(217,1,'TC','TCA'),(218,1,'TV','TUV'),(219,1,'UG','UGA'),(220,1,'UA','UKR'),(221,1,'AE','ARE'),(222,1,'GB','GBR'),(223,1,'US','USA'),(224,1,'UM','UMI'),(225,1,'UY','URY'),(226,1,'UZ','UZB'),(227,1,'VU','VUT'),(228,1,'VA','VAT'),(229,1,'VE','VEN'),(230,1,'VN','VNM'),(231,1,'VG','VGB'),(232,1,'VI','VIR'),(233,1,'WF','WLF'),(234,1,'EH','ESH'),(235,1,'YE','YEM'),(236,1,'YU','YUG'),(237,1,'ZR','ZAR'),(238,1,'ZM','ZMB'),(239,1,'ZW','ZWE');

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
  `name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`language_code`,`country_id`),
  KEY `FK_COUNTRY_NAME_COUNTRY` (`country_id`),
  CONSTRAINT `FK_COUNTRY_NAME_COUNTRY` FOREIGN KEY (`country_id`) REFERENCES `directory_country` (`country_id`),
  CONSTRAINT `FK_COUNTRY_NAME_LANG` FOREIGN KEY (`language_code`) REFERENCES `core_language` (`language_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Country names';

/*Data for the table `directory_country_name` */

insert into `directory_country_name` (`language_code`,`country_id`,`name`) values ('en',1,'Afghanistan'),('en',2,'Albania'),('en',3,'Algeria'),('en',4,'American Samoa'),('en',5,'Andorra'),('en',6,'Angola'),('en',7,'Anguilla'),('en',8,'Antarctica'),('en',9,'Antigua and Barbuda'),('en',10,'Argentina'),('en',11,'Armenia'),('en',12,'Aruba'),('en',13,'Australia'),('en',14,'Austria'),('en',15,'Azerbaijan'),('en',16,'Bahamas'),('en',17,'Bahrain'),('en',18,'Bangladesh'),('en',19,'Barbados'),('en',20,'Belarus'),('en',21,'Belgium'),('en',22,'Belize'),('en',23,'Benin'),('en',24,'Bermuda'),('en',25,'Bhutan'),('en',26,'Bolivia'),('en',27,'Bosnia and Herzegowina'),('en',28,'Botswana'),('en',29,'Bouvet Island'),('en',30,'Brazil'),('en',31,'British Indian Ocean Territory'),('en',32,'Brunei Darussalam'),('en',33,'Bulgaria'),('en',34,'Burkina Faso'),('en',35,'Burundi'),('en',36,'Cambodia'),('en',37,'Cameroon'),('en',38,'Canada'),('en',39,'Cape Verde'),('en',40,'Cayman Islands'),('en',41,'Central African Republic'),('en',42,'Chad'),('en',43,'Chile'),('en',44,'China'),('en',45,'Christmas Island'),('en',46,'Cocos (Keeling) Islands'),('en',47,'Colombia'),('en',48,'Comoros'),('en',49,'Congo'),('en',50,'Cook Islands'),('en',51,'Costa Rica'),('en',52,'Cote D\'Ivoire'),('en',53,'Croatia'),('en',54,'Cuba'),('en',55,'Cyprus'),('en',56,'Czech Republic'),('en',57,'Denmark'),('en',58,'Djibouti'),('en',59,'Dominica'),('en',60,'Dominican Republic'),('en',61,'East Timor');
insert into `directory_country_name` (`language_code`,`country_id`,`name`) values ('en',62,'Ecuador'),('en',63,'Egypt'),('en',64,'El Salvador'),('en',65,'Equatorial Guinea'),('en',66,'Eritrea'),('en',67,'Estonia'),('en',68,'Ethiopia'),('en',69,'Falkland Islands (Malvinas)'),('en',70,'Faroe Islands'),('en',71,'Fiji'),('en',72,'Finland'),('en',73,'France'),('en',74,'France, Metropolitan'),('en',75,'French Guiana'),('en',76,'French Polynesia'),('en',77,'French Southern Territories'),('en',78,'Gabon'),('en',79,'Gambia'),('en',80,'Georgia'),('en',81,'Germany'),('en',82,'Ghana'),('en',83,'Gibraltar'),('en',84,'Greece'),('en',85,'Greenland'),('en',86,'Grenada'),('en',87,'Guadeloupe'),('en',88,'Guam'),('en',89,'Guatemala'),('en',90,'Guinea'),('en',91,'Guinea-bissau'),('en',92,'Guyana'),('en',93,'Haiti'),('en',94,'Heard and Mc Donald Islands'),('en',95,'Honduras'),('en',96,'Hong Kong'),('en',97,'Hungary'),('en',98,'Iceland'),('en',99,'India'),('en',100,'Indonesia'),('en',101,'Iran (Islamic Republic of)'),('en',102,'Iraq'),('en',103,'Ireland'),('en',104,'Israel'),('en',105,'Italy'),('en',106,'Jamaica'),('en',107,'Japan'),('en',108,'Jordan'),('en',109,'Kazakhstan'),('en',110,'Kenya'),('en',111,'Kiribati'),('en',112,'Korea, Democratic People\'s Republic of'),('en',113,'Korea, Republic of'),('en',114,'Kuwait'),('en',115,'Kyrgyzstan'),('en',116,'Lao People\'s Democratic Republic'),('en',117,'Latvia'),('en',118,'Lebanon'),('en',119,'Lesotho');
insert into `directory_country_name` (`language_code`,`country_id`,`name`) values ('en',120,'Liberia'),('en',121,'Libyan Arab Jamahiriya'),('en',122,'Liechtenstein'),('en',123,'Lithuania'),('en',124,'Luxembourg'),('en',125,'Macau'),('en',126,'Macedonia, The Former Yugoslav Republic of'),('en',127,'Madagascar'),('en',128,'Malawi'),('en',129,'Malaysia'),('en',130,'Maldives'),('en',131,'Mali'),('en',132,'Malta'),('en',133,'Marshall Islands'),('en',134,'Martinique'),('en',135,'Mauritania'),('en',136,'Mauritius'),('en',137,'Mayotte'),('en',138,'Mexico'),('en',139,'Micronesia, Federated States of'),('en',140,'Moldova, Republic of'),('en',141,'Monaco'),('en',142,'Mongolia'),('en',143,'Montserrat'),('en',144,'Morocco'),('en',145,'Mozambique'),('en',146,'Myanmar'),('en',147,'Namibia'),('en',148,'Nauru'),('en',149,'Nepal'),('en',150,'Netherlands'),('en',151,'Netherlands Antilles'),('en',152,'New Caledonia'),('en',153,'New Zealand'),('en',154,'Nicaragua'),('en',155,'Niger'),('en',156,'Nigeria'),('en',157,'Niue'),('en',158,'Norfolk Island'),('en',159,'Northern Mariana Islands'),('en',160,'Norway'),('en',161,'Oman'),('en',162,'Pakistan'),('en',163,'Palau'),('en',164,'Panama'),('en',165,'Papua New Guinea'),('en',166,'Paraguay'),('en',167,'Peru'),('en',168,'Philippines'),('en',169,'Pitcairn'),('en',170,'Poland'),('en',171,'Portugal'),('en',172,'Puerto Rico'),('en',173,'Qatar'),('en',174,'Reunion'),('en',175,'Romania');
insert into `directory_country_name` (`language_code`,`country_id`,`name`) values ('en',176,'Russian Federation'),('en',177,'Rwanda'),('en',178,'Saint Kitts and Nevis'),('en',179,'Saint Lucia'),('en',180,'Saint Vincent and the Grenadines'),('en',181,'Samoa'),('en',182,'San Marino'),('en',183,'Sao Tome and Principe'),('en',184,'Saudi Arabia'),('en',185,'Senegal'),('en',186,'Seychelles'),('en',187,'Sierra Leone'),('en',188,'Singapore'),('en',189,'Slovakia (Slovak Republic)'),('en',190,'Slovenia'),('en',191,'Solomon Islands'),('en',192,'Somalia'),('en',193,'South Africa'),('en',194,'South Georgia and the South Sandwich Islands'),('en',195,'Spain'),('en',196,'Sri Lanka'),('en',197,'St. Helena'),('en',198,'St. Pierre and Miquelon'),('en',199,'Sudan'),('en',200,'Suriname'),('en',201,'Svalbard and Jan Mayen Islands'),('en',202,'Swaziland'),('en',203,'Sweden'),('en',204,'Switzerland'),('en',205,'Syrian Arab Republic'),('en',206,'Taiwan'),('en',207,'Tajikistan'),('en',208,'Tanzania, United Republic of'),('en',209,'Thailand'),('en',210,'Togo'),('en',211,'Tokelau'),('en',212,'Tonga'),('en',213,'Trinidad and Tobago'),('en',214,'Tunisia'),('en',215,'Turkey'),('en',216,'Turkmenistan'),('en',217,'Turks and Caicos Islands'),('en',218,'Tuvalu'),('en',219,'Uganda'),('en',220,'Ukraine'),('en',221,'United Arab Emirates'),('en',222,'United Kingdom'),('en',223,'United States');
insert into `directory_country_name` (`language_code`,`country_id`,`name`) values ('en',224,'United States Minor Outlying Islands'),('en',225,'Uruguay'),('en',226,'Uzbekistan'),('en',227,'Vanuatu'),('en',228,'Vatican City State (Holy See)'),('en',229,'Venezuela'),('en',230,'Viet Nam'),('en',231,'Virgin Islands (British)'),('en',232,'Virgin Islands (U.S.)'),('en',233,'Wallis and Futuna Islands'),('en',234,'Western Sahara'),('en',235,'Yemen'),('en',236,'Yugoslavia'),('en',237,'Zaire'),('en',238,'Zambia'),('en',239,'Zimbabwe');

/*Table structure for table `directory_country_region` */

DROP TABLE IF EXISTS `directory_country_region`;

CREATE TABLE `directory_country_region` (
  `region_id` mediumint(8) unsigned NOT NULL auto_increment,
  `country_id` smallint(6) NOT NULL default '0',
  `code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`region_id`),
  KEY `FK_REGION_COUNTRY` (`country_id`),
  CONSTRAINT `FK_REGION_COUNTRY` FOREIGN KEY (`country_id`) REFERENCES `directory_country` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Country regions';

/*Data for the table `directory_country_region` */

insert into `directory_country_region` (`region_id`,`country_id`,`code`) values (1,223,'AL'),(2,223,'AK'),(3,223,'AS'),(4,223,'AZ'),(5,223,'AR'),(6,223,'AF'),(7,223,'AA'),(8,223,'AC'),(9,223,'AE'),(10,223,'AM'),(11,223,'AP'),(12,223,'CA'),(13,223,'CO'),(14,223,'CT'),(15,223,'DE'),(16,223,'DC'),(17,223,'FM'),(18,223,'FL'),(19,223,'GA'),(20,223,'GU'),(21,223,'HI'),(22,223,'ID'),(23,223,'IL'),(24,223,'IN'),(25,223,'IA'),(26,223,'KS'),(27,223,'KY'),(28,223,'LA'),(29,223,'ME'),(30,223,'MH'),(31,223,'MD'),(32,223,'MA'),(33,223,'MI'),(34,223,'MN'),(35,223,'MS'),(36,223,'MO'),(37,223,'MT'),(38,223,'NE'),(39,223,'NV'),(40,223,'NH'),(41,223,'NJ'),(42,223,'NM'),(43,223,'NY'),(44,223,'NC'),(45,223,'ND'),(46,223,'MP'),(47,223,'OH'),(48,223,'OK'),(49,223,'OR'),(50,223,'PW'),(51,223,'PA'),(52,223,'PR'),(53,223,'RI'),(54,223,'SC'),(55,223,'SD'),(56,223,'TN'),(57,223,'TX'),(58,223,'UT'),(59,223,'VT'),(60,223,'VI'),(61,223,'VA'),(62,223,'WA'),(63,223,'WV'),(64,223,'WI'),(65,223,'WY'),(66,38,'AB'),(67,38,'BC'),(68,38,'MB'),(69,38,'NF'),(70,38,'NB'),(71,38,'NS'),(72,38,'NT'),(73,38,'NU'),(74,38,'ON'),(75,38,'PE'),(76,38,'QC'),(77,38,'SK'),(78,38,'YT'),(79,81,'NDS'),(80,81,'BAW'),(81,81,'BAY'),(82,81,'BER'),(83,81,'BRG'),(84,81,'BRE'),(85,81,'HAM'),(86,81,'HES'),(87,81,'MEC'),(88,81,'NRW'),(89,81,'RHE'),(90,81,'SAR'),(91,81,'SAS'),(92,81,'SAC'),(93,81,'SCN'),(94,81,'THE'),(95,14,'WI'),(96,14,'NO'),(97,14,'OO'),(98,14,'SB'),(99,14,'KN'),(100,14,'ST'),(101,14,'TI'),(102,14,'BL'),(103,14,'VB'),(104,204,'AG');
insert into `directory_country_region` (`region_id`,`country_id`,`code`) values (105,204,'AI'),(106,204,'AR'),(107,204,'BE'),(108,204,'BL'),(109,204,'BS'),(110,204,'FR'),(111,204,'GE'),(112,204,'GL'),(113,204,'JU'),(114,204,'JU'),(115,204,'LU'),(116,204,'NE'),(117,204,'NW'),(118,204,'OW'),(119,204,'SG'),(120,204,'SH'),(121,204,'SO'),(122,204,'SZ'),(123,204,'TG'),(124,204,'TI'),(125,204,'UR'),(126,204,'VD'),(127,204,'VS'),(128,204,'ZG'),(129,204,'ZH'),(130,195,'A Coruсa'),(131,195,'Alava'),(132,195,'Albacete'),(133,195,'Alicante'),(134,195,'Almeria'),(135,195,'Asturias'),(136,195,'Avila'),(137,195,'Badajoz'),(138,195,'Baleares'),(139,195,'Barcelona'),(140,195,'Burgos'),(141,195,'Caceres'),(142,195,'Cadiz'),(143,195,'Cantabria'),(144,195,'Castellon'),(145,195,'Ceuta'),(146,195,'Ciudad Real'),(147,195,'Cordoba'),(148,195,'Cuenca'),(149,195,'Girona'),(150,195,'Granada'),(151,195,'Guadalajara'),(152,195,'Guipuzcoa'),(153,195,'Huelva'),(154,195,'Huesca'),(155,195,'Jaen'),(156,195,'La Rioja'),(157,195,'Las Palmas'),(158,195,'Leon'),(159,195,'Lleida'),(160,195,'Lugo'),(161,195,'Madrid'),(162,195,'Malaga'),(163,195,'Melilla'),(164,195,'Murcia'),(165,195,'Navarra'),(166,195,'Ourense'),(167,195,'Palencia'),(168,195,'Pontevedra'),(169,195,'Salamanca'),(170,195,'Santa Cruz de Tenerife'),(171,195,'Segovia'),(172,195,'Sevilla'),(173,195,'Soria'),(174,195,'Tarragona');
insert into `directory_country_region` (`region_id`,`country_id`,`code`) values (175,195,'Teruel'),(176,195,'Toledo'),(177,195,'Valencia'),(178,195,'Valladolid'),(179,195,'Vizcaya'),(180,195,'Zamora'),(181,195,'Zaragoza');

/*Table structure for table `directory_country_region_name` */

DROP TABLE IF EXISTS `directory_country_region_name`;

CREATE TABLE `directory_country_region_name` (
  `language_code` varchar(2) NOT NULL default '',
  `region_id` mediumint(8) unsigned NOT NULL default '0',
  `name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`language_code`,`region_id`),
  KEY `FK_REGION_NAME_REGION` (`region_id`),
  CONSTRAINT `FK_REGION_NAME_LANG` FOREIGN KEY (`language_code`) REFERENCES `core_language` (`language_code`),
  CONSTRAINT `FK_REGION_NAME_REGION` FOREIGN KEY (`region_id`) REFERENCES `directory_country_region` (`region_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Regions names';

/*Data for the table `directory_country_region_name` */

insert into `directory_country_region_name` (`language_code`,`region_id`,`name`) values ('en',1,'Alabama'),('en',2,'Alaska'),('en',3,'American Samoa'),('en',4,'Arizona'),('en',5,'Arkansas'),('en',6,'Armed Forces Africa'),('en',7,'Armed Forces Americas'),('en',8,'Armed Forces Canada'),('en',9,'Armed Forces Europe'),('en',10,'Armed Forces Middle East'),('en',11,'Armed Forces Pacific'),('en',12,'California'),('en',13,'Colorado'),('en',14,'Connecticut'),('en',15,'Delaware'),('en',16,'District of Columbia'),('en',17,'Federated States Of Micronesia'),('en',18,'Florida'),('en',19,'Georgia'),('en',20,'Guam'),('en',21,'Hawaii'),('en',22,'Idaho'),('en',23,'Illinois'),('en',24,'Indiana'),('en',25,'Iowa'),('en',26,'Kansas'),('en',27,'Kentucky'),('en',28,'Louisiana'),('en',29,'Maine'),('en',30,'Marshall Islands'),('en',31,'Maryland'),('en',32,'Massachusetts'),('en',33,'Michigan'),('en',34,'Minnesota'),('en',35,'Mississippi'),('en',36,'Missouri'),('en',37,'Montana'),('en',38,'Nebraska'),('en',39,'Nevada'),('en',40,'New Hampshire'),('en',41,'New Jersey'),('en',42,'New Mexico'),('en',43,'New York'),('en',44,'North Carolina'),('en',45,'North Dakota'),('en',46,'Northern Mariana Islands'),('en',47,'Ohio'),('en',48,'Oklahoma'),('en',49,'Oregon'),('en',50,'Palau'),('en',51,'Pennsylvania'),('en',52,'Puerto Rico'),('en',53,'Rhode Island'),('en',54,'South Carolina'),('en',55,'South Dakota'),('en',56,'Tennessee'),('en',57,'Texas'),('en',58,'Utah');
insert into `directory_country_region_name` (`language_code`,`region_id`,`name`) values ('en',59,'Vermont'),('en',60,'Virgin Islands'),('en',61,'Virginia'),('en',62,'Washington'),('en',63,'West Virginia'),('en',64,'Wisconsin'),('en',65,'Wyoming'),('en',66,'Alberta'),('en',67,'British Columbia'),('en',68,'Manitoba'),('en',69,'Newfoundland'),('en',70,'New Brunswick'),('en',71,'Nova Scotia'),('en',72,'Northwest Territories'),('en',73,'Nunavut'),('en',74,'Ontario'),('en',75,'Prince Edward Island'),('en',76,'Quebec'),('en',77,'Saskatchewan'),('en',78,'Yukon Territory'),('en',79,'Niedersachsen'),('en',80,'Baden-Wьrttemberg'),('en',81,'Bayern'),('en',82,'Berlin'),('en',83,'Brandenburg'),('en',84,'Bremen'),('en',85,'Hamburg'),('en',86,'Hessen'),('en',87,'Mecklenburg-Vorpommern'),('en',88,'Nordrhein-Westfalen'),('en',89,'Rheinland-Pfalz'),('en',90,'Saarland'),('en',91,'Sachsen'),('en',92,'Sachsen-Anhalt'),('en',93,'Schleswig-Holstein'),('en',94,'Thьringen'),('en',95,'Wien'),('en',96,'Niederцsterreich'),('en',97,'Oberцsterreich'),('en',98,'Salzburg'),('en',99,'Kдrnten'),('en',100,'Steiermark'),('en',101,'Tirol'),('en',102,'Burgenland'),('en',103,'Voralberg'),('en',104,'Aargau'),('en',105,'Appenzell Innerrhoden'),('en',106,'Appenzell Ausserrhoden'),('en',107,'Bern'),('en',108,'Basel-Landschaft'),('en',109,'Basel-Stadt'),('en',110,'Freiburg'),('en',111,'Genf'),('en',112,'Glarus'),('en',113,'Graubьnden'),('en',114,'Jura');
insert into `directory_country_region_name` (`language_code`,`region_id`,`name`) values ('en',115,'Luzern'),('en',116,'Neuenburg'),('en',117,'Nidwalden'),('en',118,'Obwalden'),('en',119,'St. Gallen'),('en',120,'Schaffhausen'),('en',121,'Solothurn'),('en',122,'Schwyz'),('en',123,'Thurgau'),('en',124,'Tessin'),('en',125,'Uri'),('en',126,'Waadt'),('en',127,'Wallis'),('en',128,'Zug'),('en',129,'Zьrich'),('en',130,'A Coruсa'),('en',131,'Alava'),('en',132,'Albacete'),('en',133,'Alicante'),('en',134,'Almeria'),('en',135,'Asturias'),('en',136,'Avila'),('en',137,'Badajoz'),('en',138,'Baleares'),('en',139,'Barcelona'),('en',140,'Burgos'),('en',141,'Caceres'),('en',142,'Cadiz'),('en',143,'Cantabria'),('en',144,'Castellon'),('en',145,'Ceuta'),('en',146,'Ciudad Real'),('en',147,'Cordoba'),('en',148,'Cuenca'),('en',149,'Girona'),('en',150,'Granada'),('en',151,'Guadalajara'),('en',152,'Guipuzcoa'),('en',153,'Huelva'),('en',154,'Huesca'),('en',155,'Jaen'),('en',156,'La Rioja'),('en',157,'Las Palmas'),('en',158,'Leon'),('en',159,'Lleida'),('en',160,'Lugo'),('en',161,'Madrid'),('en',162,'Malaga'),('en',163,'Melilla'),('en',164,'Murcia'),('en',165,'Navarra'),('en',166,'Ourense'),('en',167,'Palencia'),('en',168,'Pontevedra'),('en',169,'Salamanca'),('en',170,'Santa Cruz de Tenerife'),('en',171,'Segovia'),('en',172,'Sevilla'),('en',173,'Soria'),('en',174,'Tarragona'),('en',175,'Teruel'),('en',176,'Toledo'),('en',177,'Valencia'),('en',178,'Valladolid'),('en',179,'Vizcaya'),('en',180,'Zamora');
insert into `directory_country_region_name` (`language_code`,`region_id`,`name`) values ('en',181,'Zaragoza');

/*Table structure for table `directory_currency` */

DROP TABLE IF EXISTS `directory_currency`;

CREATE TABLE `directory_currency` (
  `currency_id` smallint(6) unsigned NOT NULL auto_increment,
  `code` char(3) NOT NULL default '',
  `currency_symbol` char(1) default NULL,
  PRIMARY KEY  (`currency_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Currency';

/*Data for the table `directory_currency` */

insert into `directory_currency` (`currency_id`,`code`,`currency_symbol`) values (1,'USD','$');

/*Table structure for table `directory_currency_name` */

DROP TABLE IF EXISTS `directory_currency_name`;

CREATE TABLE `directory_currency_name` (
  `language_code` varchar(2) NOT NULL default '',
  `currency_id` smallint(6) unsigned NOT NULL default '0',
  `name` varchar(64) NOT NULL default '',
  PRIMARY KEY  (`language_code`,`currency_id`),
  KEY `FK_CURRENCY_NAME_CURRENCY` (`currency_id`),
  CONSTRAINT `FK_CURENCY_NAME_LANG` FOREIGN KEY (`language_code`) REFERENCES `core_language` (`language_code`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CURRENCY_NAME_CURRENCY` FOREIGN KEY (`currency_id`) REFERENCES `directory_currency` (`currency_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Currency names';

/*Data for the table `directory_currency_name` */

insert into `directory_currency_name` (`language_code`,`currency_id`,`name`) values ('en',1,'Dollar USA');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
