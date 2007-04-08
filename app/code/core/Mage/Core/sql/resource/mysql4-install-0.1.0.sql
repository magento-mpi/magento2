DROP TABLE IF EXISTS `core_resource`;
CREATE TABLE `core_resource` (
  `resource_name` varchar(50) NOT NULL default '',
  `resource_db_version` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`resource_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert  into `core_resource`(`resource_name`,`resource_db_version`) values 
('auth_setup','0.1.2'),
('cart_setup','0.1.6'),
('catalog_setup','0.1.6'),
('checkout_setup','0.1.1'),
('core_setup','0.1.5'),
('customer_setup','0.1.5'),
('directory_setup','0.1.3'),
('page_setup','0.1.1'),
('sales_setup','0.1.1'),
('shiptable_setup','0.1.2');