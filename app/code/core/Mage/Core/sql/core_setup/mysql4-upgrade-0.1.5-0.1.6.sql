DROP TABLE IF EXISTS `core_resource`;
CREATE TABLE `core_resource` (
  `resource_name` varchar(50) NOT NULL default '',
  `resource_db_version` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`resource_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;