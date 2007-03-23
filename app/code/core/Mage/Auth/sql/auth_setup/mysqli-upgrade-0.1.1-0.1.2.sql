
DROP TABLE IF EXISTS `acl_user`;
DROP TABLE IF EXISTS `acl_group`;
DROP TABLE IF EXISTS `acl_group_user`;
DROP TABLE IF EXISTS `acl_group_privilege`;
DROP TABLE IF EXISTS `acl_user_privilege`;
DROP TABLE IF EXISTS `acl_resource`;

DROP TABLE IF EXISTS `auth_acl`;

DROP TABLE IF EXISTS `auth_assert`;
CREATE TABLE `auth_assert` (
  `assert_id` int(10) unsigned NOT NULL auto_increment,
  `assert_type` varchar(20) NOT NULL default '',
  `assert_data` text,
  PRIMARY KEY  (`assert_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='ACL Asserts';

DROP TABLE IF EXISTS `auth_role`;
CREATE TABLE `auth_role` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='ACL Roles';

DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule` (
  `rule_id` int(10) unsigned NOT NULL auto_increment,
  `role_type` char(1) NOT NULL default '',
  `role_id` int(10) unsigned NOT NULL default '0',
  `resource_id` varchar(255) NOT NULL default '',
  `privileges` varchar(20) NOT NULL default '',
  `permission` tinyint(1) unsigned NOT NULL default '1',
  `assert_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`rule_id`),
  KEY `resource` (`resource_id`,`role_id`),
  KEY `role_id` (`role_type`,`role_id`,`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='ACL Rules';

DROP TABLE IF EXISTS `auth_user`;
CREATE TABLE `auth_user` (
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Users';

insert into `auth_user` (user_id, firstname, lastname, email, username, password) values
(1, 'Moshe', 'Gurvich', 'moshe@varien.com', 'moshe', md5('123123')),
(2, 'Andrey', 'Korolyov', 'andrey@varien.com', 'andrey', md5('123123')),
(3, 'Dmitriy', 'Soroka', 'dmitriy@varien.com', 'dmitriy', md5('123123'));

insert into `auth_role` (role_id, parent_id, tree_level, role_type, user_id, role_name) values
(1, 0, 1, 'G', 0, 'Developers'),
(2, 0, 1, 'G', 0, 'Administrators'),
(3, 0, 1, 'G', 0, 'Users'),
(4, 1, 2, 'U', 1, 'Moshe Gurvich'),
(5, 1, 2, 'U', 2, 'Andrey Korolyov'),
(6, 1, 2, 'U', 3, 'Dmitriy Soroka');

insert into `auth_rule` (role_type, role_id, resource_id, `privileges`, permission) values
('G', 1, 'system', 'all', 2);

