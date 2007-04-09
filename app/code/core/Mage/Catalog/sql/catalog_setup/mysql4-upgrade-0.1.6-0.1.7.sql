alter table `catalog_category` ,add column `attribute_set_id` smallint (6) UNSIGNED  DEFAULT '1' NOT NULL  after `level`;
ALTER TABLE `magenta`.`catalog_category` ADD CONSTRAINT `FK_CATEGORY_ATTRIBUTE_SET` FOREIGN KEY `FK_CATEGORY_ATTRIBUTE_SET` (`attribute_set_id`)
    REFERENCES `catalog_category_attribute_set` (`category_attribute_set_id`)
    ON DELETE RESTRICT
    ON UPDATE RESTRICT;
