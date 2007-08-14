replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values 
(3,'carriers/tablerate/condition_name','Condition','select','','','','adminhtml/system_config_source_shipping_tablerate',4,1,1,1,'');

replace  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values 
('default',0,'carriers/tablerate/condition_name','package_weight','',0);

