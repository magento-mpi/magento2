insert  into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`frontend_class`,`frontend_model`,`backend_model`,`source_model`,`sort_order`,`show_in_default`,`show_in_website`,`show_in_store`,`module_name`) values 
(1,'shipping','Shipping','text','','','','',50,1,1,1,''),
(2,'shipping/origin','Origin','text','','','','',1,1,1,1,''),
(3,'shipping/origin/country_id','Country','select','countries','','','adminhtml/system_config_source_country',1,1,1,1,''),
(3,'shipping/origin/region_id','Region/State','text','','','','',2,1,1,1,''),
(3,'shipping/origin/postcode','ZIP/Postal Code','text','','','','',3,1,1,1,''),
(2,'carriers/tablerate','Table rates','text','','','','',2,1,1,1,''),
(3,'carriers/tablerate/active','Enabled','select','','','','adminhtml/system_config_source_yesno',1,1,1,1,''),
(1,'carriers','Shipping Carriers','text','','','','',51,1,1,1,''),
(2,'shipping/option','Options','text','','','','',2,1,1,1,''),
(3,'shipping/option/checkout_multiple','Allow Shipping to multiple addresses','select','','','','adminhtml/system_config_source_yesno',1,1,1,1,''),
(3,'carriers/tablerate/title','Title','text','','','','',2,1,1,1,''),
(2,'carriers/pickup','Pick Up','text','','','','',3,0,0,0,''),
(3,'carriers/pickup/active','Enabled','select','','','','adminhtml/system_config_source_yesno',1,1,1,1,''),
(3,'carriers/pickup/title','Title','text','','','','',2,1,1,1,''),
(3,'carriers/tablerate/sort_order','Sort order','text',100),
(3,'carriers/pickup/sort_order','Sort order','text',100),
(2,'carriers/freeshipping','Free Shipping','text','','','','',2,1,1,1,''),
(3,'carriers/freeshipping/active','Enabled','select','','','','adminhtml/system_config_source_yesno',1,1,1,1,''),
(3,'carriers/freeshipping/sort_order','Sorting order','text','','','','',100,1,1,1,''),
(3,'carriers/freeshipping/title','Title','text','','','','',2,1,1,1,''),
(3,'carriers/freeshipping/name','Method name','text','','','','',3,1,1,1,''),
(3,'carriers/freeshipping/cutoff_cost','Cutoff cost','text','','','','',4,1,1,1,''),
(2,'carriers/flatrate','Flat Rate','text','','','','',2,1,1,1,''),
(3,'carriers/flatrate/active','Enabled','select','','','','adminhtml/system_config_source_yesno',1,1,1,1,''),
(3,'carriers/flatrate/sort_order','Sorting order','text','','','','',100,1,1,1,''),
(3,'carriers/flatrate/title','Title','text','','','','',2,1,1,1,''),
(3,'carriers/flatrate/name','Method name','text','','','','',3,1,1,1,''),
(3,'carriers/flatrate/type','Type','select','','','','adminhtml/system_config_source_shipping_flatrate',4,1,1,1,''),
(3,'carriers/flatrate/price','Price','text','','','','',5,1,1,1,''),
(3,'carriers/tablerate/name','Method name','text','','','','',3,1,1,1,''),
(3,'carriers/tablerate/condition_name','Condition','select','','','','adminhtml/system_config_source_shipping_tablerate',4,1,1,1,''),
(3,'carriers/tablerate/export','Export','export','','','','',5,0,1,0,''),
(3,'carriers/tablerate/import','Import','import','','','adminhtml/system_config_backend_shipping_tablerate','',6,0,1,0,''),
(3,'carriers/freeshipping/cutoff_cost','Minimum order amount','text','','','','',4,1,1,1,''),
(3,'carriers/tablerate/condition_name','Condition','select','','','','adminhtml/system_config_source_shipping_tablerate',4,1,1,0,'')
ON DUPLICATE KEY UPDATE field_id=field_id;
 
UPDATE `core_config_field` SET `show_in_default` = '0', `show_in_website` = '0', `show_in_store` = '0' WHERE `path` = 'carriers/pickup';
UPDATE `core_config_field` SET `frontend_class` = 'countries' WHERE `path` ='shipping/origin/country_id';
UPDATE `core_config_field` SET `frontend_label` = 'ZIP/Postal Code' WHERE `path` ='shipping/origin/postcode';
UPDATE `core_config_field` SET `frontend_label` = 'Region/State' WHERE `path` ='shipping/origin/region_id';

--

insert  into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values 
('default',0,'carriers/tablerate/active','0','',0),
('default',0,'carriers/tablerate/title','','',0),
('default',0,'carriers/pickup/active','0','',0),
('default',0,'carriers/pickup/title','','',0),
('default',0,'shipping/origin/country_id','223','',0),
('default',0,'shipping/origin/region_id','1','',0),
('default',0,'shipping/origin/postcode','90034','',0),
('default',0,'shipping/option/checkout_multiple','0','',0),
('default',0,'carriers/freeshipping/active','1','0',0),
('default',0,'carriers/flatrate/active','1','0',0),
('default',0,'carriers/freeshipping/name','Free','0',0),
('default',0,'carriers/flatrate/name','Fixed','0',0),
('default',0,'carriers/tablerate/name','Best Way','0',0),
('default',0,'carriers/freeshipping/title','Free Shipping','0',0),
('default',0,'carriers/flatrate/title','Flat Rate','0',0),
('default',0,'carriers/freeshipping/cutoff_cost','50','0',0),
('default',0,'carriers/flatrate/type','I','0',0),
('default',0,'carriers/flatrate/price','5.00','0',0),
('default',0,'carriers/tablerate/condition_name','package_weight','',0),
('default',0,'carriers/pickup/model','shipping/carrier_pickup','',0),
('default',0,'carriers/freeshipping/model','shipping/carrier_freeshipping','',0),
('default',0,'carriers/flatrate/model','shipping/carrier_flatrate','',0),
('default',0,'carriers/tablerate/model','shipping/carrier_tablerate','',0)
ON DUPLICATE KEY UPDATE config_id=config_id;

UPDATE `core_config_data` SET `value` = '0', `inherit` = '1' WHERE `path` = 'carriers/pickup/active';
UPDATE `core_config_data` SET `inherit` = '0' WHERE `scope` = 'default' AND `path` = 'carriers/pickup/active';

