replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values 
(3,'carriers/dhl/free_method','Free method','select','','','','usa/shipping_carrier_dhl_source_service',20,1,1,1,''),
(3,'carriers/dhl/cutoff_cost','Cutoff cost','text','','','','',21,1,1,1,''),
(3,'carriers/fedex/free_method','Free method','select','','','','usa/shipping_carrier_fedex_source_method',20,1,1,1,''),
(3,'carriers/fedex/cutoff_cost','Cutoff cost','text','','','','',21,1,1,1,''),
(3,'carriers/ups/free_method','Free method','select','','','','usa/shipping_carrier_ups_source_method',20,1,1,1,''),
(3,'carriers/ups/cutoff_cost','Cutoff cost','text','','','','',21,1,1,1,''),
(3,'carriers/usps/free_method','Free method','select','','','','usa/shipping_carrier_usps_source_service',20,1,1,1,''),
(3,'carriers/usps/cutoff_cost','Cutoff cost','text','','','','',21,1,1,1,'');

replace  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values 
('default',0,'carriers/dhl/free_method','G','',0),
('default',0,'carriers/dhl/cutoff_cost','','',0),
('default',0,'carriers/fedex/free_method','FEDEXGROUND','',0),
('default',0,'carriers/fedex/cutoff_cost','','',0),
('default',0,'carriers/ups/free_method','GND','',0),
('default',0,'carriers/ups/cutoff_cost','','',0),
('default',0,'carriers/usps/free_method','PARCEL','',0),
('default',0,'carriers/usps/cutoff_cost','','',0);

