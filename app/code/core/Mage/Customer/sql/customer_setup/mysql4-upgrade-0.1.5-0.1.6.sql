alter table `magenta`.`customer_address` ,add column `region` varchar (128)  NOT NULL  after `city`;
alter table `magenta`.`customer_address_type_link` drop `address_type_link_id`;

truncate table `customer_address_type`;
insert into `customer_address_type` (`address_type_id`,`address_type_code`) values (1,'billing'),(2,'shipping'),(3,'service');

truncate table `customer_address_type_language`;
insert  into `customer_address_type_language` (`address_type_id`,`language_code`,`address_type_name`) values (1,'en','Billing'),(2,'en','Shipping'),(3,'en','Service');

