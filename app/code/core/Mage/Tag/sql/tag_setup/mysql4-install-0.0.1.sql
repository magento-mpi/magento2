DROP TABLE IF EXISTS `tag`;
CREATE TABLE  `tag` (
  `tag_id` int(10) unsigned NOT NULL auto_increment,
  `tagname` varchar(45) NOT NULL default '',
  `status` int(10) unsigned NOT NULL default '0',
  `store_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  USING BTREE (`tag_id`),
  KEY `Index_2` USING BTREE (`tagname`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `tag_entity`;
CREATE TABLE  `tag_entity` (
  `tag_entity_id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(45) NOT NULL default '',
  PRIMARY KEY  USING BTREE (`tag_entity_id`)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS `tag_relations`;
CREATE TABLE  `tag_relations` (
  `tag_relations_id` int(10) unsigned NOT NULL auto_increment,
  `tag_id` int(10) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `entity_val_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  USING BTREE (`tag_relations_id`),
  UNIQUE KEY `Index_2` (`tag_id`,`entity_id`,`entity_val_id`)
) ENGINE=InnoDB;

ALTER TABLE `tag_entity` ADD UNIQUE INDEX `Index_2`(`title`);