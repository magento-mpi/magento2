replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values 
(3,'carriers/dhl/free_method','Free method','select','free-method','','','usa/shipping_carrier_dhl_source_freemethod',20,1,1,1,''),
(3,'carriers/fedex/free_method','Free method','select','free-method','','','usa/shipping_carrier_fedex_source_freemethod',20,1,1,1,''),
(3,'carriers/ups/free_method','Free method','select','free-method','','','usa/shipping_carrier_ups_source_freemethod',20,1,1,1,''),
(3,'carriers/usps/free_method','Free method','select','free-method','','','usa/shipping_carrier_usps_source_freemethod',20,1,1,1,''),
(3,'carriers/dhl/allowed_methods','Allowed methods','multiselect','','','','usa/shipping_carrier_dhl_source_method',17,1,1,1,''),
(3,'carriers/fedex/allowed_methods','Allowed methods','multiselect','','','','usa/shipping_carrier_fedex_source_method',17,1,1,1,''),
(3,'carriers/ups/allowed_methods','Allowed methods','multiselect','','','','usa/shipping_carrier_ups_source_method',17,1,1,1,''),
(3,'carriers/usps/allowed_methods','Allowed methods','multiselect','','','','usa/shipping_carrier_usps_source_method',17,1,1,1,'');

replace  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values 
('default',0,'carriers/dhl/allowed_methods','E,N,S,G','',0),
('default',0,'carriers/fedex/allowed_methods','PRIORITYOVERNIGHT,STANDARDOVERNIGHT,FIRSTOVERNIGHT,FEDEX2DAY,FEDEXEXPRESSSAVER,INTERNATIONALPRIORITY,INTERNATIONALECONOMY,INTERNATIONALFIRST,FEDEX1DAYFREIGHT,FEDEX2DAYFREIGHT,FEDEX3DAYFREIGHT,FEDEXGROUND,GROUNDHOMEDELIVERY,INTERNATIONALPRIORITY FREIGHT,INTERNATIONALECONOMY FREIGHT,EUROPEFIRSTINTERNATIONALPRIORITY','',0),
('default',0,'carriers/ups/allowed_methods','1DM,1DML,1DA,1DAL,1DAPI,1DP,1DPL,2DM,2DML,2DA,2DAL,3DS,GND,GNDCOM,GNDRES,STD,XPR,WXS,XPRL,XDM,XDML,XPD','',0),
('default',0,'carriers/usps/allowed_methods','FIRST CLASS,PRIORITY,EXPRESS,BPM,PARCEL,MEDIA,LIBRARY','',0);

