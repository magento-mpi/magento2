alter table `magenta`.`sales_discount_coupon` 
, add column `discount_fixed` decimal (10,4) DEFAULT '0' NOT NULL  after `discount_percent`
, add column `from_date` datetime   NOT NULL  after `discount_fixed`
, add column `is_active` tinyint (1) DEFAULT '1' NOT NULL  after `discount_fixed` 
, add column `to_date` datetime   NOT NULL  after `from_date`
, add column `min_subtotal` decimal (12,4)  NOT NULL  after `to_date`
, add column `limit_products` text   NOT NULL  after `min_subtotal`
, add column `limit_categories` text   NOT NULL  after `limit_products`
, add column `limit_attributes` text   NOT NULL  after `limit_categories`;
