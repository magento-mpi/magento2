replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values 
(3,'carriers/tablerate/export','Export','export','','','','',5,0,1,0,''),
(3,'carriers/tablerate/import','Import','textarea','','','adminhtml/system_config_backend_shipping_tablerate','',6,0,1,0,'');

alter table `shipping_tablerate` add column `website_id` int(11) not null after `pk`, drop key `dest_country`, add unique key `dest_country` (`website_id`, `dest_country_id`, `dest_region_id`, `condition_name`, `condition_value`);

