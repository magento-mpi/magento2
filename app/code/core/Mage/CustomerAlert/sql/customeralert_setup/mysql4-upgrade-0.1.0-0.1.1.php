<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
$this->run("

DROP TABLE IF EXISTS `customer_product_alert_check`;

CREATE TABLE `customer_product_alert_check` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(11) unsigned NOT NULL default '0',
  `store_id` int(11) unsigned NOT NULL default '0',
  `type` varchar(255) NOT NULL default '',
  `old_value` varchar(255) NOT NULL default '',
  `new_value` varchar(255) NOT NULL default '',
  `date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `customer_product_alert_queue`;

CREATE TABLE `customer_product_alert_queue` (
  `queue_id` int(7) unsigned NOT NULL auto_increment,
  `template_id` int(7) unsigned NOT NULL default '0',
  `queue_status` int(3) unsigned NOT NULL default '0',
  `queue_start_at` datetime default NULL,
  `queue_finish_at` datetime default NULL,
  PRIMARY KEY  (`queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `customer_product_alert_subscribers`;

CREATE TABLE `customer_product_alert_subscribers` (
  `subscriber_id` int(7) unsigned NOT NULL auto_increment,
  `store_id` int(3) unsigned default '0',
  `change_status_at` datetime default NULL,
  `customer_id` int(11) unsigned NOT NULL default '0',
  `subscriber_email` varchar(150) character set latin1 collate latin1_general_ci NOT NULL default '',
  `subscriber_status` int(3) NOT NULL default '0',
  `subscriber_confirm_code` varchar(32) default 'NULL',
  PRIMARY KEY  (`subscriber_id`),
  KEY `FK_SUBSCRIBER_STORE` (`store_id`),
  KEY `FK_SUBSCRIBER_CUSTOMER` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Customer product alert subscribers';

DROP TABLE IF EXISTS `customer_product_alert_queue_link`;

CREATE TABLE `customer_product_alert_queue_link` (
  `queue_link_id` int(9) unsigned NOT NULL auto_increment,
  `queue_id` int(7) unsigned NOT NULL default '0',
  `subscriber_id` int(7) unsigned NOT NULL default '0',
  `letter_sent_at` datetime default NULL,
  PRIMARY KEY  (`queue_link_id`),
  KEY `FK_QUEUE_LINK_SUBSCRIBER` (`subscriber_id`),
  KEY `FK_QUEUE_LINK_QUEUE` (`queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Customer product alert queue to subscriber link';


");

?>