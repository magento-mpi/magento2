replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values 
(3,'carriers/dhl/free_method','Free shipping method','select','','','','usa/shipping_carrier_dhl_source_service',20,1,1,1,''),
(3,'carriers/dhl/cutoff_cost','Minimum order amount for free shipping','text','','','','',21,1,1,1,''),
(3,'carriers/fedex/free_method','Free shipping method','select','','','','usa/shipping_carrier_fedex_source_method',20,1,1,1,''),
(3,'carriers/fedex/cutoff_cost','Minimum order amount for free shipping','text','','','','',21,1,1,1,''),
(3,'carriers/ups/free_method','Free shipping method','select','','','','usa/shipping_carrier_ups_source_method',20,1,1,1,''),
(3,'carriers/ups/cutoff_cost','Minimum order amount for free shipping','text','','','','',21,1,1,1,''),
(3,'carriers/usps/free_method','Free shipping method','select','','','','usa/shipping_carrier_usps_source_service',20,1,1,1,''),
(3,'carriers/usps/cutoff_cost','Minimum order amount for free shipping','text','','','','',21,1,1,1,''),
(3,'carriers/freeshipping/cutoff_cost','Minimum order amount','text','','','','',4,1,1,1,''),
(3,'carriers/tablerate/condition_name','Condition','select','','','','adminhtml/system_config_source_shipping_tablerate',4,1,1,0,'');
 