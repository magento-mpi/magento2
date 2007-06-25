CREATE TABLE  `catalog_product_tags` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tag_id` int(10) unsigned NOT NULL default '0',
  `product_id` int(10) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `Index_2` USING BTREE (`tag_id`,`product_id`,`user_id`),
  CONSTRAINT `FK_catalog_product_tags_1` FOREIGN KEY (`tag_id`) REFERENCES `catalog_tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE  `catalog_tags` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tag_name` varchar(50) NOT NULL default '',
  `status` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB;