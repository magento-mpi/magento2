/* Table structure update */

ALTER TABLE `catalog_category_entity_gallery` ADD COLUMN `type` smallint(5) NOT NULL COMMENT '0 - full size image, 1 - thumbnail, 2 - small thumb' AFTER `position`;

