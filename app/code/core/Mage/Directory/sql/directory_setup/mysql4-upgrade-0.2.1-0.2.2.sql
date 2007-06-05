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
  `iso2_code` char(2) NOT NULL default '',
  `iso3_code` char(3) NOT NULL default '',
  PRIMARY KEY  (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Countries';

/*Data for the table `directory_country` */

insert into `directory_country` (`country_id`,`iso2_code`,`iso3_code`) values (1,'AF','AFG'),(2,'AL','ALB'),(3,'DZ','DZA'),(4,'AS','ASM'),(5,'AD','AND'),(6,'AO','AGO'),(7,'AI','AIA'),(8,'AQ','ATA'),(9,'AG','ATG'),(10,'AR','ARG'),(11,'AM','ARM'),(12,'AW','ABW'),(13,'AU','AUS'),(14,'AT','AUT'),(15,'AZ','AZE'),(16,'BS','BHS'),(17,'BH','BHR'),(18,'BD','BGD'),(19,'BB','BRB'),(20,'BY','BLR'),(21,'BE','BEL'),(22,'BZ','BLZ'),(23,'BJ','BEN'),(24,'BM','BMU'),(25,'BT','BTN'),(26,'BO','BOL'),(27,'BA','BIH'),(28,'BW','BWA'),(29,'BV','BVT'),(30,'BR','BRA'),(31,'IO','IOT'),(32,'BN','BRN'),(33,'BG','BGR'),(34,'BF','BFA'),(35,'BI','BDI'),(36,'KH','KHM'),(37,'CM','CMR'),(38,'CA','CAN'),(39,'CV','CPV'),(40,'KY','CYM'),(41,'CF','CAF'),(42,'TD','TCD'),(43,'CL','CHL'),(44,'CN','CHN'),(45,'CX','CXR'),(46,'CC','CCK'),(47,'CO','COL'),(48,'KM','COM'),(49,'CG','COG'),(50,'CK','COK'),(51,'CR','CRI'),(52,'CI','CIV'),(53,'HR','HRV'),(54,'CU','CUB'),(55,'CY','CYP'),(56,'CZ','CZE'),(57,'DK','DNK'),(58,'DJ','DJI'),(59,'DM','DMA'),(60,'DO','DOM'),(61,'TP','TMP'),(62,'EC','ECU'),(63,'EG','EGY'),(64,'SV','SLV'),(65,'GQ','GNQ'),(66,'ER','ERI'),(67,'EE','EST'),(68,'ET','ETH'),(69,'FK','FLK'),(70,'FO','FRO'),(71,'FJ','FJI'),(72,'FI','FIN'),(73,'FR','FRA'),(74,'FX','FXX'),(75,'GF','GUF'),(76,'PF','PYF'),(77,'TF','ATF'),(78,'GA','GAB'),(79,'GM','GMB'),(80,'GE','GEO'),(81,'DE','DEU'),(82,'GH','GHA'),(83,'GI','GIB'),(84,'GR','GRC'),(85,'GL','GRL'),(86,'GD','GRD'),(87,'GP','GLP'),(88,'GU','GUM'),(89,'GT','GTM'),(90,'GN','GIN'),(91,'GW','GNB'),(92,'GY','GUY'),(93,'HT','HTI'),(94,'HM','HMD');
insert into `directory_country` (`country_id`,`iso2_code`,`iso3_code`) values (95,'HN','HND'),(96,'HK','HKG'),(97,'HU','HUN'),(98,'IS','ISL'),(99,'IN','IND'),(100,'ID','IDN'),(101,'IR','IRN'),(102,'IQ','IRQ'),(103,'IE','IRL'),(104,'IL','ISR'),(105,'IT','ITA'),(106,'JM','JAM'),(107,'JP','JPN'),(108,'JO','JOR'),(109,'KZ','KAZ'),(110,'KE','KEN'),(111,'KI','KIR'),(112,'KP','PRK'),(113,'KR','KOR'),(114,'KW','KWT'),(115,'KG','KGZ'),(116,'LA','LAO'),(117,'LV','LVA'),(118,'LB','LBN'),(119,'LS','LSO'),(120,'LR','LBR'),(121,'LY','LBY'),(122,'LI','LIE'),(123,'LT','LTU'),(124,'LU','LUX'),(125,'MO','MAC'),(126,'MK','MKD'),(127,'MG','MDG'),(128,'MW','MWI'),(129,'MY','MYS'),(130,'MV','MDV'),(131,'ML','MLI'),(132,'MT','MLT'),(133,'MH','MHL'),(134,'MQ','MTQ'),(135,'MR','MRT'),(136,'MU','MUS'),(137,'YT','MYT'),(138,'MX','MEX'),(139,'FM','FSM'),(140,'MD','MDA'),(141,'MC','MCO'),(142,'MN','MNG'),(143,'MS','MSR'),(144,'MA','MAR'),(145,'MZ','MOZ'),(146,'MM','MMR'),(147,'NA','NAM'),(148,'NR','NRU'),(149,'NP','NPL'),(150,'NL','NLD'),(151,'AN','ANT'),(152,'NC','NCL'),(153,'NZ','NZL'),(154,'NI','NIC'),(155,'NE','NER'),(156,'NG','NGA'),(157,'NU','NIU'),(158,'NF','NFK'),(159,'MP','MNP'),(160,'NO','NOR'),(161,'OM','OMN'),(162,'PK','PAK'),(163,'PW','PLW'),(164,'PA','PAN'),(165,'PG','PNG'),(166,'PY','PRY'),(167,'PE','PER'),(168,'PH','PHL'),(169,'PN','PCN'),(170,'PL','POL'),(171,'PT','PRT'),(172,'PR','PRI'),(173,'QA','QAT'),(174,'RE','REU'),(175,'RO','ROM'),(176,'RU','RUS'),(177,'RW','RWA'),(178,'KN','KNA'),(179,'LC','LCA'),(180,'VC','VCT'),(181,'WS','WSM'),(182,'SM','SMR'),(183,'ST','STP'),(184,'SA','SAU'),(185,'SN','SEN'),(186,'SC','SYC'),(187,'SL','SLE');
insert into `directory_country` (`country_id`,`iso2_code`,`iso3_code`) values (188,'SG','SGP'),(189,'SK','SVK'),(190,'SI','SVN'),(191,'SB','SLB'),(192,'SO','SOM'),(193,'ZA','ZAF'),(194,'GS','SGS'),(195,'ES','ESP'),(196,'LK','LKA'),(197,'SH','SHN'),(198,'PM','SPM'),(199,'SD','SDN'),(200,'SR','SUR'),(201,'SJ','SJM'),(202,'SZ','SWZ'),(203,'SE','SWE'),(204,'CH','CHE'),(205,'SY','SYR'),(206,'TW','TWN'),(207,'TJ','TJK'),(208,'TZ','TZA'),(209,'TH','THA'),(210,'TG','TGO'),(211,'TK','TKL'),(212,'TO','TON'),(213,'TT','TTO'),(214,'TN','TUN'),(215,'TR','TUR'),(216,'TM','TKM'),(217,'TC','TCA'),(218,'TV','TUV'),(219,'UG','UGA'),(220,'UA','UKR'),(221,'AE','ARE'),(222,'GB','GBR'),(223,'US','USA'),(224,'UM','UMI'),(225,'UY','URY'),(226,'UZ','UZB'),(227,'VU','VUT'),(228,'VA','VAT'),(229,'VE','VEN'),(230,'VN','VNM'),(231,'VG','VGB'),(232,'VI','VIR'),(233,'WF','WLF'),(234,'EH','ESH'),(235,'YE','YEM'),(236,'YU','YUG'),(237,'ZR','ZAR'),(238,'ZM','ZMB'),(239,'ZW','ZWE');

/*Table structure for table `directory_country_currency` */

DROP TABLE IF EXISTS `directory_country_currency`;

CREATE TABLE `directory_country_currency` (
  `country_id` smallint(6) NOT NULL default '0',
  `currency_code` char(3) NOT NULL default '0',
  PRIMARY KEY  USING BTREE (`country_id`,`currency_code`),
  KEY `FK_COUNTRY_CURRENCY` USING BTREE (`currency_code`),
  CONSTRAINT `FK_CURRENCY_COUNTRY` FOREIGN KEY (`country_id`) REFERENCES `directory_country` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Currency per country';

/*Data for the table `directory_country_currency` */

insert into `directory_country_currency` (`country_id`,`currency_code`) values (223,'USD');

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
insert into `directory_country_region_name` (`language_code`,`region_id`,`name`) values ('en',59,'Vermont'),('en',60,'Virgin Islands'),('en',61,'Virginia'),('en',62,'Washington'),('en',63,'West Virginia'),('en',64,'Wisconsin'),('en',65,'Wyoming'),('en',66,'Alberta'),('en',67,'British Columbia'),('en',68,'Manitoba'),('en',69,'Newfoundland'),('en',70,'New Brunswick'),('en',71,'Nova Scotia'),('en',72,'Northwest Territories'),('en',73,'Nunavut'),('en',74,'Ontario'),('en',75,'Prince Edward Island'),('en',76,'Quebec'),('en',77,'Saskatchewan'),('en',78,'Yukon Territory'),('en',79,'Niedersachsen'),('en',80,'Baden-WÑŒrttemberg'),('en',81,'Bayern'),('en',82,'Berlin'),('en',83,'Brandenburg'),('en',84,'Bremen'),('en',85,'Hamburg'),('en',86,'Hessen'),('en',87,'Mecklenburg-Vorpommern'),('en',88,'Nordrhein-Westfalen'),('en',89,'Rheinland-Pfalz'),('en',90,'Saarland'),('en',91,'Sachsen'),('en',92,'Sachsen-Anhalt'),('en',93,'Schleswig-Holstein'),('en',94,'ThÑŒringen'),('en',95,'Wien'),('en',96,'NiederÑ†sterreich'),('en',97,'OberÑ†sterreich'),('en',98,'Salzburg'),('en',99,'KÐ´rnten'),('en',100,'Steiermark'),('en',101,'Tirol'),('en',102,'Burgenland'),('en',103,'Voralberg'),('en',104,'Aargau'),('en',105,'Appenzell Innerrhoden'),('en',106,'Appenzell Ausserrhoden'),('en',107,'Bern'),('en',108,'Basel-Landschaft'),('en',109,'Basel-Stadt'),('en',110,'Freiburg'),('en',111,'Genf'),('en',112,'Glarus'),('en',113,'GraubÑŒnden');
insert into `directory_country_region_name` (`language_code`,`region_id`,`name`) values ('en',114,'Jura'),('en',115,'Luzern'),('en',116,'Neuenburg'),('en',117,'Nidwalden'),('en',118,'Obwalden'),('en',119,'St. Gallen'),('en',120,'Schaffhausen'),('en',121,'Solothurn'),('en',122,'Schwyz'),('en',123,'Thurgau'),('en',124,'Tessin'),('en',125,'Uri'),('en',126,'Waadt'),('en',127,'Wallis'),('en',128,'Zug'),('en',129,'ZÑŒrich'),('en',130,'A CoruÑ?a'),('en',131,'Alava'),('en',132,'Albacete'),('en',133,'Alicante'),('en',134,'Almeria'),('en',135,'Asturias'),('en',136,'Avila'),('en',137,'Badajoz'),('en',138,'Baleares'),('en',139,'Barcelona'),('en',140,'Burgos'),('en',141,'Caceres'),('en',142,'Cadiz'),('en',143,'Cantabria'),('en',144,'Castellon'),('en',145,'Ceuta'),('en',146,'Ciudad Real'),('en',147,'Cordoba'),('en',148,'Cuenca'),('en',149,'Girona'),('en',150,'Granada'),('en',151,'Guadalajara'),('en',152,'Guipuzcoa'),('en',153,'Huelva'),('en',154,'Huesca'),('en',155,'Jaen'),('en',156,'La Rioja'),('en',157,'Las Palmas'),('en',158,'Leon'),('en',159,'Lleida'),('en',160,'Lugo'),('en',161,'Madrid'),('en',162,'Malaga'),('en',163,'Melilla'),('en',164,'Murcia'),('en',165,'Navarra'),('en',166,'Ourense'),('en',167,'Palencia'),('en',168,'Pontevedra'),('en',169,'Salamanca'),('en',170,'Santa Cruz de Tenerife'),('en',171,'Segovia'),('en',172,'Sevilla'),('en',173,'Soria'),('en',174,'Tarragona'),('en',175,'Teruel'),('en',176,'Toledo'),('en',177,'Valencia'),('en',178,'Valladolid'),('en',179,'Vizcaya');
insert into `directory_country_region_name` (`language_code`,`region_id`,`name`) values ('en',180,'Zamora'),('en',181,'Zaragoza');

/*Table structure for table `directory_currency` */

DROP TABLE IF EXISTS `directory_currency`;

CREATE TABLE `directory_currency` (
  `currency_code` varchar(3) NOT NULL default '',
  PRIMARY KEY  (`currency_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Currency';

/*Data for the table `directory_currency` */

insert into `directory_currency` (`currency_code`) values ('CAD'),('EUR'),('RUB'),('UAH'),('USD');

/*Table structure for table `directory_currency_name` */

DROP TABLE IF EXISTS `directory_currency_name`;

CREATE TABLE `directory_currency_name` (
  `language_code` varchar(2) NOT NULL default '',
  `currency_code` varchar(3) NOT NULL default '',
  `currency_name` varchar(64) NOT NULL default '',
  `format` varchar(32) NOT NULL default '%s',
  `format_decimals` tinyint(2) NOT NULL default '2',
  `format_dec_point` varchar(8) NOT NULL default '.',
  `format_thousands_sep` varchar(8) NOT NULL default '',
  PRIMARY KEY  USING BTREE (`language_code`,`currency_code`),
  KEY `FK_CURRENCY_NAME_CURRENCY` USING BTREE (`currency_code`),
  CONSTRAINT `FK_CURRENCY_NAME_CURRENCY` FOREIGN KEY (`currency_code`) REFERENCES `directory_currency` (`currency_code`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CURRENCY_NAME_LANG` FOREIGN KEY (`language_code`) REFERENCES `core_language` (`language_code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Currency names';

/*Data for the table `directory_currency_name` */

insert into `directory_currency_name` (`language_code`,`currency_code`,`currency_name`,`format`,`format_decimals`,`format_dec_point`,`format_thousands_sep`) values ('en','CAD','Canadian Dollar','%s CAD',2,'.',''),('en','EUR','Euro','%s EUR',2,'.',''),('en','RUB','Russian Rouble','%s RUB',2,'.',''),('en','UAH','Ukraine Hryvnia','%s UAH',2,'.',''),('en','USD','U.S. Dollar','$ %s',2,'.',''),('ru','CAD','Доллар(Канада)','%s к.д.',2,'.',''),('ru','EUR','Евро','%s евро',2,'.',''),('ru','RUB','Рубль','%s руб.',2,'.',''),('ru','UAH','Гривна','%s грн.',2,'.',''),('ru','USD','Доллар(США)','$ %s',2,'.','');

/*Table structure for table `directory_currency_rate` */

DROP TABLE IF EXISTS `directory_currency_rate`;

CREATE TABLE `directory_currency_rate` (
  `currency_from` char(3) NOT NULL default '',
  `currency_to` char(3) NOT NULL default '',
  `rate` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`currency_from`,`currency_to`),
  KEY `FK_CURRENCY_RATE_TO` (`currency_to`),
  CONSTRAINT `FK_CURRENCY_RATE_FROM` FOREIGN KEY (`currency_from`) REFERENCES `directory_currency` (`currency_code`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CURRENCY_RATE_TO` FOREIGN KEY (`currency_to`) REFERENCES `directory_currency` (`currency_code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `directory_currency_rate` */

insert into `directory_currency_rate` (`currency_from`,`currency_to`,`rate`) values ('CAD','RUB',24.3215),('CAD','UAH',4.7308),('CAD','USD',0.9377),('RUB','CAD',0.0411),('RUB','UAH',0.1946),('RUB','USD',0.0386),('UAH','CAD',0.2113),('UAH','RUB',5.1417),('UAH','USD',0.1983),('USD','CAD',1.0663),('USD','RUB',25.9296),('USD','UAH',5.0430);

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
