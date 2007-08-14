alter table `tax_rule` CHANGE `tax_rate_id` `tax_rate_type_id` TINYINT(4) DEFAULT '0' NOT NULL;
ALTER TABLE `tax_rate` CHANGE `tax_zip_code` `tax_postcode` VARCHAR(12);
