rename table `catalog_category_attribute_set` to `catalog_category_attribute_in_set`;
rename table `catalog_category_set` to `catalog_category_attribute_set`;
alter table `catalog_category_attribute_in_set` drop foreign key `FK_CATEGORY_SET`;
alter table `catalog_category_attribute_set` ,change `category_set_id` `category_attribute_set_id` smallint (6) UNSIGNED   NOT NULL AUTO_INCREMENT;
alter table `catalog_category_attribute_in_set` ,change `category_set_id` `category_attribute_set_id` smallint (6) UNSIGNED  DEFAULT '0' NOT NULL;
alter table `catalog_category_attribute_in_set` add foreign key `FK_CATEGORY_ATTRIBUTE_SET_ID`(`category_attribute_set_id`) references `catalog_category_attribute_set` (`category_attribute_set_id`) on delete cascade  on update cascade;
alter table `catalog_category_attribute_set` ,change `category_set_code` `category_attribute_set_code` varchar (32)   NOT NULL  COLLATE utf8_general_ci;
