alter table `magenta`.`customer_address` ,add column `region` varchar (128)  NOT NULL  after `city`;
alter table `magenta`.`customer_address_type_link` drop `address_type_link_id`;