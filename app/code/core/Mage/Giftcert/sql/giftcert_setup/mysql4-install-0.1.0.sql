drop table if exists `sales_counter`;
drop table if exists `sales_discount_coupon`;

DROP TABLE IF EXISTS `sales_giftcert`;
DROP TABLE IF EXISTS `giftcert_code`;
CREATE TABLE `giftcert_code` (
  `giftcert_id` int(10) unsigned NOT NULL auto_increment,
  `giftcert_code` varchar(50) NOT NULL default '',
  `balance_amount` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`giftcert_id`),
  UNIQUE KEY `gift_code` (`giftcert_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `sales_giftcert` */

insert into `giftcert_code` (`giftcert_id`,`giftcert_code`,`balance_amount`) values (1,'test',20.0000);