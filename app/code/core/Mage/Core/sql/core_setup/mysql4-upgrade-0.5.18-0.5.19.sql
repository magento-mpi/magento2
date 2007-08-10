replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values 
(3,'carriers/fedex/account','Account ID','text','','','','',3,1,1,1,''),
(3,'carriers/fedex/packaging','Packaging','select','','','','usa/shipping_carrier_fedex_source_packaging',4,1,1,1,''),
(3,'carriers/fedex/dropoff','Dropoff','select','','','','usa/shipping_carrier_fedex_source_dropoff',5,1,1,1,''),
(3,'carriers/fedex/handling','Handling fee','text','','','','',6,1,1,1,'');
  
replace  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values 
('default',0,'carriers/fedex/account','329311708','',0); 
