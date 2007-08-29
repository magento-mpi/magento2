insert into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values 
('default',0,'carriers/usps/methods','First-Class,Express Mail,Express Mail PO to PO,Priority Mail,Parcel Post,Express Mail Flat-Rate Envelope,Priority Mail Flat-Rate Box,Bound Printed Matter,Media Mail,Library Mail,Priority Mail Flat-Rate Envelope,Global Express Guaranteed,Global Express Guaranteed Non-Document Rectangular,Global Express Guaranteed Non-Document Non-Rectangular,Express Mail International (EMS),Express Mail International (EMS) Flat Rate Envelope,Priority Mail International,Priority Mail International Flat Rate Box','',0)
ON DUPLICATE KEY UPDATE config_id=config_id;

delete from `core_config_data` where `scope` != 'default' and `path` = 'carriers/usps/allowed_methods';

replace into `core_config_data`(`scope`,`scope_id`,`path`,`value`,`old_value`,`inherit`) values 
('default',0,'carriers/usps/allowed_methods','First-Class,Express Mail,Express Mail PO to PO,Priority Mail,Parcel Post,Express Mail Flat-Rate Envelope,Priority Mail Flat-Rate Box,Bound Printed Matter,Media Mail,Library Mail,Priority Mail Flat-Rate Envelope,Global Express Guaranteed,Global Express Guaranteed Non-Document Rectangular,Global Express Guaranteed Non-Document Non-Rectangular,Express Mail International (EMS),Express Mail International (EMS) Flat Rate Envelope,Priority Mail International,Priority Mail International Flat Rate Box','',0);

