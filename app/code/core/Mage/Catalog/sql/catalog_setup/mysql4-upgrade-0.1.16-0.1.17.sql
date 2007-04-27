alter table `magenta`.`catalog_product_attribute` 
,add column `editable` tinyint (1) DEFAULT '1' NOT NULL  after `deletable`
,change `required` `required` tinyint (1)UNSIGNED  DEFAULT '0' NOT NULL 
, change `comparale` `comparable` tinyint (1)UNSIGNED  DEFAULT '1' NOT NULL 
, change `delitable` `deletable` tinyint (1)UNSIGNED  DEFAULT '1' NOT NULL;
