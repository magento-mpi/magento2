
SET NAMES utf8;

SET SQL_MODE='';

SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';

/*Table structure for table `sales_order_entity` */

DROP TABLE IF EXISTS `sales_order_entity`;

CREATE TABLE `sales_order_entity` (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
  `parent_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `is_active` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`entity_id`),
  KEY `FK_CUSTOMER_ENTITY_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_CUSTOMER_ENTITY_STORE` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `sales_order_entity_datetime` */

DROP TABLE IF EXISTS `sales_order_entity_datetime`;

CREATE TABLE `sales_order_entity_datetime` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_DATETIME_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ATTRIBUTE_DATETIME_ATTRIBUTE` (`attribute_id`),
  KEY `FK_ATTRIBUTE_DATETIME_STORE` (`store_id`),
  KEY `FK_ATTRIBUTE_DATETIME_ENTITY` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `sales_order_entity_decimal` */

DROP TABLE IF EXISTS `sales_order_entity_decimal`;

CREATE TABLE `sales_order_entity_decimal` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_DECIMAL_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ATTRIBUTE_DECIMAL_ATTRIBUTE` (`attribute_id`),
  KEY `FK_ATTRIBUTE_DECIMAL_STORE` (`store_id`),
  KEY `FK_ATTRIBUTE_DECIMAL_ENTITY` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `sales_order_entity_int` */

DROP TABLE IF EXISTS `sales_order_entity_int`;

CREATE TABLE `sales_order_entity_int` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_INT_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ATTRIBUTE_INT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_ATTRIBUTE_INT_STORE` (`store_id`),
  KEY `FK_ATTRIBUTE_INT_ENTITY` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `sales_order_entity_text` */

DROP TABLE IF EXISTS `sales_order_entity_text`;

CREATE TABLE `sales_order_entity_text` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_TEXT_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ATTRIBUTE_TEXT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_ATTRIBUTE_TEXT_STORE` (`store_id`),
  KEY `FK_ATTRIBUTE_TEXT_ENTITY` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `sales_order_entity_varchar` */

DROP TABLE IF EXISTS `sales_order_entity_varchar`;

CREATE TABLE `sales_order_entity_varchar` (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_ATTRIBUTE_VARCHAR_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_ATTRIBUTE_VARCHAR_ATTRIBUTE` (`attribute_id`),
  KEY `FK_ATTRIBUTE_VARCHAR_STORE` (`store_id`),
  KEY `FK_ATTRIBUTE_VARCHAR_ENTITY` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- -----------------------------------------------------------------------
-- add entity types
-- -----------------------------------------------------------------------

replace INTO `eav_entity_type` (`entity_type_id`, `entity_name`, `entity_table`, `value_table_prefix`, `entity_id_field`, `is_data_sharing`, `default_attribute_set_id`) VALUES (4, 'order', 'sales/order', '', '', 1, 0);
replace INTO `eav_entity_type` (`entity_type_id`, `entity_name`, `entity_table`, `value_table_prefix`, `entity_id_field`, `is_data_sharing`, `default_attribute_set_id`) VALUES (5, 'order_status', 'sales/order', '', '', 1, 0);
replace INTO `eav_entity_type` (`entity_type_id`, `entity_name`, `entity_table`, `value_table_prefix`, `entity_id_field`, `is_data_sharing`, `default_attribute_set_id`) VALUES (6, 'order_address', 'sales/order', '', '', 1, 0);
replace INTO `eav_entity_type` (`entity_type_id`, `entity_name`, `entity_table`, `value_table_prefix`, `entity_id_field`, `is_data_sharing`, `default_attribute_set_id`) VALUES (7, 'order_item', 'sales/order', '', '', 1, 0);
replace INTO `eav_entity_type` (`entity_type_id`, `entity_name`, `entity_table`, `value_table_prefix`, `entity_id_field`, `is_data_sharing`, `default_attribute_set_id`) VALUES (8, 'order_payment', 'sales/order', '', '', 1, 0);

-- -----------------------------------------------------------------------
-- copy orders and their attributes to entities
-- -----------------------------------------------------------------------


truncate table sales_order_entity;
truncate table sales_order_entity_int;
truncate table sales_order_entity_decimal;
truncate table sales_order_entity_datetime;
truncate table sales_order_entity_varchar;
truncate table sales_order_entity_text;

delete from eav_attribute where entity_type_id in (4,5,6,7,8);
delete from eav_entity_attribute where entity_type_id in (4,5,6,7,8);
delete from eav_attribute_set where entity_type_id in (4,5,6,7,8);

-- --------------------------------------------------

insert into `eav_attribute_set` (`attribute_set_id`,`entity_type_id`,`attribute_set_name`,`sort_order`) values ( NULL,'4','','1');
insert into `eav_attribute_set` (`attribute_set_id`,`entity_type_id`,`attribute_set_name`,`sort_order`) values ( NULL,'5','','1');
insert into `eav_attribute_set` (`attribute_set_id`,`entity_type_id`,`attribute_set_name`,`sort_order`) values ( NULL,'6','','1');
insert into `eav_attribute_set` (`attribute_set_id`,`entity_type_id`,`attribute_set_name`,`sort_order`) values ( NULL,'7','','1');
insert into `eav_attribute_set` (`attribute_set_id`,`entity_type_id`,`attribute_set_name`,`sort_order`) values ( NULL,'8','','1');

-- --------------------------------------------------

INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'grand_total', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'currency_rate', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'weight', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'tax_percent', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'subtotal', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'discount_amount', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'tax_amount', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'shipping_amount', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'giftcert_amount', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'custbalance_amount', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'quote_id', `backend_type` = 'int', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'customer_id', `backend_type` = 'int', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'store_id', `backend_type` = 'int', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'currency_base_id', `backend_type` = 'int', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'shipping_description', `backend_type` = 'text', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'real_order_id', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'remote_ip', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'currency_code', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'coupon_code', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'giftcert_code', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'shipping_method', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'status', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'shipping_address_id', `backend_type` = 'int', `is_required` = 1;
INSERT INTO `eav_attribute` set `entity_type_id` = 4, `attribute_name` = 'billing_address_id', `backend_type` = 'int', `is_required` = 1;

-- --------------------------------------------------

INSERT INTO `eav_attribute` set `entity_type_id` = 6, `attribute_name` = 'region_id', `backend_type` = 'int', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 6, `attribute_name` = 'country_id', `backend_type` = 'int', `is_required` = 1;
INSERT INTO `eav_attribute` set `entity_type_id` = 6, `attribute_name` = 'address_id', `backend_type` = 'int', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 6, `attribute_name` = 'customer_id', `backend_type` = 'int', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 6, `attribute_name` = 'street', `backend_type` = 'text', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 6, `attribute_name` = 'email', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 6, `attribute_name` = 'firstname', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 6, `attribute_name` = 'lastname', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 6, `attribute_name` = 'company', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 6, `attribute_name` = 'city', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 6, `attribute_name` = 'region', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 6, `attribute_name` = 'postcode', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 6, `attribute_name` = 'telephone', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 6, `attribute_name` = 'fax', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 6, `attribute_name` = 'tax_id', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 6, `attribute_name` = 'address_type', `backend_type` = 'varchar', `is_required` = 1;

-- --------------------------------------------------

INSERT INTO `eav_attribute` set `entity_type_id` = 7, `attribute_name` = 'weight', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 7, `attribute_name` = 'qty', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 7, `attribute_name` = 'qty_backordered', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 7, `attribute_name` = 'qty_canceled', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 7, `attribute_name` = 'qty_shipped', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 7, `attribute_name` = 'qty_returned', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 7, `attribute_name` = 'price', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 7, `attribute_name` = 'tier_price', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 7, `attribute_name` = 'cost', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 7, `attribute_name` = 'discount_percent', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 7, `attribute_name` = 'discount_amount', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 7, `attribute_name` = 'tax_percent', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 7, `attribute_name` = 'tax_amount', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 7, `attribute_name` = 'row_total', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 7, `attribute_name` = 'row_weight', `backend_type` = 'decimal', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 7, `attribute_name` = 'product_id', `backend_type` = 'int', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 7, `attribute_name` = 'image', `backend_type` = 'text', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 7, `attribute_name` = 'name', `backend_type` = 'text', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 7, `attribute_name` = 'model', `backend_type` = 'varchar', `is_required` = 0;

-- --------------------------------------------------

INSERT INTO `eav_attribute` set `entity_type_id` = 8, `attribute_name` = 'cc_exp_month', `backend_type` = 'int', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 8, `attribute_name` = 'cc_exp_year', `backend_type` = 'int', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 8, `attribute_name` = 'cc_raw_request', `backend_type` = 'text', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 8, `attribute_name` = 'cc_raw_response', `backend_type` = 'text', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 8, `attribute_name` = 'method', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 8, `attribute_name` = 'po_number', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 8, `attribute_name` = 'cc_type', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 8, `attribute_name` = 'cc_number_enc', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 8, `attribute_name` = 'cc_last4', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 8, `attribute_name` = 'cc_owner', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 8, `attribute_name` = 'cc_trans_id', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 8, `attribute_name` = 'cc_approval', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 8, `attribute_name` = 'cc_avs_status', `backend_type` = 'varchar', `is_required` = 0;
INSERT INTO `eav_attribute` set `entity_type_id` = 8, `attribute_name` = 'cc_cid_status', `backend_type` = 'varchar', `is_required` = 0;

-- --------------------------------------------------

INSERT INTO `eav_attribute` set `entity_type_id` = 5, `attribute_name` = 'status', `backend_type` = 'varchar', `is_required` = 1;
INSERT INTO `eav_attribute` set `entity_type_id` = 5, `attribute_name` = 'comments', `backend_type` = 'text', `is_required` = 0;

-- --------------------------------------------------

insert into eav_entity_attribute (
 entity_type_id, attribute_set_id, attribute_group_id, attribute_id
)
select
 entity_type_id, 1, 1, attribute_id
from eav_attribute
where entity_type_id in (4,5,6,7,8);

-- --------------------------------------------------

-- sales order entity from attributes to entities

insert into sales_order_entity (
 entity_id, entity_type_id, attribute_set_id, parent_id, store_id, created_at, updated_at, is_active
)
select
 vv.order_id, 4, 0, 0, 1, sales_order_attribute_datetime.attribute_value, now(), 1
from sales_order_attribute_varchar vv
left join sales_order_attribute_datetime on (sales_order_attribute_datetime.order_id=vv.order_id
	and sales_order_attribute_datetime.attribute_code='created_at')
where vv.entity_type = 'self'
group by vv.order_id;

insert into sales_order_entity_decimal (
entity_type_id, attribute_id, store_id, entity_id, `value`
)
select
eav_attribute.entity_type_id, eav_attribute.attribute_id, 1, order_id, attribute_value
from sales_order_attribute_decimal vv
left join eav_attribute on (eav_attribute.attribute_name = vv.attribute_code and eav_attribute.entity_type_id = 4)
where attribute_code in (
'grand_total', 'currency_rate','weight','tax_percent','subtotal',
'discount_amount','tax_amount','shipping_amount','giftcert_amount','custbalance_amount'
);

insert into sales_order_entity_int (
entity_type_id, attribute_id, store_id, entity_id, `value`
)
select
eav_attribute.entity_type_id, eav_attribute.attribute_id, 1, order_id, attribute_value
from sales_order_attribute_int vv
left join eav_attribute on (eav_attribute.attribute_name = vv.attribute_code and eav_attribute.entity_type_id = 4)
where attribute_code in (
'quote_id','customer_id','store_id','currency_base_id'
);

insert into sales_order_entity_text (
entity_type_id, attribute_id, store_id, entity_id, `value`
)
select
eav_attribute.entity_type_id, eav_attribute.attribute_id, 1, order_id, attribute_value
from sales_order_attribute_text vv
left join eav_attribute on (eav_attribute.attribute_name = vv.attribute_code and eav_attribute.entity_type_id = 4)
where attribute_code in (
'shipping_description'
);

insert into sales_order_entity_varchar (
entity_type_id, attribute_id, store_id, entity_id, `value`
)
select
eav_attribute.entity_type_id, eav_attribute.attribute_id, 1, order_id, attribute_value
from sales_order_attribute_varchar vv
left join eav_attribute on (eav_attribute.attribute_name = vv.attribute_code and eav_attribute.entity_type_id = 4)
where attribute_code in (
'real_order_id','remote_ip','currency_code','coupon_code','giftcert_code','shipping_method', 'status'
);

-- sales order address from attributes to entities

insert into sales_order_entity (
 entity_type_id, attribute_set_id, parent_id, store_id, created_at, updated_at, is_active
)
select 6 , 0, vv.order_id, 1, sales_order_attribute_datetime.attribute_value, now(), 1
from sales_order_attribute_int vv
left join sales_order_attribute_datetime on (sales_order_attribute_datetime.order_id=vv.order_id
and sales_order_attribute_datetime.attribute_code='created_at')
where vv.attribute_code = 'address_id'
group by vv.order_id;

insert into sales_order_entity_int (
entity_type_id, attribute_id, store_id, entity_id, `value`
)
select
eav_attribute.entity_type_id, eav_attribute.attribute_id, 1, sales_order_entity.entity_id, attribute_value
from sales_order_attribute_int vv
left join sales_order_entity on (sales_order_entity.parent_id=order_id and sales_order_entity.entity_type_id = 6)
left join eav_attribute on (eav_attribute.attribute_name = vv.attribute_code and eav_attribute.entity_type_id = 6)
where attribute_code in (
'region_id','country_id','address_id','customer_id'
);

insert into sales_order_entity_text (
entity_type_id, attribute_id, store_id, entity_id, `value`
)
select
eav_attribute.entity_type_id, eav_attribute.attribute_id, 1, sales_order_entity.entity_id, attribute_value
from sales_order_attribute_text vv
left join sales_order_entity on (sales_order_entity.parent_id=order_id and sales_order_entity.entity_type_id = 6)
left join eav_attribute on (eav_attribute.attribute_name = vv.attribute_code and eav_attribute.entity_type_id = 6)
where attribute_code in (
'street'
);

insert into sales_order_entity_varchar (
entity_type_id, attribute_id, store_id, entity_id, `value`
)
select distinctrow
eav_attribute.entity_type_id, eav_attribute.attribute_id, 1, sales_order_entity.entity_id, attribute_value
from sales_order_attribute_varchar vv
left join sales_order_entity on (sales_order_entity.parent_id=order_id and sales_order_entity.entity_type_id = 6)
left join eav_attribute on (eav_attribute.attribute_name = vv.attribute_code and eav_attribute.entity_type_id = 6)
where attribute_code in (
'email','firstname','lastname','company','city','region','postcode','telephone','fax','tax_id','address_type'
);

-- sales order items from attributes to entities

insert into sales_order_entity (
 entity_type_id, attribute_set_id, parent_id, store_id, created_at, updated_at, is_active
)
select
 7, 0, vv.order_id, 1, sales_order_attribute_datetime.attribute_value, now(), 1
from sales_order_attribute_varchar vv
left join sales_order_attribute_datetime on (sales_order_attribute_datetime.order_id=vv.order_id
	and sales_order_attribute_datetime.attribute_code='created_at')
where vv.entity_type = 'item'
group by vv.order_id;

insert into sales_order_entity_decimal (
entity_type_id, attribute_id, store_id, entity_id, `value`
)
select
eav_attribute.entity_type_id, eav_attribute.attribute_id, 1, sales_order_entity.entity_id, attribute_value
from sales_order_attribute_decimal vv
left join sales_order_entity on (sales_order_entity.parent_id=order_id and sales_order_entity.entity_type_id = 7)
left join eav_attribute on (eav_attribute.attribute_name = vv.attribute_code and eav_attribute.entity_type_id = 7)
where attribute_code in (
'weight','qty','qty_backordered','qty_canceled','qty_shipped','qty_returned',
'price','tier_price','cost','discount_percent','discount_amount','tax_percent',
'tax_amount','row_total','row_weight'
);

insert into sales_order_entity_int (
entity_type_id, attribute_id, store_id, entity_id, `value`
)
select
eav_attribute.entity_type_id, eav_attribute.attribute_id, 1, sales_order_entity.entity_id, attribute_value
from sales_order_attribute_int vv
left join sales_order_entity on (sales_order_entity.parent_id=order_id and sales_order_entity.entity_type_id = 7)
left join eav_attribute on (eav_attribute.attribute_name = vv.attribute_code and eav_attribute.entity_type_id = 7)
where attribute_code in (
'product_id'
);

insert into sales_order_entity_text (
entity_type_id, attribute_id, store_id, entity_id, `value`
)
select
eav_attribute.entity_type_id, eav_attribute.attribute_id, 1, sales_order_entity.entity_id, attribute_value
from sales_order_attribute_text vv
left join sales_order_entity on (sales_order_entity.parent_id=order_id and sales_order_entity.entity_type_id = 7)
left join eav_attribute on (eav_attribute.attribute_name = vv.attribute_code and eav_attribute.entity_type_id = 7)
where attribute_code in (
'image','name'
);

insert into sales_order_entity_varchar (
entity_type_id, attribute_id, store_id, entity_id, `value`
)
select
eav_attribute.entity_type_id, eav_attribute.attribute_id, 1, sales_order_entity.entity_id, attribute_value
from sales_order_attribute_varchar vv
left join sales_order_entity on (sales_order_entity.parent_id=order_id and sales_order_entity.entity_type_id = 7)
left join eav_attribute on (eav_attribute.attribute_name = vv.attribute_code and eav_attribute.entity_type_id = 7)
where attribute_code in (
'model'
);

-- sales order payment from attributes to entities

insert into sales_order_entity (
 entity_type_id, attribute_set_id, parent_id, store_id, created_at, updated_at, is_active
)
select
 8, 0, vv.order_id, 1, sales_order_attribute_datetime.attribute_value, now(), 1
from sales_order_attribute_int vv
left join sales_order_attribute_datetime on (sales_order_attribute_datetime.order_id=vv.order_id
	and sales_order_attribute_datetime.attribute_code='created_at')
where vv.entity_type = 'payment'
group by vv.order_id;

insert into sales_order_entity_int (
entity_type_id, attribute_id, store_id, entity_id, `value`
)
select
eav_attribute.entity_type_id, eav_attribute.attribute_id, 1, sales_order_entity.entity_id, attribute_value
from sales_order_attribute_int vv
left join sales_order_entity on (sales_order_entity.parent_id=order_id and sales_order_entity.entity_type_id = 8)
left join eav_attribute on (eav_attribute.attribute_name = vv.attribute_code and eav_attribute.entity_type_id = 8)
where attribute_code in (
'cc_exp_month','cc_exp_year'
);

insert into sales_order_entity_text (
entity_type_id, attribute_id, store_id, entity_id, `value`
)
select
eav_attribute.entity_type_id, eav_attribute.attribute_id, 1, sales_order_entity.entity_id, attribute_value
from sales_order_attribute_text vv
left join sales_order_entity on (sales_order_entity.parent_id=order_id and sales_order_entity.entity_type_id = 8)
left join eav_attribute on (eav_attribute.attribute_name = vv.attribute_code and eav_attribute.entity_type_id = 8)
where attribute_code in (
'cc_raw_request','cc_raw_response'
);

insert into sales_order_entity_varchar (
entity_type_id, attribute_id, store_id, entity_id, `value`
)
select
eav_attribute.entity_type_id, eav_attribute.attribute_id, 1, sales_order_entity.entity_id, attribute_value
from sales_order_attribute_varchar vv
left join sales_order_entity on (sales_order_entity.parent_id=order_id and sales_order_entity.entity_type_id = 8)
left join eav_attribute on (eav_attribute.attribute_name = vv.attribute_code and eav_attribute.entity_type_id = 8)
where attribute_code in (
'method','po_number','cc_type','cc_number_enc','cc_last4','cc_owner','cc_trans_id','cc_approval','cc_avs_status','cc_cid_status'
);

-- sales order status from attributes to entities

insert into sales_order_entity (
 entity_type_id, attribute_set_id, parent_id, store_id, created_at, updated_at, is_active
)
select
 5, 0, vv.order_id, 1, sales_order_attribute_datetime.attribute_value, now(), 1
from sales_order_attribute_varchar vv
left join sales_order_attribute_datetime on (sales_order_attribute_datetime.order_id=vv.order_id
	and sales_order_attribute_datetime.attribute_code='created_at')
where vv.attribute_code = 'status'
group by vv.order_id;

insert into sales_order_entity_text (
entity_type_id, attribute_id, store_id, entity_id, `value`
)
select
eav_attribute.entity_type_id, eav_attribute.attribute_id, 1, sales_order_entity.entity_id, attribute_value
from sales_order_attribute_text vv
left join sales_order_entity on (sales_order_entity.parent_id=order_id and sales_order_entity.entity_type_id = 5)
left join eav_attribute on (eav_attribute.attribute_name = vv.attribute_code and eav_attribute.entity_type_id = 5)
where attribute_code in (
'comments'
);

insert into sales_order_entity_varchar (
entity_type_id, attribute_id, store_id, entity_id, `value`
)
select
eav_attribute.entity_type_id, eav_attribute.attribute_id, 1, sales_order_entity.entity_id, attribute_value
from sales_order_attribute_varchar vv
left join sales_order_entity on (sales_order_entity.parent_id=order_id and sales_order_entity.entity_type_id = 5)
left join eav_attribute on (eav_attribute.attribute_name = vv.attribute_code and eav_attribute.entity_type_id = 5)
where attribute_code in (
'status'
);

-- --------------------------------------------------

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
