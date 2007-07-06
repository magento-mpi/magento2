CREATE TABLE  `admin_assert` (
  `assert_id` int(10) unsigned NOT NULL auto_increment,
  `assert_type` varchar(20) NOT NULL default '',
  `assert_data` text,
  PRIMARY KEY  (`assert_id`)
) ENGINE=InnoDB;

CREATE TABLE  `admin_role` (
  `role_id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned NOT NULL default '0',
  `tree_level` tinyint(3) unsigned NOT NULL default '0',
  `sort_order` tinyint(3) unsigned NOT NULL default '0',
  `role_type` char(1) NOT NULL default '0',
  `user_id` int(11) unsigned NOT NULL default '0',
  `role_name` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`role_id`),
  KEY `parent_id` (`parent_id`,`sort_order`),
  KEY `tree_level` (`tree_level`)
) ENGINE=InnoDB;

CREATE TABLE  `admin_rule` (
  `rule_id` int(10) unsigned NOT NULL auto_increment,
  `role_type` char(1) NOT NULL default '',
  `role_id` int(10) unsigned NOT NULL default '0',
  `resource_id` varchar(255) NOT NULL default '',
  `privileges` varchar(20) NOT NULL default '',
  `assert_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`rule_id`),
  KEY `resource` (`resource_id`,`role_id`),
  KEY `role_id` (`role_type`,`role_id`,`resource_id`)
) ENGINE=InnoDB;

CREATE TABLE  `admin_user` (
  `user_id` mediumint(9) unsigned NOT NULL auto_increment,
  `firstname` varchar(32) NOT NULL default '',
  `lastname` varchar(32) NOT NULL default '',
  `email` varchar(128) NOT NULL default '',
  `username` varchar(40) NOT NULL default '',
  `password` varchar(40) NOT NULL default '',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` datetime default NULL,
  `logdate` datetime default NULL,
  `lognum` smallint(5) unsigned NOT NULL default '0',
  `reload_acl_flag` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`user_id`)
) ENGINE=InnoDB;

CREATE TABLE  `admin_users_in_roles` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `role_id` int(10) unsigned NOT NULL default '0',
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `Index_4` (`role_id`,`user_id`),
  KEY `FK_admin_users_in_roles_1` (`user_id`),
  CONSTRAINT `FK_admin_users_in_roles_1` FOREIGN KEY (`user_id`) REFERENCES `admin_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_admin_users_in_roles_2` FOREIGN KEY (`role_id`) REFERENCES `admin_role` (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

ALTER TABLE `admin_rule` MODIFY COLUMN `privileges` VARCHAR(255);