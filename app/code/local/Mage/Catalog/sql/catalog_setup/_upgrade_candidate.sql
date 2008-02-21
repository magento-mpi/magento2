CREATE TABLE `magento_dmitriy_full`.`catalog_product_website` (
  `product_id` INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `website_id` SMALLINT(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`product_id`, `website_id`),
  CONSTRAINT `FK_CATALOG_PRODUCT_WEBSITE_WEBSITE` FOREIGN KEY `FK_CATALOG_PRODUCT_WEBSITE_WEBSITE` (`website_id`)
    REFERENCES `core_website` (`website_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `FK_CATALOG_WEBSITE_PRODUCT_PRODUCT` FOREIGN KEY `FK_CATALOG_WEBSITE_PRODUCT_PRODUCT` (`product_id`)
    REFERENCES `catalog_product_entity` (`entity_id`)
    ON DELETE CASCADE;


ALTER TABLE `magento_dmitriy_full`.`catalog_product_entity` 
 DROP COLUMN `parent_id`,
 DROP COLUMN `store_id`,
 DROP COLUMN `is_active`, 
 DROP INDEX `FK_CATALOG_PRODUCT_ENTITY_STORE_ID`,
 DROP FOREIGN KEY `FK_CATALOG_PRODUCT_ENTITY_STORE_ID`;


drop table `catalog_product_status`;
drop table `catalog_product_visibility`;
drop table `catalog_product_type`;


// !!!convert data to website table before drop
//insert into catalog_product_website select entity_id, 1 from catalog_product_entity
drop table `catalog_product_store`;


ALTER TABLE `magento_dmitriy_full`.`catalog_category_entity` DROP COLUMN `store_id`
, DROP INDEX `FK_catalog_category_ENTITY_ENTITY_TYPE`
, DROP INDEX `FK_catalog_category_ENTITY_STORE`
, DROP INDEX `path`
, DROP INDEX `position`,
 ADD INDEX FK_CATALOG_CATEGORY_ENTITY_ENTITY_TYPE USING BTREE(`entity_type_id`),
 ADD INDEX IDX_PATH USING BTREE(`path`),
 ADD INDEX IDX_POSITION USING BTREE(`position`);


ALTER TABLE `magento_dmitriy_full`.`catalog_product_entity_tier_price` DROP COLUMN `entity_type_id`,
 DROP COLUMN `attribute_id`
, DROP INDEX `FK_CATALOG_PRODUCT_ENTITY_TIER_PRICE_ATTRIBUTE`,
 DROP FOREIGN KEY `FK_CATALOG_PRODUCT_ENTITY_TIER_PRICE_ATTRIBUTE`;