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
) ENGINE=MyISAM DEFAULT CHARSET=latin1;