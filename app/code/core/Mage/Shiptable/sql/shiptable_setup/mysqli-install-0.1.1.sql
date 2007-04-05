DROP TABLE IF EXISTS `shiptable_data`;

CREATE TABLE `shiptable_data` (
  `pk` int(10) unsigned NOT NULL auto_increment,
  `dest_country` char(2) NOT NULL default '',
  `dest_region` varchar(10) NOT NULL default '',
  `dest_zip` varchar(10) NOT NULL default '',
  `condition_name` varchar(20) NOT NULL default '',
  `condition_value` decimal(12,4) NOT NULL default '0.0000',
  `price` decimal(12,4) NOT NULL default '0.0000',
  `cost` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`pk`),
  UNIQUE KEY `dest_country` (`dest_country`,`dest_region`,`condition_name`,`condition_value`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

alter table `shiptable_data` 
    , change `dest_country` `dest_country_id` int (10)  NOT NULL  COLLATE utf8_general_ci 
    , change `dest_region` `dest_region_id` int (10)  NOT NULL  COLLATE utf8_general_ci ;

insert into `shiptable_data` (dest_country_id, dest_region_id, condition_name, condition_value, price, cost) values 
    (223, 1, 'package_weight', 100, 10, 5),    
    (223, 1, 'package_weight', 1000, 20, 10);