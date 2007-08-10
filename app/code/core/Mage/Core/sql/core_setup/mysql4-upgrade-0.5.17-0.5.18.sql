update `core_config_field` set `frontend_label`='Payment Methods' where `path`='payment';

replace into `core_config_field` (`level`,`path`,`frontend_label`,`frontend_type`,`sort_order`) values
('3','payment/authorizenet/title','Title','text',1),
('3','payment/paypal/title','Title','text',1),
('3','payment/verisign/title','Title','text',1),
('3','payment/checkmo/title','Title','text',1),
('3','payment/ccsave/title','Title','text',1),
('3','payment/purchaseorder/title','Title','text',1);

replace into `core_config_data` (`scope`, `scope_id`, `path`, `value`) values
('default', 0, 'payment/ccsave/title', 'Credit Card'),
('default', 0, 'payment/checkmo/title', 'Credit Card'),
('default', 0, 'payment/purchaseorder/title', 'Credit Card'),
('default', 0, 'payment/authorizenet/title', 'Credit Card (Authorize.net)'),
('default', 0, 'payment/paypal/title', 'Credit Card (Paypal)'),
('default', 0, 'payment/verisign/title', 'Credit Card (Verisign)');

replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`sort_order`) values
('3','payment/authorizenet/sort_order','Sort order','text',100),
('3','payment/paypal/sort_order','Sort order','text',100),
('3','payment/verisign/sort_order','Sort order','text',100),
('3','payment/checkmo/sort_order','Sort order','text',100),
('3','payment/ccsave/sort_order','Sort order','text',100),
('3','payment/purchaseorder/sort_order','Sort order','text',100);


replace into `core_config_field`(`level`,`path`,`frontend_label`,`frontend_type`,`sort_order`) values
('3','carriers/tablerate/sort_order','Sort order','text',100),
('3','carriers/pickup/sort_order','Sort order','text',100),
('3','carriers/ups/sort_order','Sort order','text',100),
('3','carriers/usps/sort_order','Sort order','text',100),
('3','carriers/fedex/sort_order','Sort order','text',100),
('3','carriers/dhl/sort_order','Sort order','text',100);
