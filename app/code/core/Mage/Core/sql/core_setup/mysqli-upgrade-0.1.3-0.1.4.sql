drop table if exists core_config;
CREATE TABLE `core_config` (
  `config_id` int(10) unsigned NOT NULL auto_increment,
  `config_module` varchar(255) NOT NULL default '',
  `config_key` varchar(255) NOT NULL default '',
  `config_value` text,
  `value_input_type` varchar(50) default NULL,
  `value_source` varchar(255) default NULL,
  PRIMARY KEY  (`config_id`),
  UNIQUE KEY `config_key` (`config_key`),
  KEY `config_module` (`config_module`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

insert into core_config (config_module, config_key, config_value, value_input_type) 
values ('Mage_Sales', '/config/global/shipping/ups/active', 'True', 'checkbox');